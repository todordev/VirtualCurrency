<?php
/**
 * @package         VirtualCurrency
 * @subpackage      Plugins
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
jimport('virtualcurrency.payment.plugin');

/**
 * VirtualCurrency Payment Plugin
 *
 * @package        VirtualCurrency
 * @subpackage     Plugins
 */
class plgVirtualCurrencyPaymentPayPal extends VirtualCurrencyPaymentPlugin
{
    protected $paymentService = "paypal";

    protected $textPrefix = "PLG_VIRTUALCURRENCYPAYMENT_PAYPAL";
    protected $debugType = "PAYPAL_PAYMENT_PLUGIN_DEBUG";
    
    /**
     * Display payment form.
     *
     * @param string $context
     * @param object $item
     * @param Joomla\Registry\Registry $params Component options.
     *
     * @return null|string
     */
    public function onProjectPayment($context, &$item, &$params)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        if ($app->isAdmin()) {
            return null;
        }

        $doc = JFactory::getDocument();
        /**  @var $doc JDocumentHtml */

        // Check document type
        $docType = $doc->getType();
        if (strcmp("html", $docType) != 0) {
            return null;
        }

        if (strcmp("com_virtualcurrency.payment", $context) != 0) {
            return null;
        }

        // Get real currency
        $realCurrencyId = $params->get("payments_currency_id");
        jimport("virtualcurrency.realcurrency");
        $realCurrency = VirtualCurrencyRealCurrency::getInstance(JFactory::getDbo(), $realCurrencyId, $params);

        // This is a URI path to the plugin folder
        $pluginURI = "plugins/virtualcurrencypayment/paypal";

        $notifyUrl = $this->getNotifyUrl();
        $returnUrl = $this->getReturnUrl();
        $cancelUrl = $this->getCancelUrl();

        $html = array();
        $html[] = '<h4><img src="' . $pluginURI . '/images/paypal_icon.png" width="36" height="32" alt="PayPal" />' . JText::_($this->textPrefix . "_TITLE") . '</h4>';
        $html[] = '<p>' . JText::_($this->textPrefix."_INFO") . '</p>';

        if (!$this->params->get('paypal_sandbox', 1)) {
            $html[] = '<form action="' . $this->params->get('paypal_url') . '" method="post">';
            $html[] = '<input type="hidden" name="business" value="' . $this->params->get('paypal_business_name') . '" />';
        } else {
            $html[] = '<form action="' . $this->params->get('paypal_sandbox_url') . '" method="post">';
            $html[] = '<input type="hidden" name="business" value="' . $this->params->get('paypal_sandbox_business_name') . '" />';
        }

        $html[] = '<input type="hidden" name="cmd" value="_xclick" />';
        $html[] = '<input type="hidden" name="charset" value="utf-8" />';
        $html[] = '<input type="hidden" name="currency_code" value="' . $realCurrency->getAbbr() . '" />';
        $html[] = '<input type="hidden" name="amount" value="' . $item->total . '" />';
        $html[] = '<input type="hidden" name="quantity" value="1" />';
        $html[] = '<input type="hidden" name="no_shipping" value="1" />';
        $html[] = '<input type="hidden" name="no_note" value="1" />';
        $html[] = '<input type="hidden" name="tax" value="0" />';

        // Title
        $title = htmlentities($item->title, ENT_QUOTES, "UTF-8");
        $html[] = '<input type="hidden" name="item_name" value="' . $title . '" />';

        // Get an ID of temporary record
        $paymentSessionData = $app->getUserState("payment.data");

        // Custom data
        $custom = array(
            "payment_id"  => JArrayHelper::getValue($paymentSessionData, "payment_id", 0),
            "gateway" => "PayPal"
        );

        $custom = base64_encode(json_encode($custom));

        $html[] = '<input type="hidden" name="custom" value="' . $custom . '" />';

        if ($this->params->get('paypal_image_url')) {
            $html[] = '<input type="hidden" name="image_url" value="' . $this->params->get('paypal_image_url') . '" />';
        }

        $html[] = '<input type="hidden" name="cancel_return" value="' . $cancelUrl . '" />';

        $html[] = '<input type="hidden" name="return" value="' . $returnUrl . '" />';

        $html[] = '<input type="hidden" name="notify_url" value="' . $notifyUrl . '" />';
        $html[] = '<input type="image" name="submit" border="0" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif"
        alt="' . JText::_($this->textPrefix."_BUTTON_ALT") . '">
        <img alt="" border="0" width="1" height="1" src="https://www.paypal.com/en_US/i/scr/pixel.gif" >  
    	</form>';

