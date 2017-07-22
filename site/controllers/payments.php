<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\String\StringHelper;
use Prism\Payment\Result as PaymentResult;

// no direct access
defined('_JEXEC') or die;

/**
 * This controller provides functionality
 * that helps to payment plugins to prepare their payment data.
 *
 * @package        Virtualcurrency
 * @subpackage     Payments
 */
class VirtualcurrencyControllerPayments extends JControllerLegacy
{
    protected $log;

    protected $serviceAlias;

    protected $itemId;
    protected $itemType;

    protected $app;

    /**
     * Tasks that needs form token.
     *
     * @var array
     */
    protected $tokenTasks = array('checkout');

    protected $logFile    = 'com_virtualcurrency.php';

    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $file = JPath::clean($this->app->get('log_path') . DIRECTORY_SEPARATOR . $this->logFile);

        $this->log = new Prism\Log\Log();
        $this->log->addAdapter(new Prism\Log\Adapter\File($file));

        // Get project id.
        $this->itemId   = $this->input->getUint('item_id');

        // Get the service alias.
        $this->serviceAlias = $this->input->getCmd('service_alias', '');

        // Local executing tasks. It needs to provide form token.
        $this->registerTask('checkout', 'process');

        // Remote executing tasks. It does not need to provide form token.
        $this->registerTask('doCheckout', 'process');
        $this->registerTask('completeCheckout', 'process');
    }

    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return    VirtualcurrencyModelPayments|bool    The model.
     * @since    1.5
     */
    public function getModel($name = 'Payments', $prefix = 'VirtualcurrencyModel', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }

    /**
     * Task used for user authorization in their payment gateways.
     *
     * @throws Exception
     */
    public function authorize()
    {
        // Get component parameters
        $params = JComponentHelper::getParams('com_virtualcurrency');
        /** @var  $params Joomla\Registry\Registry */

        // Check for disabled payment functionality
        if ($params->get('debug_payment_disabled', 0)) {
            throw new Exception(JText::_('COM_VIRTUALCURRENCY_ERROR_PAYMENT_HAS_BEEN_DISABLED_MESSAGE'));
        }

        // Check payment service alias.
        if (!$this->serviceAlias) {
            throw new UnexpectedValueException(JText::_('COM_VIRTUALCURRENCY_ERROR_INVALID_PAYMENT_GATEWAY'));
        }

        $redirectUrl = null;
        $message     = null;

        // Trigger the event
        try {
            $context = 'com_virtualcurrency.payments.authorize.' . StringHelper::strtolower($this->serviceAlias);

            // Import Virtualcurrency Payment Plugins
            $dispatcher = JEventDispatcher::getInstance();
            JPluginHelper::importPlugin('virtualcurrencypayment');

            // Trigger the event.
            $results = $dispatcher->trigger('onPaymentsAuthorize', array($context, &$params));

            // Get the result, that comes from the plugin.
            if (is_array($results) and count($results) > 0) {
                foreach ($results as $result) {
                    if (is_object($result) and ($result instanceof PaymentResult) and $result->transaction !== null) {
                        $redirectUrl   = $result->redirectUrl ?: null;
                        $message       = $result->message ?: null;
                        break;
                    }
                }
            }

        } catch (UnexpectedValueException $e) {
            $this->setMessage($e->getMessage(), 'notice');
            $this->setRedirect(JRoute::_('index.php', false));
            return;

        } catch (Exception $e) {
            // Store log data in the database
            $this->log->add(JText::_('COM_VIRTUALCURRENCY_ERROR_SYSTEM'), 'CONTROLLER_PAYMENTS_DOCHECKOUT_ERROR', $e->getMessage());

            throw new Exception(JText::_('COM_VIRTUALCURRENCY_ERROR_SYSTEM'));
        }

        if (!$redirectUrl) {
            throw new UnexpectedValueException(JText::_('COM_VIRTUALCURRENCY_ERROR_INVALID_REDIRECT_URL'));
        }

        if (!$message) {
            $this->setRedirect($redirectUrl);
        } else {
            $this->setRedirect($redirectUrl, $message, 'notice');
        }
    }

    /**
     * Process action triggering an event that comes from remote server.
     *
     * Actions:
     * * authorize - Authorize or obtain access token from payment gateways.
     * * doCheckout - Authorize or obtain access token from payment gateways.
     *
     * @throws Exception
     */
    public function process()
    {
        // Get the task.
        $task    = StringHelper::strtolower($this->input->getCmd('task'));
        if (!$task) {
            throw new Exception(JText::_('COM_VIRTUALCURRENCY_ERROR_INVALID_TASK'));
        }

        // Check for request forgeries.
        if (in_array($task, $this->tokenTasks, true)) {
            JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        }

        // Get component parameters
        $params = JComponentHelper::getParams('com_virtualcurrency');
        /** @var  $params Joomla\Registry\Registry */

        // Check for disabled payment functionality
        if ($params->get('debug_payment_disabled', 0)) {
            throw new Exception(JText::_('COM_VIRTUALCURRENCY_ERROR_PAYMENT_HAS_BEEN_DISABLED_MESSAGE'));
        }

        // Check payment service alias.
        if (!$this->serviceAlias) {
            throw new UnexpectedValueException(JText::_('COM_VIRTUALCURRENCY_ERROR_INVALID_PAYMENT_GATEWAY'));
        }

        $paymentResult  = null;
        $redirectUrl    = null;
        $message        = null;

        $model   = $this->getModel();

        // Trigger the event
        try {
            // Prepare project object.
            // Create an object that contains a data used during the payment process.
            $cartSessionContext = Virtualcurrency\Constants::PAYMENT_SESSION_CONTEXT;
            $cartSession        = $this->app->getUserState($cartSessionContext);

            $item    = $model->prepareItem($cartSession, $params);

            $context = 'com_virtualcurrency.payments.'.$task.'.' . StringHelper::strtolower($this->serviceAlias);

            // Import Virtualcurrency Payment Plugins
            $dispatcher = JEventDispatcher::getInstance();
            JPluginHelper::importPlugin('virtualcurrencypayment');

            // Trigger the event.
            $results = $dispatcher->trigger('onPayments'. StringHelper::ucwords($task), array($context, &$item, &$params));

            // Get the result, that comes from the plugin.
            if (is_array($results) and count($results) > 0) {
                foreach ($results as $result) {
                    if (is_object($result) and ($result instanceof PaymentResult)) {
                        $paymentResult = $result;
                        $redirectUrl   = $result->redirectUrl ?: null;
                        $message       = $result->message ?: null;
                        break;
                    }
                }
            }

            // Trigger the event onAfterPaymentNotify
            if ($paymentResult !== null and $paymentResult->isEventActive(PaymentResult::EVENT_AFTER_PAYMENT_NOTIFY)) {
                $dispatcher->trigger('onAfterPaymentNotify', array($context, &$paymentResult, &$params));
            }

            // Trigger the event onAfterPayment
            if ($paymentResult !== null and $paymentResult->isEventActive(PaymentResult::EVENT_AFTER_PAYMENT)) {
                $dispatcher->trigger('onAfterPayment', array($context, &$paymentResult, &$params));
            }

        } catch (UnexpectedValueException $e) {
            $this->setMessage($e->getMessage(), 'notice');
            $this->setRedirect(JRoute::_('index.php', false));
            return;

        } catch (Exception $e) {
            $this->log->add(JText::_('COM_VIRTUALCURRENCY_ERROR_SYSTEM'), 'CONTROLLER_PAYMENTS_DOCHECKOUT_ERROR', $e->getMessage());

            throw new Exception(JText::_('COM_VIRTUALCURRENCY_ERROR_SYSTEM'));
        }

        if (!$redirectUrl) {
            throw new UnexpectedValueException(JText::_('COM_VIRTUALCURRENCY_ERROR_INVALID_REDIRECT_URL'));
        }

        if (!$message) {
            $this->setRedirect($redirectUrl);
        } else {
            $this->setRedirect($redirectUrl, $message, 'warning');
        }
    }
}
