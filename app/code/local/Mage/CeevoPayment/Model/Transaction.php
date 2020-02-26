<?php
 
class Mage_CeevoPayment_Model_Transaction
{
     
    public function toOptionArray()
    {

      return [
         ['value' => "SALES", 'label' => "SALES"],
         ['value' => "AUTH", 'label' => "AUTH"]
      ];
    }  
}
