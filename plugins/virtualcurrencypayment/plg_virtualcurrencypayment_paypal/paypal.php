<?php
/**
 * @package         VirtualCurrency
 * @subpackage      Plugins
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017h Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

use Virtualcurrency\Payment\Session\Session as PaymentSession;
use Virtualcurrency\Cart\Item as CartItem;
use Joomla\Utilities\ArrayHelper;
use Joomla\Registry\Registry;
use Prism\Payment\Result as PaymentResult;
use Virtualcurrency\Transaction\Transaction;

// no direct access
defined('_JEXEC') or die;

/**
 * VirtualCurrency Payment Plugin
 *
 * @package        VirtualCurrency
 * @subpackage     Plugins
 */
class plgVirtualcurrencyPaymentPayPal extends Virtualcurrency\Payment\Plugin
{
    protected $logFile    = 'vc_paypal.log.php';

    public function __construct(&$subject, $config = array())
    {
        parent::__construct($subject, $config);

        $this->serviceProvider = 'PayPal';
        $this->serviceAlias    = 'paypal';
        $this->textPrefix     .= '_' . \strtoupper($this->serviceAlias);
        $this->debugType      .= '_' . \strtoupper($this->serviceAlias);
    }

    /**
     * Display payment form.
     *
     * @param string   $context
     * @param stdClass $item
     * @param Registry $params Component options.
     *
     * @return null|string
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function onPreparePayment($context, &$item, &$params)
    {
        if (strcmp('com_virtualcurrency.payment.prepare', $context) !== 0) {
            return null;
        }

        // The plugin can only be used for payment via real currency.
        $currencyType = ($item->order instanceof CartItem) ? $item->order->getCurrencyType() : '';
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

        $notifyUrl = $this->getCallbackUrl();
        $returnUrl = $this->getReturnUrl();
        $cancelUrl = $this->getCancelUrl();

        // DEBUG DATA
        JDEBUG ? $this->log->add('URL for notifying from IPN', $this->debugType, $notifyUrl) : null;
        JDEBUG ? $this->log->add('Return URL', $this->debugType, $returnUrl) : null;
        JDEBUG ? $this->log->add('Cancel URL', $this->debugType, $cancelUrl) : null;

        $html   = array();
        $html[] = '<div class="well">';
        $html[] = '<h4><img src="plugins/virtualcurrencypayment/paypal/images/paypal_icon.png" width="36" height="32" alt="PayPal" />' . JText::_($this->textPrefix . '_TITLE') . '</h4>';

        // Prepare payment receiver.
        $paymentReceiver = $this->params->get('sandbox', 0) ? trim($this->params->get('sandbox_business_name')) : trim($this->params->get('business_name'));
        if (!$paymentReceiver) {
            $html[] = $this->generateSystemMessage(JText::_($this->textPrefix . '_ERROR_PAYMENT_RECEIVER_MISSING'));
            return implode("\n", $html);
        }

        // Display additional information.
        $html[] = '<p>' . JText::_($this->textPrefix . '_INFO') . '</p>';

        // Start the form.
        if ($this->params->get('sandbox', 1)) {
            $html[] = '<form action="' . trim($this->params->get('sandbox_url')) . '" method="post">';
        } else {
            $html[] = '<form action="' . trim($this->params->get('url')) . '" method="post">';
        }

        $html[] = '<input type="hidden" name="business" value="' . $paymentReceiver . '" />';
        $html[] = '<input type="hidden" name="cmd" value="_xclick" />';
        $html[] = '<input type="hidden" name="charset" value="utf-8" />';
        $html[] = '<input type="hidden" name="currency_code" value="' . $item->order->price('real')->getCurrencyCode() . '" />';
        $html[] = '<input type="hidden" name="amount" value="' . $item->order->price('real')->getPrice() . '" />';
        $html[] = '<input type="hidden" name="quantity" value="' . $item->order->getItemsNumber() . '" />';
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
        $imageUrl = trim($this->params->get('image_url'));
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
            $html[] = '<div class="alert alert-info mb-0"><span class="fa fa-info-circle"></span> ' . JText::_($this->textPrefix . '_WORKS_SANDBOX') . '</div>';
        }

        $html[] = '</div>';

        return implode("\n", $html);
    }

    /**
     * This method processes transaction data that comes from PayPal instant notifier.
     *
     * @param string   $context
     * @param Registry $params The parameters of the component
     *
     * @return PaymentResult|null
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
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
            $this->log->add(JText::_($this->textPrefix . '_ERROR_INVALID_REQUEST_METHOD'), $this->debugType, JText::sprintf($this->textPrefix . '_ERROR_INVALID_TRANSACTION_REQUEST_METHOD', $requestMethod));
            return null;
        }

        // DEBUG DATA
        JDEBUG ? $this->log->add('POST Response', $this->debugType, $_POST) : null;

        // Decode custom data
        $custom = ArrayHelper::getValue($_POST, 'custom');
        $custom = json_decode(base64_decode($custom), true);

        // DEBUG DATA
        JDEBUG ? $this->log->add('Custom data', $this->debugType, $custom) : null;

        // Verify gateway. Is it PayPal?
        $gateway = ArrayHelper::getValue($custom, 'gateway');
        if (!$this->isValidPaymentGateway($gateway)) {
            $this->log->add(JText::_($this->textPrefix . '_ERROR_INVALID_PAYMENT_GATEWAY'), $this->debugType, ['custom' => $custom, '_POST' => $_POST]);
            return null;
        }

        // Get PayPal URL
        if ($this->params->get('paypal_sandbox', 1)) {
            $url = trim($this->params->get('paypal_sandbox_url', 'https://www.sandbox.paypal.com/cgi-bin/webscr'));
        } else {
            $url = trim($this->params->get('paypal_url', 'https://www.paypal.com/cgi-bin/webscr'));
        }

        $paypalIpn       = new Prism\Payment\PayPal\Ipn($url, $_POST);
        $loadCertificate = (bool)$this->params->get('load_certificate', 1);
        $paypalIpn->verify($loadCertificate);

        // DEBUG DATA
        JDEBUG ? $this->log->add('Verified object', $this->debugType, $paypalIpn) : null;

        // Prepare output data.
        $paymentResult = new PaymentResult;
        $paymentResult->serviceProvider = $this->serviceProvider;
        $paymentResult->serviceAlias = $this->serviceAlias;

        if ($paypalIpn->isVerified()) {
            $mapper = new \Virtualcurrency\RealCurrency\Mapper(new \Virtualcurrency\RealCurrency\Gateway\JoomlaGateway(JFactory::getDbo()));
            $repository = new \Virtualcurrency\RealCurrency\Repository($mapper);
            $currency   = $repository->fetchById($params->get('currency_id'));

            // Get payment session data
            $paymentSessionId = ArrayHelper::getValue($custom, 'payment_session_id', 0, 'int');
            $paymentSession   = $this->getPaymentSession(array('id' => $paymentSessionId));

            // DEBUG DATA
            JDEBUG ? $this->log->add('Payment session', $this->debugType, $paymentSession->getProperties()) : null;

            // Validate transaction data
            $validData = $this->validateData($_POST, $currency->getCode(), $paymentSession);
            if ($validData === null) {
                return null;
            }

            // DEBUG DATA
            JDEBUG ? $this->log->add('Validated Data', $this->debugType, $validData) : null;

            // Save transaction data.
            // If it is not completed, return empty results.
            // If it is complete, continue with process transaction data
            $transaction = $this->storeTransaction($validData);
            if ($transaction !== null) {
                $transaction                = $transaction->getProperties();
                $paymentResult->transaction = ArrayHelper::toObject($transaction);
            }
        } else {
            // Log error
            $this->log->add(
                JText::_($this->textPrefix . '_ERROR_INVALID_TRANSACTION_DATA'),
                $this->debugType,
                array('error message' => $paypalIpn->getError(), 'paypalVerify' => $paypalIpn, '_POST' => $_POST)
            );
        }

        return $paymentResult;
    }

    /**
     * Validate PayPal transaction.
     *
     * @param array  $data
     * @param string $currencyCode
     * @param PaymentSession  $paymentSession
     *
     * @return array
     * @throws InvalidArgumentException
     */
    protected function validateData($data, $currencyCode, $paymentSession)
    {
        $txnDate = ArrayHelper::getValue($data, 'payment_date');
        $date    = new JDate($txnDate);

        // Prepare transaction data
        $transaction = array(
            'title'            => ArrayHelper::getValue($data, 'item_name', '', 'string'),
            'units'            => (int)$paymentSession->getItemsNumber(),
            'sender_id'        => Virtualcurrency\Constants::BANK_ID,
            'receiver_id'      => (int)$paymentSession->getUserId(),
            'item_id'          => (int)$paymentSession->getItemId(),
            'item_type'        => $paymentSession->getItemType(),
            'service_provider' => $this->serviceProvider,
            'service_alias'    => $this->serviceAlias,
            'txn_id'           => ArrayHelper::getValue($data, 'txn_id', null, 'string'),
            'txn_amount'       => ArrayHelper::getValue($data, 'mc_gross', null, 'float'),
            'txn_currency'     => ArrayHelper::getValue($data, 'mc_currency', null, 'string'),
            'txn_status'       => strtolower(ArrayHelper::getValue($data, 'payment_status', null, 'string')),
            'txn_date'         => $date->toSql(),
            'extra_data'       => $this->prepareExtraData($data)
        );

        // Check Project ID and Transaction ID
        if (!$transaction['item_id'] or !$transaction['txn_id']) {
            $this->log->add(JText::_($this->textPrefix . '_ERROR_INVALID_TRANSACTION_DATA'), $this->debugType, $transaction);
            return null;
        }

        // Check currency
        if (strcmp($transaction['txn_currency'], $currencyCode) !== 0) {
            $this->log->add(JText::_($this->textPrefix . '_ERROR_INVALID_TRANSACTION_CURRENCY'), $this->debugType, ['TRANSACTION DATA' => $transaction, 'CURRENCY' => $currencyCode]);
            return null;
        }

        // Check payment receiver.
        $allowedReceivers = array(
            strtolower(ArrayHelper::getValue($data, 'business')),
            strtolower(ArrayHelper::getValue($data, 'receiver_email')),
            strtolower(ArrayHelper::getValue($data, 'receiver_id'))
        );

        // Get payment receiver.
        if ($this->params->get('paypal_sandbox', Prism\Constants::YES)) {
            $paymentReceiver = strtolower(trim($this->params->get('sandbox_business_name')));
        } else {
            $paymentReceiver = strtolower(trim($this->params->get('business_name')));
        }

        if (!in_array($paymentReceiver, $allowedReceivers, true)) {
            $this->log->add(JText::_($this->textPrefix . '_ERROR_INVALID_RECEIVER'), $this->debugType, ['TRANSACTION DATA' => $transaction, 'RECEIVER' => $paymentReceiver, 'RECEIVER DATA' => $allowedReceivers]);
            return null;
        }

        return $transaction;
    }

