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
   

    public function  successAction()
    {
       $response = $this->getRequest()->getPost();//get response

       //make another api call here by using curl or whatever

       $this->_redirect('checkout/onepage/success');// redirect success page
    }


    public function  failureAction()
    {
       $response = $this->getRequest()->getPost();//get response

       //make another api call here by using curl or whatever

       $this->_redirect('checkout/onepage/error');// redirect success page
    }


    
}