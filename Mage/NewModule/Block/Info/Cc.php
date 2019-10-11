<?php

class Mage_CeevoPayment_Block_Info_Cc extends Mage_Payment_Block_Info
{
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }

        $info = $this->getInfo();
        $transport = new Varien_Object();
        $transport = parent::_prepareSpecificInformation($transport);
        $transport->addData(array(
            Mage::helper('internship_payment')->__('Check No#') => $info->getCheckNo(),
            Mage::helper('internship_payment')->__('Check Date') => $info->getCheckDate()
        ));

        return $transport;
    }
}
