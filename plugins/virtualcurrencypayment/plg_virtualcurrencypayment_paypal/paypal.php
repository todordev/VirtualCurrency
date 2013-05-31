<?php
/**
 * @package		 ITPrism Plugins
 * @subpackage	 VirtualCurrency Payment 
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * VirtualCurrency is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * VirtualCurrency Payment Plugin
 *
 * @package		ITPrism Plugins
 * @subpackage	VirtualCurrency
 */
class plgVirtualCurrencyPaymentPayPal extends JPlugin {
    
    public function onProjectPayment($context, $item) {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/

        if($app->isAdmin()) {
            return;
        }

        $doc     = JFactory::getDocument();
        /**  @var $doc JDocumentHtml **/
        
        // Check document type
        $docType = $doc->getType();
        if(strcmp("html", $docType) != 0){
            return;
        }
       
        if(strcmp("com_virtualcurrency.payment", $context) != 0){
            return;
        }
        
        // Load language
        $this->loadLanguage();
        
        $notifyUrl = $this->getNotifyUrl();
        $returnUrl = $this->getReturnUrl();
        $cancelUrl = $this->getCancelUrl();
        
        $html  =  "";
        $html .= '<h4>'.JText::_("PLG_VIRTUALCURRENCYPAYMENT_PAYPAL_TITLE").'</h4>';
        $html .= '<p>'.JText::_("PLG_VIRTUALCURRENCYPAYMENT_PAYPAL_INFO").'</p>';
        
        if(!$this->params->get('paypal_sandbox', 1)) {
            $html .= '<form action="'.$this->params->get('paypal_url').'" method="post">';
            $html .= '<input type="hidden" name="business" value="'.$this->params->get('paypal_business_name').'" />';
        }  else {
            $html .= '<form action="'.$this->params->get('paypal_sandbox_url').'" method="post">';
            $html .= '<input type="hidden" name="business" value="'.$this->params->get('paypal_sandbox_business_name').'" />';
        }
        
        $html .= '<input type="hidden" name="cmd" value="_xclick" />';
        $html .= '<input type="hidden" name="charset" value="utf-8" />';
        $html .= '<input type="hidden" name="currency_code" value="'.$item->currency.'" />';
        $html .= '<input type="hidden" name="amount" value="'.$item->amount.'" />';
        $html .= '<input type="hidden" name="quantity" value="1" />';
        $html .= '<input type="hidden" name="no_shipping" value="1" />';
        $html .= '<input type="hidden" name="no_note" value="1" />';
        $html .= '<input type="hidden" name="tax" value="0" />';
        
        // Title
        $title = htmlentities($item->title, ENT_QUOTES, "UTF-8");
        $html .= '<input type="hidden" name="item_name" value="'.$title.'" />';
        
        // Get an ID of temporary record
        $tmpId = $app->getUserState("ordering.tmp_id", 0);
        
        // Custom data
        $custom = array(
            "tmp_id"	=>  $tmpId,
            "gateway"	=>  "PayPal"
        );
        
        $custom = base64_encode( json_encode($custom) );
        
        $html .= '<input type="hidden" name="custom" value="'.$custom.'" />';
        
        if($this->params->get('paypal_image_url')) {
            $html .= '<input type="hidden" name="image_url" value="'.$this->params->get('paypal_image_url').'" />';
        }
        
        if($this->params->get('paypal_cpp_headerback_color')) {
            $html .= '<input type="hidden" name="cpp_headerback_color" value='.$this->params->get('paypal_cpp_headerback_color').'" />';
        }
        
        $html .= '<input type="hidden" name="cancel_return" value="'.$cancelUrl.'" />';
        
        $html .= '<input type="hidden" name="return" value="'.$returnUrl.'" />';
        
        $html .= '<input type="hidden" name="notify_url" value="'.$notifyUrl.'" />';
        $html .= '<input type="image" name="submit" border="0" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif" alt="'.JText::_("PLG_VIRTUALCURRENCYPAYMENT_PAYPAL_BUTTON_ALT").'">
        <img alt="" border="0" width="1" height="1" src="https://www.paypal.com/en_US/i/scr/pixel.gif" >  
    	</form>';
        
        if($this->params->get('paypal_sandbox', 1)) {
            $html .= '<p class="sticky">'.JText::_("PLG_VIRTUALCURRENCYPAYMENT_PAYPAL_WORKS_SANDBOX").'</p>';
        }
        
        return $html;
        
    }
    
