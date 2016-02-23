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

/**
 * VirtualCurrency Payment Plugin
 *
 * @package        VirtualCurrency
 * @subpackage     Plugins
 */
class plgVirtualCurrencyPaymentPayPal extends Virtualcurrency\Payment\Plugin
{
    public function __construct(&$subject, $config = array())
    {
        parent::__construct($subject, $config);

        $this->serviceProvider = 'PayPal';
        $this->serviceAlias    = 'paypal';
        $this->textPrefix     .= '_' . \JString::strtoupper($this->serviceAlias);
        $this->debugType      .= '_' . \JString::strtoupper($this->serviceAlias);
    }

    /**
     * Display payment form.
     *
     * @param string                   $context
     * @param stdClass                 $item
     * @param Joomla\Registry\Registry $params Component options.
     * @param string $currencyType The type of the currency that should be used - real or virtual.
     *
     * @return null|string
     */
    public function onPreparePayment($context, &$item, &$params)
    {
        $currencyType = (is_array($item->order) and array_key_exists('currency_type', $item->order)) ? $item->order['currency_type'] : '';
        // The plugin can only be used for payment via real currency.
        if (!in_array($currencyType, array('real', 'both'), true)) {
            return null;
        }

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        if ($app->isAdmin()) {
            return null;
        }

        $doc = JFactory::getDocument();
        /**  @var $doc JDocumentHtml */

        // Check document type
        $docType = $doc->getType();
        if (strcmp('html', $docType) !== 0) {
            return null;
        }

        if (strcmp('com_virtualcurrency.payment.prepare', $context) !== 0) {
            return null;
        }

        // This is a URI path to the plugin folder
        $pluginURI = 'plugins/virtualcurrencypayment/paypal';

        $notifyUrl = $this->getCallbackUrl();
        $returnUrl = $this->getReturnUrl();
        $cancelUrl = $this->getCancelUrl();

        // DEBUG DATA
        JDEBUG ? $this->log->add(JText::_($this->textPrefix . '_DEBUG_NOTIFY_URL'), $this->debugType, $notifyUrl) : null;
        JDEBUG ? $this->log->add(JText::_($this->textPrefix . '_DEBUG_RETURN_URL'), $this->debugType, $returnUrl) : null;
        JDEBUG ? $this->log->add(JText::_($this->textPrefix . '_DEBUG_CANCEL_URL'), $this->debugType, $cancelUrl) : null;

        $html   = array();
        $html[] = '<div class="well">';

        $html[] = '<h4><img src="' . $pluginURI . '/images/paypal_icon.png" width="36" height="32" alt="PayPal" />' . JText::_($this->textPrefix . '_TITLE') . '</h4>';

        // Prepare payment receiver.
        $paymentReceiver = ($this->params->get('sandbox', 0)) ? JString::trim($this->params->get('sandbox_business_name')) : JString::trim($this->params->get('business_name'));
        if (!$paymentReceiver) {
            $html[] = $this->generateSystemMessage(JText::_($this->textPrefix . '_ERROR_PAYMENT_RECEIVER_MISSING'));
            return implode("\n", $html);
        }

        // Display additional information.
        $html[] = '<p>' . JText::_($this->textPrefix . '_INFO') . '</p>';

        // Start the form.
        if ($this->params->get('sandbox', 1)) {
            $html[] = '<form action="' . JString::trim($this->params->get('sandbox_url')) . '" method="post">';
        } else {
            $html[] = '<form action="' . JString::trim($this->params->get('url')) . '" method="post">';
        }

        $html[] = '<input type="hidden" name="business" value="' . $paymentReceiver . '" />';
        $html[] = '<input type="hidden" name="cmd" value="_xclick" />';
        $html[] = '<input type="hidden" name="charset" value="utf-8" />';
        $html[] = '<input type="hidden" name="currency_code" value="' . $item->order['real']['currency_code'] . '" />';
        $html[] = '<input type="hidden" name="amount" value="' . $item->order['real']['item_price'] . '" />';
        $html[] = '<input type="hidden" name="quantity" value="' . $item->order['items_number'] . '" />';
        $html[] = '<input type="hidden" name="no_shipping" value="1" />';
        $html[] = '<input type="hidden" name="no_note" value="1" />';
        $html[] = '<input type="hidden" name="tax" value="0" />';

        // Title
        $html[] = '<input type="hidden" name="item_name" value="' . htmlentities($item->title, ENT_QUOTES, 'UTF-8') . '" />';

        // Get payment session
        $paymentSessionLocal = $this->app->getUserState(Virtualcurrency\Constants::PAYMENT_SESSION_CONTEXT);
        $paymentSession      = $this->getPaymentSession(array(
            'session_id' => $paymentSessionLocal->session_id
        ));

        // Prepare custom data
        $custom = array(
            'payment_session_id' => $paymentSession->getId(),
            'gateway'            => $this->serviceAlias
        );

        $custom = base64_encode(json_encode($custom));
        $html[] = '<input type="hidden" name="custom" value="' . $custom . '" />';

        // Set a link to logo
        $imageUrl = JString::trim($this->params->get('image_url'));
        if ($imageUrl) {
            $html[] = '<input type="hidden" name="image_url" value="' . $imageUrl . '" />';
        }

        // Set URLs
        $html[] = '<input type="hidden" name="cancel_return" value="' . $cancelUrl . '" />';
        $html[] = '<input type="hidden" name="return" value="' . $returnUrl . '" />';
        $html[] = '<input type="hidden" name="notify_url" value="' . $notifyUrl . '" />';

        // Set locale
        $html[] = '<input type="hidden" name="lc" value="' . $this->params->get('locale', 'en_US') . '" />';

        // Prepare button
        if ($this->params->get('button_url')) {
            $html[] = '<input type="image" name="submit" border="0" src="' . $this->params->get('button_url') . '" alt="' . JText::_($this->textPrefix . '_BUTTON_ALT') . '">';
        } else { // Default button
            $html[] = '<input type="image" name="submit" border="0" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif" alt="' . JText::_($this->textPrefix . '_BUTTON_ALT') . '">';
        }

        // End the form.
        $html[] = '<img alt="" border="0" width="1" height="1" src="https://www.paypal.com/en_US/i/scr/pixel.gif" >';
        $html[] = '</form>';

        // Display a sticky note if the extension works in sandbox mode.
        if ($this->params->get('sandbox', 1)) {
            $html[] = '<div class="bg-info p-10-5"><span class="fa fa-info-circle"></span> ' . JText::_($this->textPrefix . '_WORKS_SANDBOX') . '</div>';
        }

        $html[] = '</div>';

        return implode("\n", $html);
    }

