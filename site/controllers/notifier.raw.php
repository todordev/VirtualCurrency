<?php
/**
 * @package      Virtual Currency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * @package        VirtualCurrency
 * @subpackage     Components
 * @since          2.5
 */
class VirtualCurrencyControllerNotifier extends JControllerLegacy
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return    object    The model.
     * @since    1.5
     */
    public function getModel($name = 'Notifier', $prefix = '', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    /**
     * Catch the response from PayPal and store data about transaction/
     *
     * @todo Move sending mail functionality to plugins.
     */
    public function notify()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite * */

        $params = $app->getParams("com_virtualcurrency");

        // Check for disabled payment functionality
        if ($params->get("debug_payment_disabled", 0)) {
            $error = JText::_("COM_VIRTUALCURRENCY_ERROR_PAYMENT_HAS_BEEN_DISABLED");
            $error .= "\n" . JText::sprintf("COM_VIRTUALCURRENCY_TRANSACTION_DATA", var_export($_POST, true));
            JLog::add($error);

            return null;
        }

        // Clear the name of the payment gateway.
        $filter         = new JFilterInput();
        $paymentService = $filter->clean(JString::trim(JString::strtolower($this->input->getCmd("payment_service"))));

        $context        = (!empty($paymentService)) ? 'com_virtualcurrency.notify.' . $paymentService : 'com_crowdfunding.notify';

        // Save data
        try {

            // Events
            $dispatcher = JEventDispatcher::getInstance();

            // Event Notify
            JPluginHelper::importPlugin('virtualcurrencypayment');
            $results = $dispatcher->trigger('onPaymenNotify', array($context, &$params));

            $transaction    = null;
            $currency       = null;

            if (!empty($results)) {
                foreach ($results as $result) {
                    if (!empty($result) and isset($result["transaction"])) {
                        $transaction    = JArrayHelper::getValue($result, "transaction");
                        $currency       = JArrayHelper::getValue($result, "currency");
                        break;
                    }
                }
            }

            // If there is no transaction data, the status might be pending or another one.
            // So, we have to stop the script execution.
            if (empty($transaction)) {
                return;
            }

            // Event After Payment
            $dispatcher->trigger('onAfterPayment', array($context, &$transaction, &$params, &$currency));

        } catch (Exception $e) {

            JLog::add($e->getMessage());
            $input = "INPUT:" . var_export($app->input, true) . "\n";
            JLog::add($input);

            // Send notification about the error to the administrator.
            $model = $this->getModel();
            $model->sendMailToAdministrator();
        }
    }
}
