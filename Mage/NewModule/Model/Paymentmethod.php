<?php
//require_once(__DIR__ . '/vendor/autoload.php');

use Omnipay\Common\CreditCard;
use Omnipay\Omnipay;
 
/**
* Our test CC module adapter
*/
class Mage_NewModule_Model_Paymentmethod extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'newmodule';
    protected $_formBlockType = 'newmodule/form_cc';
    protected $_infoBlockType = 'newmodule/info_cc';
    protected $_isInitializeNeeded      = true;

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
       
        return $this;
    }

    public function initialize($paymentAction, $stateObject)
    {
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState($state);
        $stateObject->setStatus('pending_payment');
        $stateObject->setIsNotified(false);
    
        $payment = $this->getInfoInstance();
        $order = $payment->getOrder();
        $order->save();

        $amount = $order->getTotalDue();

        $customer = $this->createCustomer($payment);
        if($customer['message'] == "Approved"){

            $banktransactionid = $customer['paymentId']; 
            
            $payment->setTransactionId($banktransactionid);
            $payment->setParentTransactionId($banktransactionid);
            $payment->setIsTransactionClosed(false);
            
        }else{
            $errorMsg = $this->_getHelper()->__('Error in processing payment.');
            Mage::throwException(
                $errorMsg
            );
        }
      
        
    }

 

    function createCustomer($payment){
       
        $order = $payment->getOrder();
        $billing = $order->getBillingAddress();
    
        $data = array("billing" => array("city" => $billing->getCity(), "country" => $billing->getCountry(),"state" => $billing->getRegion(),"street" => $billing->getStreetLine(1),"zip"=> $billing->getPostcode()),
                      "email" => $order->getCustomerEmail(),"firstName" => $billing->getFirstname(),"lastName" => $billing->getLastname(),"mobile" => $billing->getTelephone(),"phone" => $billing->getTelephone(),"sex" => "M");  
        $data_string = json_encode($data);
        $get_data = $this->callAPI('POST', 'https://api.ceevo.com/acquiring/customer', $data_string);
        $response = json_decode($get_data, true);
        $chargeResponse = $this->chargeApi($payment);
        return $chargeResponse;
    
    }
    
    function registerAccountToken($customer_registered_id,$order){
    
        $token_array = array("accountToken" => $order->info['customerToken'],"default" => true);
        $token_string = json_encode($token_array);
        $get_data = $this->callAPI('POST', 'https://api.ceevo.com/acquiring/customer/'.$customer_registered_id, $token_string);
        $response = json_decode($get_data, true);
    
    }

    function chargeApi($payment){

        $order = $payment->getOrder();
        $billing = $order->getBillingAddress();

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
        
        // $currencies = new ISOCurrencies();
        // $moneyParser = new DecimalMoneyParser($currencies);
        // $money = $moneyParser->parse((string)$order->getGrandTotal(), $order->getOrderCurrencyCode());
        // $converted_money = $money->getAmount(); // outputs 100000
    
        $items_array = [];
        foreach($order->getAllVisibleItems() as $item){
          
          $item_json = array("item" => $item->getName(),"itemValue" => $item->getPrice());
          array_push($items_array, json_encode($item_json));
        }
        $itemString = implode(',',$items_array);
    
        $jres = json_decode($res, true);
        $access_token = $jres['access_token'];
        
        $authorization = "Authorization: Bearer $access_token";
        
        $charge_api = "https://api.ceevo.com/acquiring/charge"; 
            
            $cparam = '{
                "cartItems": ['.$itemString.'],
                "amount": '.$order->getGrandTotal().',
                "3dsecure": '.$flag.',
                "mode" : "'.$mode.'",
                "methodCode":  "'.$_SESSION['paymentMethod'].'",
                "currency": "'.$order->getOrderCurrencyCode().'",
                "accountToken": "'.$_SESSION['token_hidden_input'].'",
                "sessionId": "'.$_SESSION['session_hidden_input'].'",
                "referenceId": "",
                "statementDescriptor": "",
                "userEmail": "'.$order->getCustomerEmail().'",
                "shippingAddress": {
                    "city": "'.$billing->getCity().'",
                    "country": "'.$billing->getCountry().'",
                    "state": "'.$billing->getRegion().'",
                    "street": "'.$billing->getStreetLine(1).'",
                    "zip": "'.$billing->getPostcode().'"
                }
            }';
            
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL,$charge_api); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $cparam);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json; charset=utf-8',
                    'Content-Length: ' . strlen($cparam),
                    $authorization
                )
            );
            $cres = curl_exec($ch); 
            $charge_response = json_decode($cres, true);
            return $charge_response;
        }

        function callAPI($method, $url, $data){
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
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
       
               'Content-Type: application/json',
            ));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
       
            // EXECUTE:
            $result = curl_exec($curl);
            if(!$result){die("Connection Failure");}
            curl_close($curl);
            return $result;
        }
    
}