    /**
     * This method processes transaction data that comes from PayPal instant notifier.
     *
     * @param string                   $context
     * @param Joomla\Registry\Registry $params The parameters of the component
     *
     * @return null|array
     */
    public function onPaymentNotify($context, &$params)
    {
        if (strcmp('com_virtualcurrency.notify.'.$this->serviceAlias, $context) !== 0) {
            return null;
        }

        if ($this->app->isAdmin()) {
            return null;
        }

        $doc = JFactory::getDocument();
        /**  @var $doc JDocumentHtml */

        // Check document type
        $docType = $doc->getType();
        if (strcmp('raw', $docType) !== 0) {
            return null;
        }

        // Validate request method
        $requestMethod = $this->app->input->getMethod();
        if (strcmp('POST', $requestMethod) !== 0) {
            $this->log->add(
                JText::_($this->textPrefix . '_ERROR_INVALID_REQUEST_METHOD'),
                $this->debugType,
                JText::sprintf($this->textPrefix . '_ERROR_INVALID_TRANSACTION_REQUEST_METHOD', $requestMethod)
            );

            return null;
        }

        // DEBUG DATA
        JDEBUG ? $this->log->add(JText::_($this->textPrefix . '_DEBUG_RESPONSE'), $this->debugType, $_POST) : null;

        // Decode custom data
        $custom = Joomla\Utilities\ArrayHelper::getValue($_POST, 'custom');
        $custom = json_decode(base64_decode($custom), true);

        // DEBUG DATA
        JDEBUG ? $this->log->add(JText::_($this->textPrefix . '_DEBUG_CUSTOM'), $this->debugType, $custom) : null;

        // Verify gateway. Is it PayPal?
        $gateway = Joomla\Utilities\ArrayHelper::getValue($custom, 'gateway');
        if (!$this->isValidPaymentGateway($gateway)) {
            $this->log->add(
                JText::_($this->textPrefix . '_ERROR_INVALID_PAYMENT_GATEWAY'),
                $this->debugType,
                array('custom' => $custom, '_POST' => $_POST)
            );

            return null;
        }

        // Get PayPal URL
        if ($this->params->get('paypal_sandbox', 1)) {
            $url = JString::trim($this->params->get('paypal_sandbox_url', 'https://www.sandbox.paypal.com/cgi-bin/webscr'));
        } else {
            $url = JString::trim($this->params->get('paypal_url', 'https://www.paypal.com/cgi-bin/webscr'));
        }

        $paypalIpn       = new Prism\Payment\PayPal\Ipn($url, $_POST);
        $loadCertificate = (bool)$this->params->get('paypal_load_certificate', 0);
        $paypalIpn->verify($loadCertificate);

        // DEBUG DATA
        JDEBUG ? $this->log->add(JText::_($this->textPrefix . '_DEBUG_VERIFY_OBJECT'), $this->debugType, $paypalIpn) : null;

        // Prepare the array that have to be returned by this method.
        $result = array(
            'item'             => null,
            'transaction'      => null,
            'payment_session'  => null,
            'service_provider' => $this->serviceProvider,
            'service_alias'    => $this->serviceAlias
        );

        if ($paypalIpn->isVerified()) {

            // Get currency
            $currency   = new Virtualcurrency\Currency\Real\Currency(JFactory::getDbo());
            $currency->load($params->get('payments_currency_id'));

            // Get payment session data
            $paymentSessionId = Joomla\Utilities\ArrayHelper::getValue($custom, 'payment_session_id', 0, 'int');
            $paymentSession = $this->getPaymentSession(array('id' => $paymentSessionId));

            // DEBUG DATA
            JDEBUG ? $this->log->add(JText::_($this->textPrefix . '_DEBUG_PAYMENT_SESSION'), $this->debugType, $paymentSession->getProperties()) : null;

            // Validate transaction data
            $validData = $this->validateData($_POST, $currency->getCode(), $paymentSession);
            if ($validData === null) {
                return $result;
            }

            // DEBUG DATA
            JDEBUG ? $this->log->add(JText::_($this->textPrefix . '_DEBUG_VALID_DATA'), $this->debugType, $validData) : null;

            // Get item.
            $itemId   = Joomla\Utilities\ArrayHelper::getValue($validData, 'item_id', 0, 'int');
            $itemType = Joomla\Utilities\ArrayHelper::getValue($validData, 'item_type', '', 'cmd');
            $userId   = Joomla\Utilities\ArrayHelper::getValue($validData, 'receiver_id', 0, 'int');

            if (strcmp('currency', $itemType) === 0) {
                $item   = new Virtualcurrency\Account\Account(JFactory::getDbo());
                $item->load(array('user_id' => $userId, 'currency_id' => $itemId));
            } else { // Commodity
                $item   = new Virtualcurrency\User\Commodity(JFactory::getDbo());
                $item->load(array('user_id' => $userId, 'commodity_id' => $itemId));
            }

            // DEBUG DATA
            JDEBUG ? $this->log->add(JText::_($this->textPrefix . '_DEBUG_ITEM_OBJECT'), $this->debugType, $item->getProperties()) : null;

            // Check for valid project
            if (!$item->getId() or ($validData['txn_amount'] < $item->getPrice('real', $validData['units']))) {

                // Log data in the database
                $this->log->add(
                    JText::_($this->textPrefix . '_ERROR_INVALID_ITEM'),
                    $this->debugType,
                    $validData
                );

                return $result;
            }

            if ($validData['txn_amount'] < $item->getPrice('real', $validData['units'])) {

                // Log data in the database
                $this->log->add(
                    JText::_($this->textPrefix . '_ERROR_INVALID_ITEMS_PRICE'),
                    $this->debugType,
                    $validData
                );

                return $result;
            }

            // Save transaction data.
            // If it is not completed, return empty results.
            // If it is complete, continue with process transaction data
            $transactionData = $this->storeTransaction($validData, $item);
            if ($transactionData === null) {
                return $result;
            }

            // Generate object of data, based on the transaction properties.
            $result['transaction'] = Joomla\Utilities\ArrayHelper::toObject($transactionData);

            // Generate object of data based on the project properties.
            $properties        = $item->getProperties();
            $result['item']    = Joomla\Utilities\ArrayHelper::toObject($properties);

            // Generate data object, based on the payment session properties.
            $properties       = $paymentSession->getProperties();
            $result['payment_session'] = Joomla\Utilities\ArrayHelper::toObject($properties);

            // DEBUG DATA
            JDEBUG ? $this->log->add(JText::_($this->textPrefix . '_DEBUG_RESULT_DATA'), $this->debugType, $result) : null;

            // Remove payment session.
            $paymentSession->delete();

        } else {

            // Log error
            $this->log->add(
                JText::_($this->textPrefix . '_ERROR_INVALID_TRANSACTION_DATA'),
                $this->debugType,
                array('error message' => $paypalIpn->getError(), 'paypalVerify' => $paypalIpn, '_POST' => $_POST)
            );

        }

        return $result;
    }

