<?php
 
/**
* Our test CC module adapter
*/
class Mage_NewModule_Model_Transactiontype extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'newmodule';
     
    public function toOptionArray()
    {

      return [
         ['value' => "TEST", 'label' => "TEST"],
         ['value' => "LIVE", 'label' => "LIVE"]
      ];
    }

    
}

