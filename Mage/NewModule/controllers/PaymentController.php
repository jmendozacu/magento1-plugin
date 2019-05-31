<?php

class Mage_NewModule_PaymentController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $var ="abcd";
        $customer = Mage::getModel('customer/session')->setData('token',$_POST['token_hidden_input']);
        $orderId = $this->_getCheckoutSession()->getLastOrderId();
        $order = ($orderId) ? Mage::getModel('sales/order')->load($orderId) : false;
        //$this->loadLayout();
        //$this->renderLayout();
        $this->_redirect('checkout/onepage/savepayment');
       
    }

    protected function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }


    
}
