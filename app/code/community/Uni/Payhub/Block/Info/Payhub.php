<?php

/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Payhub
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
?>

<?php

class Uni_Payhub_Block_Info_Payhub extends Mage_Payment_Block_Info {

    /**
     * Payment info block
     * @param type $transport
     * @return type
     */
    protected function _prepareSpecificInformation($transport = null) {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $info = $this->getInfo();
        $ccname = Mage::getConfig()->getNode('global/payment/cc/types/' . $info->getCcType() . '/name');
        if (Mage::app()->getStore()->isAdmin()):
            $ccnum = $info->getCcNumber();
        else:
            $ccnum = 'xxxx-' . $info->getCcLast4();
        endif;
        $transport = new Varien_Object();
        $transport = parent::_prepareSpecificInformation($transport);
        $transport->addData(array(
            Mage::helper('payment')->__('Credit Card Type') => $ccname,
            Mage::helper('payment')->__('Credit Card Number') => $ccnum,
            Mage::helper('payment')->__('Name on the Card') => $info->getCcOwner(),
            Mage::helper('payment')->__('Expiration Date') => $info->getCcExpMonth() . ' / ' . $info->getCcExpYear(),
        ));
        return $transport;
    }

}
