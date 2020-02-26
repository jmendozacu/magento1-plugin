<?php
class Mage_CeevoPayment_Model_SecureFlag
{
     
    public function toOptionArray()
    {

      return [
         ['value' => "true", 'label' => "true"],
         ['value' => "false", 'label' => "false"]
      ];
    }  
}

