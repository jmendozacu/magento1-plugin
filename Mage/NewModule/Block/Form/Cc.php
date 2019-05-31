<?php

class Mage_NewModule_Block_Form_Cc extends Mage_Payment_Block_Form
{
  protected function _construct()
  {
    parent::_construct();
    $this->setTemplate('newmodule/form/cc.phtml');
  }

  public function getPaymentMethods(){
    $paymentMethods = $this->method->getConfigData('paytype');
    $pay_methods = explode(',',$paymentMethods);
    return $pay_methods;
  }
}