    /**
     * Validate PayPal transaction.
     *
     * @param array  $data
     * @param string $currencyCode
     * @param Virtualcurrency\Payment\Session  $paymentSession
     *
     * @return array
     */
    protected function validateData($data, $currencyCode, $paymentSession)
    {
        $txnDate = Joomla\Utilities\ArrayHelper::getValue($data, 'payment_date');
        $date    = new JDate($txnDate);

        // Prepare transaction data
        $transaction = array(
            'sender_id'        => Virtualcurrency\Constants::BANK_ID,
            'receiver_id'      => (int)$paymentSession->getUserId(),
            'title'            => Joomla\Utilities\ArrayHelper::getValue($data, 'item_name', '', 'string'),
            'item_id'          => (int)$paymentSession->getItemId(),
            'item_type'        => $paymentSession->getItemType(),
            'units'            => (int)$paymentSession->getItemsNumber(),
            'service_provider' => $this->serviceProvider,
            'service_alias'    => $this->serviceAlias,
            'txn_id'           => Joomla\Utilities\ArrayHelper::getValue($data, 'txn_id', null, 'string'),
            'txn_amount'       => Joomla\Utilities\ArrayHelper::getValue($data, 'mc_gross', null, 'float'),
            'txn_currency'     => Joomla\Utilities\ArrayHelper::getValue($data, 'mc_currency', null, 'string'),
            'txn_status'       => JString::strtolower(Joomla\Utilities\ArrayHelper::getValue($data, 'payment_status', null, 'string')),
            'txn_date'         => $date->toSql(),
            'extra_data'       => $this->prepareExtraData($data)
        );

        // Check Project ID and Transaction ID
        if (!$transaction['item_id'] or !$transaction['txn_id']) {

            // Log data in the database
            $this->log->add(
                JText::_($this->textPrefix . '_ERROR_INVALID_TRANSACTION_DATA'),
                $this->debugType,
                $transaction
            );

            return null;
        }

        // Check currency
        if (strcmp($transaction['txn_currency'], $currencyCode) !== 0) {

            // Log data in the database
            $this->log->add(
                JText::_($this->textPrefix . '_ERROR_INVALID_TRANSACTION_CURRENCY'),
                $this->debugType,
                array('TRANSACTION DATA' => $transaction, 'CURRENCY' => $currencyCode)
            );

            return null;
        }

        // Check payment receiver.
        $allowedReceivers = array(
            JString::strtolower(Joomla\Utilities\ArrayHelper::getValue($data, 'business')),
            JString::strtolower(Joomla\Utilities\ArrayHelper::getValue($data, 'receiver_email')),
            JString::strtolower(Joomla\Utilities\ArrayHelper::getValue($data, 'receiver_id'))
        );

        // Get payment receiver.
        if ($this->params->get('paypal_sandbox', 1)) {
            $paymentReceiver = JString::strtolower(JString::trim($this->params->get('sandbox_business_name')));
        } else {
            $paymentReceiver = JString::strtolower(JString::trim($this->params->get('business_name')));
        }

        if (!in_array($paymentReceiver, $allowedReceivers, true)) {
            // Log data in the database
            $this->log->add(
                JText::_($this->textPrefix . '_ERROR_INVALID_RECEIVER'),
                $this->debugType,
                array('TRANSACTION DATA' => $transaction, 'RECEIVER' => $paymentReceiver, 'RECEIVER DATA' => $allowedReceivers)
            );

            return null;
        }

        return $transaction;
    }

