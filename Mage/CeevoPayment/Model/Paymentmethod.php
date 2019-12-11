<?php
//require_once(__DIR__ . '/vendor/autoload.php');

//use Omnipay\Common\CreditCard;
//use Omnipay\Omnipay;
 
/**
* Our test CC module adapter
*/
class Mage_CeevoPayment_Model_Paymentmethod extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'ceevopayment';
    protected $_formBlockType = 'CeevoPayment/form_cc';
    protected $_infoBlockType = 'CeevoPayment/info_cc';
    protected $_isInitializeNeeded      = true;
    protected  $access_token = ''; 
    
    public function assignData($data)
    {   
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }

        $info = $this->getInfoInstance();

        $info->setCheckNo($data->getCheckNo())
            ->setCheckDate($data->getCheckDate());
        return $this;
    }

    public function validate()
    {
        parent::validate();
        $info = $this->getInfoInstance();
        $api_key = $this->getConfigData('api_key');
        if(array_key_exists('payment_method', $_POST) && array_key_exists('token_hidden_input', $_POST) && array_key_exists('session_hidden_input', $_POST)){
            $info->setAdditionalInformation('payment_method', $_POST['payment_method']);
            $info->setAdditionalInformation('token_hidden_input', $_POST['token_hidden_input']);
            $info->setAdditionalInformation('session_hidden_input', $_POST['session_hidden_input']);
        }
        $_SESSION['3durl'] = '';
        $_SESSION['transID'] = '';
        
        $this->getToken();      
        return $this;
    }
   
    public function initialize($paymentAction, $stateObject)
    {     
        $orderState = Mage_Sales_Model_Order::STATE_PROCESSING;
       
        $payment = $this->getInfoInstance();
        $order = $payment->getOrder();
        $amount = $order->getTotalDue();
        $transID = $this->createCustomer($payment);
          
        if(!empty($transID)){

            $banktransactionid = $transID; 
            
            $payment->setTransactionId($banktransactionid);
            $payment->setParentTransactionId($banktransactionid);
            $payment->setIsTransactionClosed(false);
            $payment->setTransactionAdditionalInfo($_POST['method_code']);
            $payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);
            if(empty($_SESSION['3durl'])){
                            
                $message = "Payment completed successfully with Transaction Id -".$transID; 
                $orderState = Mage_Sales_Model_Order::STATE_PROCESSING;
                $order->setState($orderState, "pending", $message, false);

                //$order->setStatus("complete");       
                $order->save();
                $order->sendNewOrderEmail();
        
                Mage::getModel('sales/quote')
                        ->load($order->getQuoteId())
                        ->setIsActive(false)
                        ->save();                  
            }else{

                $order->save();
            }    
        }else{
            $errorMsg = $this->_getHelper()->__('Error in processing payment.');
            Mage::throwException(
                $errorMsg
            );
        }        
    }

    function createCustomer($payment)
    {     
        $order = $payment->getOrder();
        $billing = $order->getBillingAddress();
    
        $data = array("billing_address" => array("city" => $billing->getCity(), "country" => $billing->getCountry(),"state" => $billing->getRegion(),"street" => $billing->getStreet1(),"zip_or_postal"=> $billing->getPostcode()),"email" => $order->getCustomerEmail(),"first_name" => $billing->getFirstname(),"last_name" => $billing->getLastname(),"mobile" => $billing->getTelephone(),"phone" => $billing->getTelephone());  
        $data_string = json_encode($data);

        $customer_id = $this->callAPI('POST', 'https://api.ceevo.com/payment/customer', $data_string);
        $this->registerAccountToken($customer_id, $order);
        $chargeResponse = $this->chargeApi($payment, $customer_id);
        return $chargeResponse;  
    }
    
    function registerAccountToken($customer_registered_id,$order){
    
        $token_array = array("account_token" => $_POST['token_hidden_input'],"is_default" => true,"verify" => true);
        $token_string = json_encode($token_array);
        $get_data = $this->callAPI('POST', 'https://api.ceevo.com/payment/customer/'.$customer_registered_id, $token_string);
        $response = json_decode($get_data, true);
    }

    function getToken()
    {
        $api = "https://auth.ceevo.com/auth/realms/ceevo-realm/protocol/openid-connect/token"; 
        $param['grant_type'] = "client_credentials"; 
        $param['client_id'] = $this->getConfigData('client_id'); 
        $param['client_secret'] = $this->getConfigData('client_secret'); 
        $flag = $this->getConfigData('secureflag');
        
        $mode = $this->getConfigData('transaction_mode');
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,$api); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
        //curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
        $res = curl_exec($ch);     
        $jres = json_decode($res, true);
        $access_token = $jres['access_token'];
        $this->access_token  = $access_token;    
    } 
   
    function chargeApi($payment, $cusId)
    {
        $order = $payment->getOrder();
        $billing = $order->getBillingAddress();       
        $ord =  $order->getId();
        $orde = Mage::getModel('sales/order')->load($ord);
        $orderId =  $orde->getIncrementId();
        $apiKey =  $this->getConfigData('api_key');
        $mode = $this->getConfigData('transaction_mode');
        $secure = $this->getConfigData('secureflag');
        $capture = 'false';

        if($this->getConfigData('transaction_type') == 'SALES')
          {
            $capture = 'true';
          }
        $items_array = [];
        foreach($order->getAllVisibleItems() as $item){
          
          $item_json = array("item" => $item->getName(),"itemValue" =>(string) $item->getPrice());
          array_push($items_array, json_encode($item_json));
        }
        $itemString = implode(',',$items_array);
    
        $jres = json_decode($res, true);
        $access_token = $this->access_token; 
        $authorization = "Authorization: Bearer $access_token";
        $charge_api = "https://api.ceevo.com/payment/charge";    
        $successURL = Mage::getUrl('ceevopayment/payment/success', array('_secure' => false));
        $failURL = Mage::getUrl('ceevopayment/payment/failure', array('_secure' => false));      

        $totalAmount = $order->getGrandTotal();
        $amount =  round($totalAmount,2);

        $cparam = '{"amount": '.( $amount*100 ).',
                "capture": "'.$capture.'",
                "3dsecure": "'.$secure.'",
                "mode" : "'.$mode.'",
                "method_code":  "'.$_POST['method_code'].'",
                "currency": "'.$order->getOrderCurrencyCode().'",
                "customer_id": "'.$cusId.'", 
                "account_token": "'.$_POST['token_hidden_input'].'",
                "session_id": "'.$_POST['session_hidden_input'].'",
                "redirect_urls": {
                    "failure_url": "'.$failURL.'",
                    "success_url": "'.$successURL.'"
                },
                "reference_id": "'.$orderId.'",
                "shipping_address": {
                    "city": "'.$billing->getCity().'",
                    "country": "'.$billing->getCountry().'",
                    "state": "'.$billing->getRegion().'",
                    "street": "'.$billing->getStreet1().'",
                    "zip_or_postal": "'.$billing->getPostcode().'"
                },
                "user_email": "'.$order->getCustomerEmail().'"}';
            
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,$charge_api); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $cparam);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($cparam),
                $authorization
            )
        );
        $cres = curl_exec($ch); 
        $httpcode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $locationUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($cres, 0, $header_size);
        $body = substr($cres, $header_size); 
        $jbody = json_decode($body, true);

        curl_close($ch);
        $transactionHeaders = $this->http_parse_headers($headers);

        if($httpcode  == '301' || $httpcode  == '302')
        {
            $_SESSION['3durl'] = $locationUrl;           
        }

        $transactionId  = $jbody['payment_id'];
        $_SESSION['ceevo_hash_Key'] = $jbody['message'];
        return $transactionId;
    }

    function callAPI($method, $url, $data)
    {
        $apiKey =  $this->getConfigData('api_key');
        $access_token = $this->access_token;
        $authorization = "Authorization: Bearer $access_token";
        $curl = curl_init();
   
        switch ($method){
           case "POST":
              curl_setopt($curl, CURLOPT_POST, 1);
              if ($data)
                 curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
              break;
           case "PUT":
              curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
              if ($data)
                 curl_setopt($curl, CURLOPT_POSTFIELDS, $data);                                 
              break;
           default:
              if ($data)
                 $url = sprintf("%s?%s", $url, http_build_query($data));
        }
   
        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
   
           'Content-Type: application/json',
            'Content-Length: ' . strlen($data),
            $authorization
            //'X-CV-APIKey: 553fbbcd-f488-4e97-bf90-ad418a781e62'
            
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        // EXECUTE:
        $response = curl_exec($curl);

        // Retudn headers seperatly from the Response Body
        $httpcode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $locationUrl = curl_getinfo($curl, CURLINFO_REDIRECT_URL);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        curl_close($curl);
        header("Content-Type:text/plain; charset=UTF-8");
        $transactionHeaders = $this->http_parse_headers($headers);
        $cusId = '';

        if( $httpcode == '201' ) {
                
            $customerIdurl   = $transactionHeaders['Location'] ? $transactionHeaders['Location'] : $transactionHeaders['location'];
            $path = parse_url($customerIdurl, PHP_URL_PATH);
            $cusId = basename($path);
        }
        return $cusId;
    }

    function http_parse_headers($raw_headers)
    {
        $headers = array();
        $key = ''; // [+]

        foreach(explode("\n", $raw_headers) as $i => $h)
        {
            $h = explode(':', $h, 2);

            if (isset($h[1]))
            {
                if (!isset($headers[$h[0]]))
                    $headers[$h[0]] = trim($h[1]);
                elseif (is_array($headers[$h[0]]))
                {

                    // $tmp = array_merge($headers[$h[0]], array(trim($h[1]))); // [-]
                    // $headers[$h[0]] = $tmp; // [-]
                    $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1]))); // [+]
                }
                else
                {
                    // $tmp = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [-]
                    // $headers[$h[0]] = $tmp; // [-]
                    $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [+]
                }

                $key = $h[0]; // [+]
            }
            else // [+]
            { // [+]
                if (substr($h[0], 0, 1) == "\t") // [+]
                    $headers[$key] .= "\r\n\t".trim($h[0]); // [+]
                elseif (!$key) // [+]
                    $headers[0] = trim($h[0]);trim($h[0]); // [+]
            } // [+]
        }
        return $headers;
    }

    public function getOrderPlaceRedirectUrl()
    { 
        if(!empty($_SESSION['3durl'])){
          return $_SESSION['3durl'];
        }
    } 
}
