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

class Uni_Payhub_Model_Standard extends Mage_Payment_Model_Method_Cc {

    protected $_code = 'payhub';
    protected $_formBlockType = 'payhub/form_payhub';
    protected $_infoBlockType = 'payhub/info_payhub';
    	protected $_canSaveCc = true;
    /**
     * Process payment gateway
     * @return type
     */
    public function getOrderPlaceRedirectUrl() {
        return Mage::getUrl('payhub/payment/redirect', array('_secure' => true));
    }
    /**
     * Post data to use for payment
     * @param type $observer
     */
        public function beforeSavePaymentCustom($observer){
        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_onepage_savePayment')
        {
            $data = Mage::app()->getRequest()->getPost('payment', array());
            $checkout = Mage::getSingleton('checkout/session');
            $checkout->setPayhubPaymentDetail($data);
        }
    }

}