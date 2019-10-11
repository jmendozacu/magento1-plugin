<?php
 
/**
* Our test CC module adapter
*/
class Mage_CeevoPayment_Model_Transactiontype extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'ceevopayment';
     
    public function toOptionArray()
    {

      return [
         ['value' => "TEST", 'label' => "TEST"],
         ['value' => "LIVE", 'label' => "LIVE"]
      ];
    }  
}