    /**
     * 
     * Enter description here ...
     * @param array 	$post	This is _POST variable
     * @param JRegistry $params	The parameters of the component
     */
    public function onPaymenNotify($context, $post, $params) {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        if($app->isAdmin()) {
            return;
        }

        $doc     = JFactory::getDocument();
        /**  @var $doc JDocumentRaw **/
        
        // Check document type
        $docType = $doc->getType();
        if(strcmp("raw", $docType) != 0){
            return;
        }

        if(strcmp("com_virtualcurrency.notify", $context) != 0){
            return;
        }
        
        // Verify gateway. Is it PayPal? 
        if(!$this->isPayPalGateway($post)) {
            return null;
        }
        
        // Load language
        $this->loadLanguage();
        
        // Get PayPal URL
        $sandbox      = $this->params->get('paypal_sandbox', 0); 
        if(!$sandbox) { 
            $url = $this->params->get('paypal_url', "https://www.paypal.com/cgi-bin/webscr"); 
        } else { 
            $url = $this->params->get('paypal_sandbox_url', "https://www.sandbox.paypal.com/cgi-bin/webscr");
        }
        
        // Validate PayPal response
        jimport("itprism.paypal.verify");
        $paypalVerify = new ITPrismPayPalVerify($url, $post);
        $paypalVerify->verify();
        
        $result = null;
        
        if($paypalVerify->isVerified()) {
            
            // Validate transaction data
            $validData = $this->validateData($post, $params);
            if(is_null($validData)) {
                return $result;
            }
            
            // Save transaction data
            $this->save($validData);
            
            //  Prepare the data that will be returned
            $result = JArrayHelper::toObject($validData);
            
        }
        
        return $result;
                
    }
    
	/**
     * Validate PayPal transaction
     * @param array $data    POST data
     * @param array $params  The parameters of the component
     */
    protected function validateData($data, $params) {
        
        // Prepare transaction data
        $custom    = JArrayHelper::getValue($data, "custom");
        $custom    = json_decode( base64_decode($custom), true );
        
        $tmpId     = JArrayHelper::getValue($custom, "tmp_id");
            
        // Get temporary data
        jimport("virtualcurrency.temporary");
        $temporary = new VirtualCurrencyTemporary();
        $temporary->load($tmpId);
        
        // Check for valid temporary data
        if(!$temporary->id) {
            $error  = JText::_("PLG_VIRTUALCURRENCYPAYMENT_PAYPAL_ERROR_INVALID_TEMPORARY_DATA");
            $error .= "\n". JText::sprintf("PLG_VIRTUALCURRENCYPAYMENT_PAYPAL_CUSTOM_DATA", var_export($temporary, true));
            JLog::add($error);
            return null;
        }
        
        // Prepare transaction data
        $transaction = array(
            "number"		     => $temporary->number,
        	"txn_id"             => JArrayHelper::getValue($data, "txn_id"),
        	"txn_amount"		 => JArrayHelper::getValue($data, "mc_gross"),
            "txn_currency"       => JArrayHelper::getValue($data, "mc_currency"),
            "txn_status"         => strtolower( JArrayHelper::getValue($data, "payment_status") ),
        	"txn_date"           => JArrayHelper::getValue($data, "payment_date"),
        	"service_provider"   => "PayPal",
        	"currency_id"		 => $temporary->currency_id,
        	"sender_id"			 => $params->get("ordering_bank_id"),
        	"receiver_id"		 => $temporary->user_id,
        ); 
        
        // Check User Id, Project ID and Transaction ID
        if(!$transaction["receiver_id"] OR !$transaction["currency_id"] OR !$transaction["txn_id"]) {
            $error  = JText::_("PLG_VIRTUALCURRENCYPAYMENT_PAYPAL_ERROR_INVALID_TRANSACTION_DATA");
            $error .= "\n". JText::sprintf("PLG_VIRTUALCURRENCYPAYMENT_PAYPAL_TRANSACTION_DATA", var_export($transaction, true));
            JLog::add($error);
            return null;
        }
        
        // Get currency
        jimport("virtualcurrency.currency");
        $currency = VirtualCurrencyCurrency::getInstance($temporary->currency_id);

        // Check currency
        if(strcmp($transaction["txn_currency"], $currency->currency) != 0) {
            $error  = JText::_("PLG_VIRTUALCURRENCYPAYMENT_PAYPAL_ERROR_INVALID_TRANSACTION_CURRENCY");
            $error .= "\n". JText::sprintf("PLG_VIRTUALCURRENCYPAYMENT_PAYPAL_TRANSACTION_DATA", var_export($transaction, true));
            JLog::add($error);
            return null;
        }
        
        // I am using the number of items to calculate how does it cost.
        $amount = $currency->calculate($temporary->number);
        
        // Check for valid amount
        if($transaction["txn_amount"] != $amount) {
            $error  = JText::_("PLG_VIRTUALCURRENCYPAYMENT_PAYPAL_ERROR_INVALID_PAID_AMOUNT");
            $error .= "\n". JText::sprintf("PLG_VIRTUALCURRENCYPAYMENT_PAYPAL_TRANSACTION_DATA", var_export($transaction, true));
            JLog::add($error);
            return null;
        }
        
        // Check receiver
        $allowedReceivers = array(
            JArrayHelper::getValue($data, "business"),
            JArrayHelper::getValue($data, "receiver_email"),
            JArrayHelper::getValue($data, "receiver_id")
        );
        
        if($this->params->get("paypal_sandbox", 0)) {
            $receiver = $this->params->get("paypal_sandbox_business_name");
        } else {
            $receiver = $this->params->get("paypal_business_name");
        }
        if(!in_array($receiver, $allowedReceivers)) {
            $error  = JText::_("PLG_VIRTUALCURRENCYPAYMENT_PAYPAL_ERROR_INVALID_RECEIVER");
            $error .= "\n". JText::sprintf("PLG_VIRTUALCURRENCYPAYMENT_PAYPAL_TRANSACTION_DATA", var_export($transaction, true));
            JLog::add($error);
            return null;
        }
        
        return $transaction;
    }
    