        if ($this->params->get('paypal_sandbox', 1)) {
            $html[] = '<p class="sticky">' . JText::_($this->textPrefix."_WORKS_SANDBOX") . '</p>';
        }

        return implode("\n", $html);
    }

    /**
     * This method processes transaction data that comes from PayPal instant notifier.
     *
     * @param string    $context
     * @param Joomla\Registry\Registry $params The parameters of the component
     *
     * @return null|object
     */
    public function onPaymenNotify($context, &$params)
    {
        if (strcmp("com_virtualcurrency.notify.paypal", $context) != 0) {
            return null;
        }

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        if ($app->isAdmin()) {
            return null;
        }

        $doc = JFactory::getDocument();
        /**  @var $doc JDocumentRaw */

        // Check document type
        $docType = $doc->getType();
        if (strcmp("raw", $docType) != 0) {
            return null;
        }

        // Validate request method
        $requestMethod = $app->input->getMethod();
        if (strcmp("POST", $requestMethod) != 0) {
            $this->log->add(
                JText::_($this->textPrefix . "_ERROR_INVALID_REQUEST_METHOD"),
                $this->debugType,
                JText::sprintf($this->textPrefix . "_ERROR_INVALID_TRANSACTION_REQUEST_METHOD", $requestMethod)
            );

            return null;
        }

        // DEBUG DATA
        JDEBUG ? $this->log->add(JText::_($this->textPrefix . "_DEBUG_RESPONSE"), $this->debugType, $_POST) : null;

        // Decode custom data
        $custom = JArrayHelper::getValue($_POST, "custom");
        $custom = json_decode(base64_decode($custom), true);

        // DEBUG DATA
        JDEBUG ? $this->log->add(JText::_($this->textPrefix . "_DEBUG_CUSTOM"), $this->debugType, $custom) : null;

        // Verify gateway. Is it PayPal?
        if (!$this->isPayPalGateway($custom)) {
            $this->log->add(
                JText::_($this->textPrefix . "_ERROR_INVALID_PAYMENT_GATEWAY"),
                $this->debugType,
                array("custom" => $custom, "_POST" => $_POST)
            );

            return null;
        }

        // Get PayPal URL
        $sandbox = $this->params->get('paypal_sandbox', 0);
        if (!$sandbox) {
            $url = JString::trim($this->params->get('paypal_url', "https://www.paypal.com/cgi-bin/webscr"));
        } else {
            $url = JString::trim($this->params->get('paypal_sandbox_url', "https://www.sandbox.paypal.com/cgi-bin/webscr"));
        }

        jimport("itprism.payment.paypal.ipn");
        $paypalIpn       = new ITPrismPayPalIpn($url, $_POST);
        $loadCertificate = (bool)$this->params->get("paypal_load_certificate", 0);
        $paypalIpn->verify($loadCertificate);

        // DEBUG DATA
        JDEBUG ? $this->log->add(JText::_($this->textPrefix . "_DEBUG_VERIFY_OBJECT"), $this->debugType, $paypalIpn) : null;

        // Prepare the array that will be returned by this method
        $result = array(
            "currency"        => null,
            "transaction"     => null,
            "payment_service" => "PayPal"
        );

        if ($paypalIpn->isVerified()) {

            // Get currency
            jimport("virtualcurrency.realcurrency");
            $realCurrencyId = $params->get("payments_currency_id");
            $realCurrency   = VirtualCurrencyRealCurrency::getInstance(JFactory::getDbo(), $realCurrencyId);

            // Get intention data
            $paymentId = JArrayHelper::getValue($custom, "payment_id", 0, "int");

            jimport("virtualcurrency.payment.session");
            $paymentSession = new VirtualCurrencyPaymentSession(JFactory::getDbo());
            $paymentSession->load($paymentId);

            // DEBUG DATA
            JDEBUG ? $this->log->add(JText::_($this->textPrefix . "_DEBUG_PAYMENT_SESSION"), $this->debugType, $paymentSession->getProperties()) : null;

            // Validate transaction data
            $validData = $this->validateData($_POST, $realCurrency->getAbbr(), $paymentSession, $params);
            if (is_null($validData)) {
                return $result;
            }

            // DEBUG DATA
            JDEBUG ? $this->log->add(JText::_($this->textPrefix . "_DEBUG_VALID_DATA"), $this->debugType, $validData) : null;

            // Get project.
            jimport("virtualcurrency.currency");
            $currencyId = JArrayHelper::getValue($validData, "currency_id");
            $currency   = VirtualCurrencyCurrency::getInstance(JFactory::getDbo(), $currencyId);

            // DEBUG DATA
            JDEBUG ? $this->log->add(JText::_($this->textPrefix . "_DEBUG_CURRENCY_OBJECT"), $this->debugType, $currency->getProperties()) : null;

            // Check for valid project
            if (!$currency->getId()) {

                // Log data in the database
                $this->log->add(
                    JText::_($this->textPrefix . "_ERROR_INVALID_CURRENCY"),
                    $this->debugType,
                    $validData
                );

                return $result;
            }

            // Save transaction data.
            // If it is not completed, return empty results.
            // If it is complete, continue with process transaction data
            if (!$this->storeTransaction($validData, $currency)) {
                return $result;
            }

            //  Prepare the data that will be returned

            $result["transaction"] = JArrayHelper::toObject($validData);

            // Generate object of data based on the project properties
            $properties        = $currency->getProperties();
            $result["currency"] = JArrayHelper::toObject($properties);

            // DEBUG DATA
            JDEBUG ? $this->log->add(JText::_($this->textPrefix . "_DEBUG_RESULT_DATA"), $this->debugType, $result) : null;

            // Remove intention
            $txnStatus = (isset($result["transaction"]->txn_status)) ? $result["transaction"]->txn_status : null;
            $this->removePaymentSession($paymentSession, $txnStatus);
            unset($paymentSession);

        } else {

            // Log error
            $this->log->add(
                JText::_($this->textPrefix . "_ERROR_INVALID_TRANSACTION_DATA"),
                $this->debugType,
                array("error message" => $paypalIpn->getError(), "paypalVerify" => $paypalIpn, "_POST" => $_POST)
            );

        }

        return $result;
    }

    /**
     * This method is executed after complete payment.
     * It is used to be sent mails to user and administrator
     *
     * @param object $context
     * @param object $transaction Transaction data
     * @param Joomla\Registry\Registry $params Component parameters
     * @param object $currency Currency data
     *
     */
    public function onAfterPayment($context, &$transaction, &$params, &$currency)
    {
        if (strcmp("com_virtualcurrency.notify.paypal", $context) != 0) {
            return;
        }

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        if ($app->isAdmin()) {
            return;
        }

        $doc = JFactory::getDocument();
        /**  @var $doc JDocumentHtml */

        // Check document type
        $docType = $doc->getType();
        if (strcmp("raw", $docType) != 0) {
            return;
        }

        // Send mails
        $this->sendMails($currency, $transaction, $params);
    }

    /**
     * Validate PayPal transaction.
     *
     * @param array $data   POST data
     * @param string $realCurrency
     * @param VirtualCurrencyPaymentSession $paymentSession
     * @param Joomla\Registry\Registry $params
     *
     * @return array
     */
    protected function validateData($data, $realCurrency, $paymentSession, $params)
    {
        $txnDate = JArrayHelper::getValue($data, "payment_date");
        $date    = new JDate($txnDate);

        // Prepare transaction data
        $transaction = array(
            "sender_id"        => (int)$params->get("payments_bank_id"),
            "receiver_id"      => (int)$paymentSession->getUserId(),
            "currency_id"      => (int)$paymentSession->getCurrencyId(),
            "units"            => (int)$paymentSession->getAmount(),
            "service_provider" => "PayPal",
            "txn_id"           => JArrayHelper::getValue($data, "txn_id", null, "string"),
            "txn_amount"       => JArrayHelper::getValue($data, "mc_gross", null, "float"),
            "txn_currency"     => JArrayHelper::getValue($data, "mc_currency", null, "string"),
            "txn_status"       => JString::strtolower(JArrayHelper::getValue($data, "payment_status", null, "string")),
            "txn_date"         => $date->toSql(),
        );


        // Check Currency ID and Transaction ID
        if (!$transaction["currency_id"] or !$transaction["txn_id"]) {

            // Log data in the database
            $this->log->add(
                JText::_($this->textPrefix . "_ERROR_INVALID_TRANSACTION_DATA"),
                $this->debugType,
                $transaction
            );

            return null;
        }


        // Check real currency.
        if (strcmp($transaction["txn_currency"], $realCurrency) != 0) {

            // Log data in the database
            $this->log->add(
                JText::_($this->textPrefix . "_ERROR_INVALID_TRANSACTION_CURRENCY"),
                $this->debugType,
                array("TRANSACTION DATA" => $transaction, "CURRENCY" => $realCurrency)
            );

            return null;
        }


        // Check receiver
        $allowedReceivers = array(
            JString::strtolower(JArrayHelper::getValue($data, "business")),
            JString::strtolower(JArrayHelper::getValue($data, "receiver_email")),
            JString::strtolower(JArrayHelper::getValue($data, "receiver_id"))
        );

        if ($this->params->get("paypal_sandbox", 0)) {
            $receiver = JString::strtolower(JString::trim($this->params->get("paypal_sandbox_business_name")));
        } else {
            $receiver = JString::strtolower(JString::trim($this->params->get("paypal_business_name")));
        }

        if (!in_array($receiver, $allowedReceivers)) {
            // Log data in the database
            $this->log->add(
                JText::_($this->textPrefix . "_ERROR_INVALID_RECEIVER"),
                $this->debugType,
                array("TRANSACTION DATA" => $transaction, "RECEIVER" => $receiver, "RECEIVER DATA" => $allowedReceivers)
            );

            return null;
        }

        return $transaction;
    }

    /**
     * Save transaction.
     *
     * @param array     $data
     *
     * @return boolean
     */
    protected function storeTransaction($data)
    {
        // Get transaction by txn ID
        jimport("virtualcurrency.transaction");
        $keys        = array(
            "txn_id" => JArrayHelper::getValue($data, "txn_id")
        );
        $transaction = new VirtualCurrencyTransaction(JFactory::getDbo());
        $transaction->load($keys);

        // DEBUG DATA
        JDEBUG ? $this->log->add(JText::_($this->textPrefix . "_DEBUG_TRANSACTION_OBJECT"), $this->debugType, $transaction->getProperties()) : null;

        // Check for existed transaction
        if ($transaction->getId()) {

            // If the current status if completed,
            // stop the process.
            if ($transaction->isCompleted()) {
                return false;
            }

        }

        // Store the new transaction data.
        $transaction->bind($data);
        $transaction->store();

        // If it is not completed (it might be pending or other status),
        // stop the process. Only completed transaction will continue
        // and will process the units.
        if (!$transaction->isCompleted()) {
            return false;
        }

        // If the new transaction is completed,
        // update user account.
        $keys = array(
            "user_id" => $transaction->getReceiverId(),
            "currency_id" => $transaction->getCurrencyId(),
        );

        jimport("virtualcurrency.account");
        $account = new VirtualCurrencyAccount(JFactory::getDbo());
        $account->load($keys);
        $account->increaseAmount($transaction->getUnits());
        $account->updateAmount();

        return true;
    }

    /**
     * Remove a payment session record or change the state of a payment session record.
     *
     * @param VirtualCurrencyPaymentSession $paymentSession
     * @param string                        $txnStatus
     */
    protected function removePaymentSession($paymentSession, $txnStatus)
    {
        // If status is NOT completed set the state of the session to pending.
        /** @todo do it in next release */
        if (strcmp("completed", $txnStatus) != 0) {


        // If transaction status is completed, remove the record.
        } elseif (strcmp("completed", $txnStatus) == 0) {
            $paymentSession->delete();
        }
    }

    protected function getNotifyUrl()
    {
        $page = JString::trim($this->params->get('paypal_notify_url'));

        $uri    = JURI::getInstance();
        $domain = $uri->toString(array("host"));

        if (false == strpos($page, $domain)) {
            $page = JURI::root() . str_replace("&", "&amp;", $page);
        }

        if (false === strpos($page, "payment_service=PayPal")) {
            $page .= "&amp;payment_service=PayPal";
        }

        // DEBUG DATA
        JDEBUG ? $this->log->add(JText::_($this->textPrefix . "_DEBUG_NOTIFY_URL"), $this->debugType, $page) : null;

        return $page;
    }

    protected function getReturnUrl()
    {
        $page = JString::trim($this->params->get('paypal_return_url'));
        if (!$page) {
            $uri  = JURI::getInstance();
            $page = $uri->toString(array("scheme", "host")) . JRoute::_(VirtualCurrencyHelperRoute::getPaymentRoute("information"), false);
        }

        // DEBUG DATA
        JDEBUG ? $this->log->add(JText::_($this->textPrefix . "_DEBUG_RETURN_URL"), $this->debugType, $page) : null;

        return $page;
    }

    protected function getCancelUrl()
    {
        $page = JString::trim($this->params->get('paypal_cancel_url'));
        if (!$page) {
            $uri  = JURI::getInstance();
            $page = $uri->toString(array("scheme", "host")) . JRoute::_(VirtualCurrencyHelperRoute::getPaymentRoute("default"), false);
        }

        // DEBUG DATA
        JDEBUG ? $this->log->add(JText::_($this->textPrefix . "_DEBUG_CANCEL_URL"), $this->debugType, $page) : null;

        return $page;
    }

    protected function isPayPalGateway($custom)
    {
        $paymentGateway = JArrayHelper::getValue($custom, "gateway");

        if (strcmp("PayPal", $paymentGateway) != 0) {
            return false;
        }

        return true;
    }
}
