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

class Uni_Payhub_Helper_Data extends Mage_Core_Helper_Abstract {
   /**
    * payhubGateway send response on transaction process
    * @return boolean
    */
    public function payhubGateway() {
        $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        $checkout = Mage::getSingleton('checkout/session');
        $data = $checkout->getPayhubPaymentDetail();
        $checkout->unsetData('payhub_payment_detail');
$api_endpoint = "https://checkout.payhub.com/transaction/api"; 
if(Mage::getStoreConfig('payment/payhub/demo')):
$mode='demo';
    else:    
$mode='live';
    endif;
$debug = true; #boolean
$sale_data = array("mode" => $mode,
//*********Merchant Account Details*************//    
                   "orgid" => Mage::getStoreConfig('payment/payhub/orgid'),
                   "username" => Mage::getStoreConfig('payment/payhub/username'),
                   "password" => Mage::getStoreConfig('payment/payhub/password'),
                   "tid" => Mage::getStoreConfig('payment/payhub/tid'),
//*********Merchant Account Details*************//    
                    "trans_type" => "sale",    
                   "first_name" => $order->getCustomerFirstname(), 
                   "last_name" => $order->getCustomerLastname(), 
                   "phone" => $order->getBillingAddress()->getTelephone(), 
                   "email" => $order->getCustomerEmail(), 
                   "address1" => $order->getBillingAddress()->getStreet(), 
                   "city" => $order->getBillingAddress()->getCity(), 
                   "state" => $order->getBillingAddress()->getRegion(), 
                   "zip" => $order->getBillingAddress()->getPostCode(), 
                   "cc" => $data['cc_number'], 
                   "month" => $data['cc_exp_month'], 
                   "year" => $data['cc_exp_year'], 
                   "cvv" => $data['cc_cid'], 
                   "amount" => $order->getGrandTotal()
    );
       
$json_payload = json_encode($sale_data);

$ch = curl_init();

$c_opts = array(CURLOPT_URL => $api_endpoint,
                CURLOPT_VERBOSE => $debug,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $json_payload);

curl_setopt_array($ch, $c_opts);

$result = curl_exec($ch);

curl_close($ch);

$result = json_decode($result);

if($debug) var_dump($result);

zend_debug::dump($sale_data);

if($result->RESPONSE_CODE == "00" || $result->RESPONSE_CODE == "08" || $result->RESPONSE_CODE == "11"|| $result->RESPONSE_CODE == "85")
{
    #Success
          Mage::getSingleton('core/session')->addSuccess($result->RESPONSE_TEXT);
          Mage::getSingleton('core/session')->addSuccess('Your order has been received.');
            return true;
}
else
{
    #Fail
          Mage::getSingleton('core/session')->addError($result->RESPONSE_TEXT);
            return false;
    }
    
    }

}