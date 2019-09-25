<?php

class Mage_NewModule_Block_Form_Cc extends Mage_Payment_Block_Form
{
  protected function _construct()
  {
    parent::_construct();
    $this->setTemplate('newmodule/form/cc.phtml');
  }

  public function getPaymentMethods(){
    //$paymentMethods = $this->method->getConfigData('paytype');
    //$pay_methods = explode(',',$paymentMethods);
    //return $pay_methods;

    $api_key = $this->method->getConfigData('api_key');

     $get_data = $this->callAPI('GET', 'https://api.ceevo.com/payment/methods', $api_key);

        $response = json_decode($get_data, true);
     
        //$response = array(0=>array('title'=>'pay1'),1=>array('title'=>'pay2'));
        $methods_array = [];
        foreach($response as $methods){
          array_push($methods_array,array('value' => $methods['method_code'],'label'=> $methods['title']) );
  
        }

          
        return $methods_array;
  }


  function callAPI($method, $url, $data, $api_key){
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
           'X-CV-APIKey: '.$api_key
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
     
        $result = curl_exec($curl);
       
        if(!$result){die("Connection Failure");}
        curl_close($curl);
        return $result;
     }

}