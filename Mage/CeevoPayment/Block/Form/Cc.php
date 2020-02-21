<?php

class Mage_CeevoPayment_Block_Form_Cc extends Mage_Payment_Block_Form
{ 
  
  protected function _construct(){
     parent::_construct();
     $this->setTemplate('ceevopayment/form/cc.phtml');
  }

  public function getPaymentMethods(){
     $methods_array = array();
     return $methods_array;
  }

}
