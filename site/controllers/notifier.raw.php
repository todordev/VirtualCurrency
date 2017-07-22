<?php
/**
 * @package      Virtual Currency
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
 * @package        VirtualCurrency
 * @subpackage     Components
 * @since          2.5
 */
class VirtualcurrencyControllerNotifier extends JControllerLegacy
{
    protected $log;

    protected $paymentProcess;

    protected $projectId;
    protected $context;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $params;

    /**
     * @var JApplicationSite
     */
    protected $app;
    
    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->app = JFactory::getApplication();

        // Prepare log object
        $file = JPath::clean($this->app->get('log_path') . DIRECTORY_SEPARATOR . 'com_virtualcurrency.php');

        $this->log = new Prism\Log\Log();
        $this->log->addAdapter(new Prism\Log\Adapter\File($file));

        // Create an object that contains a data used during the payment process.
        $this->paymentProcess = $this->app->getUserState(Virtualcurrency\Constants::PAYMENT_SESSION_CONTEXT);

        // Prepare context
        $filter         = new JFilterInput();
        $paymentService = StringHelper::trim(StringHelper::strtolower($this->input->getCmd('payment_service')));
        $paymentService = $filter->clean($paymentService, 'ALNUM');

        $this->context  = $paymentService ? 'com_virtualcurrency.notify.' . $paymentService : 'com_virtualcurrency.notify';

        // Prepare params
        $this->params = JComponentHelper::getParams('com_virtualcurrency');
    }
    
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return   VirtualCurrencyModelNotifier    The model.
     * @since    1.5
     */
    public function getModel($name = 'Notifier', $prefix = 'VirtualcurrencyModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * Catch the response from PayPal and store data about transaction.
     */
    public function notify()
    {
        // Check for disabled payment functionality
        if ($this->params->get('debug_payment_disabled', 0)) {
            $error = JText::_('COM_VIRTUALCURRENCY_ERROR_PAYMENT_HAS_BEEN_DISABLED');
            $error .= "\n" . JText::sprintf('COM_VIRTUALCURRENCY_TRANSACTION_DATA', var_export($_REQUEST, true));
            $this->log->add($error, 'CONTROLLER_NOTIFIER_ERROR');
            return;
        }

        $paymentResult      = null;
        $responseToService  = null;

        // Save data
        try {
            // Events
            $dispatcher = JEventDispatcher::getInstance();

            // Event Notify
            JPluginHelper::importPlugin('virtualcurrencypayment');
            $results = $dispatcher->trigger('onPaymentNotify', array($this->context, &$this->params));

            if (is_array($results) and count($results) > 0) {
                foreach ($results as $result) {
                    if (is_object($result) and ($result instanceof PaymentResult)) {
                        $paymentResult      = $result;
                        $responseToService  = $result->response ?: null;
                        break;
                    }
                }
            }

            // If there is no transaction data, the status might be pending or another one.
            // So, we have to stop the script execution.
            if (!$paymentResult) {
                return;
            }

            // Trigger the event onAfterPaymentNotify
            if ($paymentResult->isEventActive(PaymentResult::EVENT_AFTER_PAYMENT_NOTIFY)) {
                $dispatcher->trigger('onAfterPaymentNotify', array($this->context, &$paymentResult, &$this->params));
            }

            // Trigger the event onAfterPayment
            if ($paymentResult->isEventActive(PaymentResult::EVENT_AFTER_PAYMENT)) {
                $dispatcher->trigger('onAfterPayment', array($this->context, &$paymentResult, &$this->params));
            }

        } catch (Exception $e) {
            $error     = 'NOTIFIER ERROR: ' .$e->getMessage() .'\n';
            $errorData = 'INPUT:' . var_export($this->app->input, true) . '\n';
            $this->log->add($error, 'CONTROLLER_NOTIFIER_ERROR', $errorData);

            // Send notification about the error to the administrator.
            $model = $this->getModel();
            $model->sendMailToAdministrator();
        }

        // Send a specific response to a payment service.
        if (is_string($responseToService) and $responseToService !== '') {
            echo $responseToService;
        }

        // Stop the execution of the script.
        $this->app->close();
    }
}
