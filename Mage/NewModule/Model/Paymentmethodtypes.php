<?php
 
/**
* Our test CC module adapter
*/
class Mage_CeevoPayment_Model_Paymentmethodtypes extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'ceevopayment';

   
    public function toOptionArray()
    {

        //$get_data = $this->callAPI('GET', 'https://api.ceevo.com/payment/methods', false);

       // $response = json_decode($get_data, true);
        
        //$response = array(0=>array('title'=>'pay1'),1=>array('title'=>'pay2'));
        $methods_array = [];
        //foreach($response as $methods){
         // array_push($methods_array,array('value' => $methods['method_code'],'label'=> $methods['method_title']) );
  
       //   }
        return $methods_array;
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
           'X-CV-APIKey: 553fbbcd-f488-4e97-bf90-ad418a781e62'
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
     
        // EXECUTE:
        $result = curl_exec($curl);
        if(!$result){die("Connection Failure");}
        curl_close($curl);
        return $result;
     }

}
