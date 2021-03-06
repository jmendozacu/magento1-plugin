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
        $hashKey =  $_SESSION['ceevo_hash_Key'];         
        $payData = base64_decode(urldecode($_POST['payload']));
        $HMACSHA256 = urldecode($_POST['HMACSHA256']);
        $s = hash_hmac('sha256', $payData, $hashKey, true);
        $checksum = urldecode(base64_encode($s));
        $PaymentData = json_decode(base64_decode($_POST['payload']));

        if($checksum == $HMACSHA256  && $PaymentData->status == 'SUCCEEDED')
        {     
            $payload_string = $_POST['payload'];
            $data = base64_decode($payload_string); 
            $returnData =  json_decode($data,true);
            $transactionId = $returnData['payment_id'];
            $orderId = $returnData['reference_id'];

            /* @var $order Mage_Sales_Model_Order */
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
            //create transaction. need for void if amount will not match.
            $payment = $order->getPayment();
            // $payment->setTransactionId($transactionId)
            //   ->setParentTransactionId(null)
            // ->setIsTransactionClosed(0);

            // $payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);
            $message = "Payment completed successfully with transaction id - ".$transactionId;              
            $orderState = Mage_Sales_Model_Order::STATE_PROCESSING;
            $order->setState($orderState, "pending", $message, false);           
            $order->save();
          
            $order->sendNewOrderEmail();
            Mage::getModel('sales/quote')
                ->load($order->getQuoteId())
                ->setIsActive(false)
                ->save();   
            $this->_redirect('checkout/onepage/success');// redirect success page
        }else{
            $this->_redirect('checkout/onepage/failure');
        }
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
