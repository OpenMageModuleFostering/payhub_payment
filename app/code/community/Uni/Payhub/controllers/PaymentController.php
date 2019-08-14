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
class Uni_Payhub_PaymentController extends Mage_Core_Controller_Front_Action {

    /**
     * The redirect action used to trigger when someone places an order.
     */
    public function redirectAction() {
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('Mage_Core_Block_Template', 'payhub', array('template' => 'payhub/redirect.phtml'));
        echo $block;
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }

    /**
     * The response action used to trigger when your gateway sends back a response after processing the customer's payment.
     */
    public function responseAction() {
        
        $model = Mage::helper('payhub');
        if ($model->payhubGateway()) {
            Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure' => true));
        } else {
            $this->cancelAction();
            Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure' => true));
        }
    }

    /**
     * The cancel action used to trigger when an order is to be cancelled. 
     */
    public function cancelAction() {
        if (Mage::getSingleton('checkout/session')->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
            if ($order->getId()) {
                $order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Gateway has declined the payment.')->save();
            }
        }
    }

}