    /**
     * Save transaction data.
     *
     * @param array     $transactionData
     * @param Virtualcurrency\Account\Account|Virtualcurrency\User\Commodity  $item
     *
     * @return null|array
     */
    protected function storeTransaction($transactionData, $item)
    {
        // Get transaction by txn ID
        $keys        = array(
            'txn_id' => Joomla\Utilities\ArrayHelper::getValue($transactionData, 'txn_id')
        );
        $transaction = new Virtualcurrency\Transaction\Transaction(JFactory::getDbo());
        $transaction->load($keys);

        // DEBUG DATA
        JDEBUG ? $this->log->add(JText::_($this->textPrefix . '_DEBUG_TRANSACTION_OBJECT'), $this->debugType, $transaction->getProperties()) : null;

        // Check for existed transaction
        // If the current status if completed, stop the payment process.
        if ($transaction->getId() and $transaction->isCompleted()) {
            return null;
        }

        // Add extra data.
        if (array_key_exists('extra_data', $transactionData)) {
            if (!empty($transactionData['extra_data'])) {
                $transaction->addExtraData($transactionData['extra_data']);
            }

            unset($transactionData['extra_data']);
        }

        // Store the new transaction data.
        $transaction->bind($transactionData);
        $transaction->store();

        // If it is not completed (it might be pending or other status),
        // stop the process. Only completed transaction will continue
        // and will process the project, rewards,...
        if (!$transaction->isCompleted()) {
            return null;
        }

        // Set transaction ID.
        $transactionData['id'] = $transaction->getId();

        // Get item.
        $itemType    = Joomla\Utilities\ArrayHelper::getValue($transactionData, 'item_type', '', 'cmd');
        $itemsNumber = Joomla\Utilities\ArrayHelper::getValue($transactionData, 'units', 0, 'int');

        if (strcmp('currency', $itemType) === 0 and $itemsNumber > 0) {
            $item->increaseAmount($itemsNumber);
            $item->storeAmount();
        } else { // Commodity
            $item->increaseNumber($itemsNumber);
            $item->storeNumber();
        }

        return $transactionData;
    }
}