    /**
     * Save transaction data.
     *
     * @param array $data
     *
     * @return null|Transaction
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     */
    protected function storeTransaction(array $data)
    {
        // Get transaction by txn ID
        $keys        = array(
            'txn_id' => ArrayHelper::getValue($data, 'txn_id')
        );
        $txnMapper      = new Virtualcurrency\Transaction\Mapper(new Virtualcurrency\Transaction\Gateway\JoomlaGateway(JFactory::getDbo()));
        $txnRepository  = new Virtualcurrency\Transaction\Repository($txnMapper);
        $transaction    = $txnRepository->fetch($keys);

        // DEBUG DATA
        JDEBUG ? $this->log->add('Transaction object before bind().', $this->debugType, $transaction->getProperties()) : null;

        // Check for existed transaction
        // If the current status if completed, stop the payment process.
        if ($transaction->getId() and $transaction->isCompleted()) {
            return null;
        }

        // Add extra data.
        if (array_key_exists('extra_data', $data)) {
            if (!empty($data['extra_data'])) {
                $transaction->addExtraData($data['extra_data']);
            }
            unset($data['extra_data']);
        }

        // Store the new transaction data.
        $transaction->bind($data);

        // DEBUG DATA
        JDEBUG ? $this->log->add('Transaction object after bind().', $this->debugType, $transaction->getProperties()) : null;

        // If it is not completed (it might be pending or other status),
        // stop the process. Only completed transaction will continue
        // and will process the project, rewards,...
       /* if (!$transaction->isCompleted()) {
            return null;
        }*/

        $itemType    = ArrayHelper::getValue($data, 'item_type', '', 'cmd');

        // DEBUG DATA
        JDEBUG ? $this->log->add('Item Type', $this->debugType, $itemType) : null;

        // Start database transaction.
        if (strcmp('currency', $itemType) === 0) {
            $transactionalApp  = new \Virtualcurrency\Transaction\Service\Joomla\VirtualByReal($transaction, \JFactory::getDbo());
        } else { // Commodity
            $transactionalApp  = new \Virtualcurrency\Transaction\Service\Joomla\CommodityByReal($transaction, \JFactory::getDbo());
        }

        $transactionalSession   = new \Prism\Database\JoomlaDatabaseSession(\JFactory::getDbo());
        $paymentTransaction     = new \Prism\Domain\TransactionalApplicationService($transactionalApp, $transactionalSession);

        // DEBUG DATA
        JDEBUG ? $this->log->add('Before execute', $this->debugType) : null;

        $paymentTransaction->execute();

        // DEBUG DATA
        JDEBUG ? $this->log->add('After execute', $this->debugType) : null;

        return $transaction;
    }
}
