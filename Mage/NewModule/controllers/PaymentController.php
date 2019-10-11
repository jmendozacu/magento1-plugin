<?php

class Mage_CeevoPayment_PaymentController extends Mage_Core_Controller_Front_Action
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

        $session = Mage::getSingleton('core/session');

        $payload_string = $_POST['payload'];
        $data = base64_decode($payload_string); 
        $returnData =  json_decode($data,true);

        $transactionId = $returnData['payment_id'];
        $orderId = $returnData['reference_id'];
     
       
          /* @var $order Mage_Sales_Model_Order */
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        //create transaction. need for void if amount will not match.
        $payment = $order->getPayment();
        $payment->setTransactionId($transactionId)
            ->setParentTransactionId(null)
            ->setIsTransactionClosed(0);

        $payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);

        $order->setData('state', "complete");
        $order->setStatus("complete");       
        $order->save();

      
        $order->sendNewOrderEmail();
        

         Mage::getModel('sales/quote')
                ->load($order->getQuoteId())
                ->setIsActive(false)
                ->save();
       

         $this->_redirect('checkout/onepage/success');// redirect success page
      
    }


    public function failureAction()
    {
        $session = Mage::getSingleton('core/session');

        $payload_string = $_POST['payload'];
        $data = base64_decode($payload_string); 
        $returnData =  json_decode($data,true);

        $transactionId = $returnData['payment_id'];
        $orderId = $returnData['reference_id'];
        $responseText = $returnData['fraud_check_result']['description'];
        
        /* @var $order Mage_Sales_Model_Order */
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
           
           if ($order->getId() &&  $order->getState() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
                //operate with order
                 

                $payment = $order->getPayment();
                $payment->setTransactionId(null)
                    ->setParentTransactionId($transactionId);

                //$order->registerCancellation($responseText)
                   // ->save();
                
            } 

        $this->_redirect('checkout/onepage/failure');              
    }
  
}