    /**
     * 
     * Save transaction
     * @param array $data
     */
    public function save($data) {
        
        // Save data about donation
        $db     = JFactory::getDbo();
        
        $date             = new JDate($data["txn_date"]);
        $data["txn_date"] = $date->toSql();
        
        jimport("virtualcurrency.transaction");
        $transaction      = new VirtualCurrencyTransaction();
        $transaction->bind($data);
        $transaction->store();
        
        // Get account and update item amount
        jimport("virtualcurrency.account");
        $account = new VirtualCurrencyAccount();
        $keys = array(
        	"user_id"     => $data["receiver_id"], 
        	"currency_id" => $data["currency_id"]
        );
        $account->load($keys);
        
        // Store the number of paid items
        $account->increaseAmount($data["number"]);
        $account->store();
    }
    
    private function getNotifyUrl() {
        
        $notifyPage = $this->params->get('paypal_notify_url');
        $uri        = JFactory::getURI();
        
        $domain     = $uri->toString(array("host"));
        
        if( false == strpos($notifyPage, $domain) ) {
            $notifyPage = $uri->toString(array("scheme", "host"))."/".str_replace("&", "&amp;", $notifyPage);
        }
        
        return $notifyPage;
        
    }
    
    private function getReturnUrl() {
        
        $returnPage = $this->params->get('paypal_return_url');
        if(!$returnPage) {
            $uri        = JFactory::getURI();
            $returnPage = $uri->toString(array("scheme", "host")).JRoute::_("index.php?option=com_virtualcurrency&view=ordering&layout=information", false);
        } 
        
        return $returnPage;
        
    }
    
    private function getCancelUrl() {
        
        $cancelPage = $this->params->get('paypal_cancel_url');
        if(!$cancelPage) {
            $uri        = JFactory::getURI();
            $cancelPage = $uri->toString(array("scheme", "host")).JRoute::_("index.php?option=com_virtualcurrency&view=ordering&layout=default", false);
        } 
        
        return $cancelPage;
    }
    
    private function isPayPalGateway($post) {
        
        $custom         = JArrayHelper::getValue($post, "custom");
        $custom         = json_decode( base64_decode($custom), true );
        $paymentGateway = JArrayHelper::getValue($custom, "gateway");

        if(strcmp("PayPal", $paymentGateway) != 0 ) {
            return false;
        }
        
        return true;
    }
    
}