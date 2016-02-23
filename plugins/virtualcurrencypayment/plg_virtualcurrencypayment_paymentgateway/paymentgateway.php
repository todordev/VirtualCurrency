<?php
/**
 * @package         VirtualCurrency
 * @subpackage      Plugins
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Virtualcurrency Payment Plugin
 *
 * @package        VirtualCurrency
 * @subpackage     Plugins
 */
class plgVirtualcurrencyPaymentPaymentGateway extends Virtualcurrency\Payment\Plugin
{
    public function __construct(&$subject, $config = array())
    {
        parent::__construct($subject, $config);

        $this->serviceProvider = 'Payment Gateway';
        $this->serviceAlias    = 'paymentgateway';
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

        // The plugin can only be used for payment via real currency.
        $currencyType = (is_array($item->order) and array_key_exists('currency_type', $item->order)) ? $item->order['currency_type'] : '';
        if (!in_array($currencyType, array('virtual', 'both'), true)) {
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

        $virtualCurrencyData = array();
        if (isset($item->order) or !is_array($item->order) and array_key_exists('virtual', $item->order)) {
            $virtualCurrencyData = $item->order['virtual'];
        }

        if (!$virtualCurrencyData or !array_key_exists('currency_id', $virtualCurrencyData)) {
            return null;
        }

        // Get the virtual currency that will be used for payment.
        $currency = new Virtualcurrency\Currency\Currency(JFactory::getDbo());
        $currency->load($virtualCurrencyData['currency_id']);
        if (!$currency->getId()) {
            return null;
        }

        $userId   = JFactory::getUser()->get('id');
        if (!$userId) {
            return null;
        }

        $account = new Virtualcurrency\Account\Account(JFactory::getDbo());
        $account->load(array('user_id' => $userId, 'currency_id' => $currency->getId()));
        if (!$account->getId()) {
            return null;
        }

        $totalCost              = $virtualCurrencyData['total_cost'];
        $currencyCode           = $virtualCurrencyData['currency_code'];
        $currencyTitle          = htmlentities($currency->getTitle(), ENT_QUOTES, 'UTF-8');
        $numberOfItemsFormatted = htmlentities($item->order['items_number_formatted'], ENT_QUOTES, 'UTF-8');
        $itemsCostFormatted     = htmlentities($virtualCurrencyData['items_cost_formatted'], ENT_QUOTES, 'UTF-8');

        $componentParams        = JComponentHelper::getParams('com_virtualcurrency');

        $amountFormatter = new Virtualcurrency\Amount($componentParams);
        $amountFormatter->setCurrency($currency);
        $availableAmount = $amountFormatter->setValue($account->getAmount())->formatCurrency();

        // URL to images.
        $imageURI = JUri::base() . $componentParams->get('media_folder', 'images/virtualcurrency');

        $returnUrl = $this->getReturnUrl();
        $cancelUrl = $this->getCancelUrl();

        // DEBUG DATA
        JDEBUG ? $this->log->add(JText::_($this->textPrefix . '_DEBUG_RETURN_URL'), $this->debugType, $returnUrl) : null;
        JDEBUG ? $this->log->add(JText::_($this->textPrefix . '_DEBUG_CANCEL_URL'), $this->debugType, $cancelUrl) : null;

        $html   = array();
        $html[] = '<div class="well">';

        $title = JText::sprintf($this->textPrefix . '_TITLE_S', $currencyTitle);
        $icon  = '';
        if ($currency->getIcon()) {
            $icon = '<img src="' . $imageURI . '/'. $currency->getIcon().'" alt="'.$title.'" /> ';
        }
        $html[] = '<h4>' .$icon . $title . '</h4>';

        $html[] = '<p>'.JText::sprintf($this->textPrefix . '_YOU_BUY_S_S', $numberOfItemsFormatted, $itemsCostFormatted).'</p>';
        $html[] = '<p>'.JText::sprintf($this->textPrefix . '_YOU_HAVE_S', $availableAmount).'</p>';

        $buttonDisabled = '';
        if ($account->getAmount() < $totalCost) {
            $html[] = $this->generateSystemMessage(JText::sprintf($this->textPrefix . '_ERROR_NOT_ENOUGH_AMOUNT', $currencyTitle), 'warning');
            $buttonDisabled = 'disabled="disabled"';
        }

        // Start the form.
        $html[] = '<form action="index.php?option=com_virtualcurrency" method="post">';
        $html[] = '<input type="hidden" name="task" value="payments.checkout" />';
        $html[] = '<input type="hidden" name="payment_service" value="paymentgateway" />';
        $html[] = JHtml::_('form.token');

        // Prepare button
        $html[] = '<button type="submit" name="submit" class="btn btn-primary mb-10" '.$buttonDisabled.'>'.JText::_($this->textPrefix . '_BUY_NOW').'</button>';

        // End the form.
        $html[] = '</form>';

        // Display a sticky note if the extension works in sandbox mode.
        if ($this->params->get('sandbox', 1)) {
            $html[] = '<div class="bg-info p-10-5"><span class="fa fa-info-circle"></span> ' . JText::_($this->textPrefix . '_WORKS_SANDBOX') . '</div>';
        }

        $html[] = '</div>';

        return implode("\n", $html);
    }

    /**
     * Process payment transaction.
     *
     * @param string                   $context
     * @param stdClass                 $item
     * @param Joomla\Registry\Registry $params
     *
     * @return null|array
     */
    public function onPaymentsCheckout($context, &$item, &$params)
    {
        if (strcmp('com_virtualcurrency.payments.checkout.paymentgateway', $context) !== 0) {
            return null;
        }

        if ($this->app->isAdmin()) {
            return null;
        }

        $doc = JFactory::getDocument();
        /**  @var $doc JDocumentHtml */

        // Check document type
        $docType = $doc->getType();
        if (strcmp('html', $docType) !== 0) {
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

        // Prepare output data.
        $output = array(
            'redirect_url' => VirtualCurrencyHelperRoute::getCartRoute(),
            'message'      => ''
        );
        
        // Get payment session.
        $cartSession    = $this->app->getUserState(Virtualcurrency\Constants::PAYMENT_SESSION_CONTEXT);

        $paymentSession = $this->getPaymentSession(array(
            'session_id' => $cartSession->session_id
        ));
        
        // Create the charge on Stripe's servers - this will charge the user's card
        try {

            // DEBUG DATA
            JDEBUG ? $this->log->add(JText::_($this->textPrefix . '_DEBUG_PAYMENT_SESSION'), $this->debugType, $paymentSession->getProperties()) : null;

            // Validate transaction data
            $validData = $this->validateData($item, $paymentSession);
            if ($validData === null) {
                return $output;
            }

            // DEBUG DATA
            JDEBUG ? $this->log->add(JText::_($this->textPrefix . '_DEBUG_VALID_DATA'), $this->debugType, $validData) : null;

            // Save transaction data.
            // If it is not completed, return empty results.
            // If it is complete, continue with process transaction data
            $transaction = $this->storeTransaction($item, $validData, $this->params->get('sandbox', 1));

            if ($transaction !== null) {
                $transaction = $transaction->getProperties();
                $transaction = Joomla\Utilities\ArrayHelper::toObject($transaction);
            }
            
        } catch (RuntimeException $e) {

            $output['message']      = $e->getMessage();
            return $output;

        } catch (Exception $e) {

            $output['message']      = $e->getMessage();
            return $output;
        }

        // Get next URL.
        $output['redirect_url'] = ($this->getReturnUrl()) ?: VirtualCurrencyHelperRoute::getCartRoute('summary');

        // Send mails
        $this->sendMails($item, $transaction, $params);

        return $output;
    }

    /**
     * Validate transaction.
     *
     * @param stdClass                        $item
     * @param Virtualcurrency\Payment\Session $paymentSession
     *
     * @return array
     */
    protected function validateData($item, $paymentSession)
    {
        $date    = new JDate();

        // Prepare transaction data
        $transaction = array(
            'title'            => htmlentities($item->title, ENT_QUOTES, 'UTF-8'),
            'units'            => (int)abs($item->order['items_number']),
            'sender_id'        => Virtualcurrency\Constants::BANK_ID,
            'receiver_id'      => (int)$paymentSession->getUserId(),
            'item_id'          => (int)$item->id,
            'item_type'        => $item->order['item_type'],
            'service_provider' => JText::_($this->textPrefix.'_SYSTEM'),
            'txn_id'           => JString::strtoupper(Prism\Utilities\StringHelper::generateRandomString(16)),
            'txn_amount'       => $item->order['virtual']['total_cost'],
            'txn_currency'     => $item->order['virtual']['currency_code'],
            'txn_status'       => 'pending',
            'txn_date'         => $date->toSql()
        );

        // Check Currency ID and Transaction ID
        if (!$transaction['item_id'] or !$transaction['receiver_id']) {
            $this->log->add(
                JText::_($this->textPrefix . '_ERROR_INVALID_TRANSACTION_DATA'),
                $this->debugType,
                $transaction
            );
            return null;
        }

        if (!$transaction['txn_amount'] or !$transaction['txn_currency']) {
            $this->log->add(
                JText::_($this->textPrefix . '_ERROR_INVALID_AMOUNT_OR_CURRENCY'),
                $this->debugType,
                $transaction
            );
            return null;
        }

        if (!in_array($transaction['item_type'], array('currency', 'commodity'), true)) {
            $this->log->add(
                JText::_($this->textPrefix . '_ERROR_INVALID_ITEM_TYPE'),
                $this->debugType,
                $transaction
            );
            return null;
        }

        return $transaction;
    }

    /**
     * Save transaction.
     *
     * @param stdClass $item
     * @param array $data
     * @param bool $testMode
     *
     * @throws \RuntimeException
     *
     * @return null|Virtualcurrency\Transaction\Transaction
     */
    protected function storeTransaction($item, $data, $testMode)
    {
        // Get transaction by txn ID
        $keys        = array(
            'txn_id' => Joomla\Utilities\ArrayHelper::getValue($data, 'txn_id')
        );
        $transaction = new Virtualcurrency\Transaction\Transaction(JFactory::getDbo());
        $transaction->load($keys);

        // DEBUG DATA
        JDEBUG ? $this->log->add(JText::_($this->textPrefix . '_DEBUG_TRANSACTION_OBJECT'), $this->debugType, $transaction->getProperties()) : null;

        // Check for existed transaction.
        // If the current status if completed, stop the process.
        if ($transaction->getId() and $transaction->isCompleted()) {
            throw new \RuntimeException('Transaction has been completed.');
        }

        // Get the account from which we will have to get units.
        $userId       = Joomla\Utilities\ArrayHelper::getValue($data, 'receiver_id');
        $currencyId   = Joomla\Utilities\ArrayHelper::getValue($item->order['virtual'], 'currency_id');

        $account      = new Virtualcurrency\Account\Account(JFactory::getDbo());
        $account->load(array('user_id' => $userId, 'currency_id' => $currencyId));

        // DEBUG DATA
        JDEBUG ? $this->log->add(JText::_($this->textPrefix . '_DEBUG_ACCOUNT_OBJECT'), $this->debugType, $account->getProperties()) : null;

        // Check for valid project
        if (!$account->getId()) {
            $this->log->add(
                JText::_($this->textPrefix . '_ERROR_INVALID_ACCOUNT'),
                $this->debugType,
                $data
            );

            throw new \RuntimeException('Invalid account.');
        }

        // Check if user account contains enough units.
        if ($data['txn_amount'] > $account->getAmount()) {
            throw new \RuntimeException('The amount of the transaction is greater than the available in the account of the user.');
        } else {
            $data['txn_status'] = 'completed';
        }

        // DEBUG DATA
        JDEBUG ? $this->log->add(JText::_($this->textPrefix . '_DEBUG_TRANSACTION_OBJECT'), $this->debugType, $data) : null;

        if ($testMode) {
            throw new \RuntimeException('Transaction in test mode.');
        }

        // Start database transaction.
        $db = JFactory::getDbo();
        $db->transactionStart();

        try {

            // Store the new transaction data.
            $transaction->bind($data);
            $transaction->store();

            // If it is not completed (it might be pending or other status),
            // stop the process. Only completed transaction will continue
            // and will process the units.
            if (!$transaction->isCompleted()) {
                return null;
            }

            // Decrease the amount in user's account.
            $account->decreaseAmount($transaction->getTransactionAmount());
            $account->storeAmount();

            // Increase units.
            if (strcmp('currency', $transaction->getItemType()) === 0) {

                $account = new Virtualcurrency\Account\Account(JFactory::getDbo());
                $account->load(array('user_id' => $userId, 'currency_id' => $item->id));

                $account->increaseAmount($transaction->getUnits());
                $account->storeAmount();

            } else {

                $commodity = new Virtualcurrency\User\Commodity(JFactory::getDbo());
                $commodity->load(array('user_id' => $userId, 'commodity_id' => $item->id));

                $commodity->increaseNumber($transaction->getUnits());
                $commodity->storeNumber();
            }

            $db->transactionCommit();

        } catch (Exception $e) {

            $db->transactionRollback();
        }

        return $transaction;
    }
}
