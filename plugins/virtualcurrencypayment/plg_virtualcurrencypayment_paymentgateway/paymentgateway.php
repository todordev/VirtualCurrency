<?php
/**
 * @package         VirtualCurrency
 * @subpackage      Plugins
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

use Virtualcurrency\Cart\Item as CartItem;
use Prism\Payment\Result as PaymentResult;
use Virtualcurrency\Payment\Session\Session as PaymentSession;
use Joomla\Utilities\ArrayHelper;
use Virtualcurrency\Transaction\Transaction;
use Virtualcurrency\Account\Account;

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
        $this->textPrefix     .= '_' . strtoupper($this->serviceAlias);
        $this->debugType      .= '_' . strtoupper($this->serviceAlias);
    }

    /**
     * Display payment form.
     *
     * @param string                   $context
     * @param stdClass                 $item
     * @param Joomla\Registry\Registry $params Component options.
     *
     * @return null|string
     */
    public function onPreparePayment($context, &$item, &$params)
    {
        if (strcmp('com_virtualcurrency.payment.prepare', $context) !== 0) {
            return null;
        }

        if (!isset($item->order)) {
            return null;
        }

        // The plugin can only be used for payment via real currency.
        $currencyType = ($item->order instanceof CartItem) ? $item->order->getCurrencyType() : '';
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

        $virtualCurrencyData = $item->order->price('virtual');
        if (!$virtualCurrencyData->getCurrencyId()) {
            return null;
        }

        // Get the virtual currency that will be used for payment.
        $mapper       = new Virtualcurrency\Currency\Mapper(new Virtualcurrency\Currency\Gateway\JoomlaGateway(JFactory::getDbo()));
        $repository   = new Virtualcurrency\Currency\Repository($mapper);
        $currency     = $repository->fetchById($virtualCurrencyData->getCurrencyId());
        if (!$currency->getId()) {
            return null;
        }

        $userId   = JFactory::getUser()->get('id');
        if (!$userId) {
            return null;
        }

        $mapper     = new Virtualcurrency\Account\Mapper(new Virtualcurrency\Account\Gateway\JoomlaGateway(JFactory::getDbo()));
        $repository = new Virtualcurrency\Account\Repository($mapper);
        $account    = $repository->fetch(['user_id' => $userId, 'currency_id' => $currency->getId()]);
        if (!$account->getId() or !$account->isActive()) {
            return null;
        }

        $totalCost              = $virtualCurrencyData->getTotal();
        $currencyCode           = $virtualCurrencyData->getCurrencyCode();
        $currencyTitle          = htmlentities($currency->getTitle(), ENT_QUOTES, 'UTF-8');
        $numberOfItemsFormatted = htmlentities($item->order->getItemsNumberFormatted(), ENT_QUOTES, 'UTF-8');
        $itemsCostFormatted     = htmlentities($virtualCurrencyData->getTotalFormatted(), ENT_QUOTES, 'UTF-8');

        $componentParams        = JComponentHelper::getParams('com_virtualcurrency');

        $moneyFormatter  = Virtualcurrency\Money\Helper::factory('joomla')->getFormatter();
        /** @var $moneyFormatter \Prism\Money\Formatter  */

        $moneyCurrency   = new Prism\Money\Currency($currency->getProperties());
        $money           = new Prism\Money\Money($account->getAmount(), $moneyCurrency);
        $availableAmount = $moneyFormatter->formatCurrency($money);

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
        $html[] = '<form action="/index.php?option=com_virtualcurrency" method="post">';
        $html[] = '<input type="hidden" name="task" value="payments.checkout" />';
        $html[] = '<input type="hidden" name="service_alias" value="'.$this->serviceAlias.'" />';
        $html[] = JHtml::_('form.token');

        // Prepare button
        $html[] = '<button type="submit" name="submit" class="btn btn-primary mb-10" '.$buttonDisabled.'>'.JText::_($this->textPrefix . '_BUY_NOW').'</button>';

        // End the form.
        $html[] = '</form>';

        // Display a sticky note if the extension works in sandbox mode.
        if ($this->params->get('sandbox', 1)) {
            $html[] = '<div class="alert alert-info mb-0"><span class="fa fa-info-circle"></span> ' . JText::_($this->textPrefix . '_WORKS_SANDBOX') . '</div>';
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
     * @return PaymentResult
     *
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     */
    public function onPaymentsCheckout($context, $item, $params)
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
            $this->log->add(JText::_($this->textPrefix . '_ERROR_INVALID_REQUEST_METHOD'), $this->debugType, JText::sprintf($this->textPrefix . '_ERROR_INVALID_TRANSACTION_REQUEST_METHOD', $requestMethod));
            return null;
        }

        // DEBUG DATA
        JDEBUG ? $this->log->add(JText::_($this->textPrefix . '_DEBUG_RESPONSE'), $this->debugType, $_POST) : null;

        // Prepare output data.
        $paymentResult = new PaymentResult;
        $paymentResult->redirectUrl = VirtualcurrencyHelperRoute::getCartRoute();

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
                return $paymentResult;
            }

            // DEBUG DATA
            JDEBUG ? $this->log->add(JText::_($this->textPrefix . '_DEBUG_VALID_DATA'), $this->debugType, $validData) : null;

            // Get the account from which we will have to get units.
            $payerId      = ArrayHelper::getValue($validData, 'receiver_id');
            $currencyId   = $item->order->price('virtual')->getCurrencyId();

            $accountMapper      = new Virtualcurrency\Account\Mapper(new Virtualcurrency\Account\Gateway\JoomlaGateway(JFactory::getDbo()));
            $accountRepository  = new Virtualcurrency\Account\Repository($accountMapper);
            $account            = $accountRepository->fetch(['user_id' => $payerId, 'currency_id' => $currencyId]);

            // DEBUG DATA
            JDEBUG ? $this->log->add(JText::_($this->textPrefix . '_DEBUG_ACCOUNT_OBJECT'), $this->debugType, $account->getProperties()) : null;

            // Check for valid project
            if (!$account->getId()) {
                $paymentResult->message = JText::_($this->textPrefix . '_ERROR_INVALID_ACCOUNT');
                $this->log->add($paymentResult->message, $this->debugType, $validData);
                return $paymentResult;
            }

            // Check if user account contains enough units.
            if ($validData['txn_amount'] > $account->getAmount()) {
                $paymentResult->message = 'The amount of the transaction is greater than the available in the account of the user.';
                return $paymentResult;
            }

            // Save transaction data.
            // If it is not completed, return empty results.
            // If it is complete, continue with process transaction data
            $transaction = $this->storeTransaction($validData, $account, $this->params->get('sandbox', Prism\Constants::YES));
            if ($transaction !== null) {
                $transaction                = $transaction->getProperties();
                $paymentResult->transaction = ArrayHelper::toObject($transaction);
            }
        } catch (RuntimeException $e) {
            $paymentResult->message  = $e->getMessage();
            return $paymentResult;
        } catch (Exception $e) {
            $paymentResult->message  = $e->getMessage();
            return $paymentResult;
        }

        // Get next URL.
        $paymentResult->redirectUrl = $this->getReturnUrl() ?: VirtualcurrencyHelperRoute::getCartRoute('summary');
        $paymentResult->skipEvent(PaymentResult::EVENT_AFTER_PAYMENT_NOTIFY);
        $paymentResult->skipEvent(PaymentResult::EVENT_AFTER_PAYMENT);

        // Send mails
        $this->sendMails($paymentResult, $params);

        return $paymentResult;
    }

    /**
     * Validate transaction.
     *
     * @param stdClass                        $item
     * @param PaymentSession $paymentSession
     *
     * @return array
     */
    protected function validateData($item, $paymentSession)
    {
        $date    = new JDate();

        // Prepare transaction data
        $transaction = array(
            'title'            => htmlentities($item->title, ENT_QUOTES, 'UTF-8'),
            'units'            => (int)abs($item->order->getItemsNumber()),
            'sender_id'        => Virtualcurrency\Constants::BANK_ID,
            'receiver_id'      => (int)$paymentSession->getUserId(),
            'item_id'          => (int)$item->id,
            'item_type'        => $item->order->getItemType(),
            'service_provider' => $this->serviceProvider,
            'service_alias'    => $this->serviceAlias,
            'txn_id'           => strtoupper(Prism\Utilities\StringHelper::generateRandomString(16)),
            'txn_amount'       => $item->order->price('virtual')->getTotal(),
            'txn_currency'     => $item->order->price('virtual')->getCurrencyCode(),
            'txn_status'       => 'pending',
            'txn_date'         => $date->toSql()
        );

        // Check Currency ID and Transaction ID
        if (!$transaction['item_id'] or !$transaction['receiver_id']) {
            $this->log->add(JText::_($this->textPrefix . '_ERROR_INVALID_TRANSACTION_DATA'), $this->debugType, $transaction);
            return null;
        }

        if (!$transaction['txn_amount'] or !$transaction['txn_currency']) {
            $this->log->add(JText::_($this->textPrefix . '_ERROR_INVALID_AMOUNT_OR_CURRENCY'), $this->debugType, $transaction);
            return null;
        }

        if (!in_array($transaction['item_type'], array('currency', 'commodity'), true)) {
            $this->log->add(JText::_($this->textPrefix . '_ERROR_INVALID_ITEM_TYPE'), $this->debugType, $transaction);
            return null;
        }

        return $transaction;
    }

    /**
     * Save transaction.
     *
     * @param array $data
     * @param Account $account
     * @param bool $testMode
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     *
     * @return null|Transaction
     */
    protected function storeTransaction($data, Account $account, $testMode)
    {
        // Get transaction by txn ID
        $keys        = array(
            'txn_id' => ArrayHelper::getValue($data, 'txn_id')
        );

        $txnMapper      = new Virtualcurrency\Transaction\Mapper(new Virtualcurrency\Transaction\Gateway\JoomlaGateway(JFactory::getDbo()));
        $txnRepository  = new Virtualcurrency\Transaction\Repository($txnMapper);
        $transaction    = $txnRepository->fetch($keys);

        // DEBUG DATA
        JDEBUG ? $this->log->add(JText::_($this->textPrefix . '_DEBUG_TRANSACTION_OBJECT'), $this->debugType, $transaction->getProperties()) : null;

        // Check for existed transaction.
        // If the current status if completed, stop the process.
        if ($transaction->getId() and $transaction->isCompleted()) {
            throw new \RuntimeException('Transaction has been completed.');
        }

        $data['txn_status'] = 'completed';

        // DEBUG DATA
        JDEBUG ? $this->log->add(JText::_($this->textPrefix . '_DEBUG_TRANSACTION_OBJECT'), $this->debugType, $data) : null;
        if ($testMode) {
            return null;
        }

        // Store the new transaction data.
        $transaction->bind($data);

        // Start database transaction.
        $transactionalApp       = new \Virtualcurrency\Transaction\Service\Joomla\CommodityByVirtual($transaction, $account, JFactory::getDbo());
        $transactionalSession   = new \Prism\Database\JoomlaDatabaseSession(JFactory::getDbo());
        $paymentTransaction     = new \Prism\Domain\TransactionalApplicationService($transactionalApp, $transactionalSession);
        $paymentTransaction->execute();

        return $transaction;
    }
}
