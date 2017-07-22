<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\Utilities\ArrayHelper;
use Joomla\Data\DataObject;
use Joomla\String\StringHelper;

// no direct access
defined('_JEXEC') or die;

/**
 * This controller provides functionality
 * that helps to payment plugins to prepare their data.
 *
 * @package        Virtualcurrency
 * @subpackage     Payments
 */
class VirtualcurrencyControllerPayments extends JControllerLegacy
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return   VirtualcurrencyModelPayments    The model.
     * @since    1.5
     */
    public function getModel($name = 'Payments', $prefix = 'VirtualcurrencyModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    /**
     * This method trigger the event onPaymentsPreparePayment.
     * The purpose of this method is to load a data and send it to browser.
     * That data will be used in the process of payment.
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function preparePaymentAjax()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Get component parameters
        $params = JComponentHelper::getParams('com_virtualcurrency');
        /** @var  $params Joomla\Registry\Registry */

        $response = new Prism\Response\Json();

        // Check for disabled payment functionality
        if ($params->get('debug_payment_disabled', 0)) {
            // Send response to the browser
            $response
                ->setTitle(JText::_('COM_VIRTUALCURRENCY_FAIL'))
                ->setContent(JText::_('COM_VIRTUALCURRENCY_ERROR_PAYMENT_HAS_BEEN_DISABLED_MESSAGE'))
                ->failure();

            echo $response;
            $app->close();
        }

        $output         = array();

        // Prepare payment service name.
        $filter         = new JFilterInput();
        $paymentService = StringHelper::trim(StringHelper::strtolower($this->input->getCmd('payment_service')));
        $paymentService = $filter->clean($paymentService, 'ALNUM');

        // Trigger the event
        try {
            $context = 'com_virtualcurrency.preparepayment.' . $paymentService;

            // Import Virtualcurrency Payment Plugins
            $dispatcher = JEventDispatcher::getInstance();
            JPluginHelper::importPlugin('virtualcurrencypayment');

            // Trigger onContentPreparePayment event.
            $results = $dispatcher->trigger('onPaymentsPreparePayment', array($context, &$params));

            // Get the result, that comes from the plugin.
            if (is_array($results) and count($results) > 0) {
                foreach ($results as $result) {
                    if ($result !== null and is_array($result)) {
                        $output = $result;
                        break;
                    }
                }
            }

        } catch (Exception $e) {
            // Store log data in the database
            JLog::add($e->getMessage());

            // Send response to the browser
            $response
                ->failure()
                ->setTitle(JText::_('COM_VIRTUALCURRENCY_FAIL'))
                ->setContent(JText::_('COM_VIRTUALCURRENCY_ERROR_SYSTEM'));

            echo $response;
            $app->close();
        }

        // Check the response
        $success = ArrayHelper::getValue($output, 'success');
        if (!$success) { // If there is an error...
            $paymentProcessContext = Virtualcurrency\Constants::PAYMENT_SESSION_CONTEXT;

            // Initialize the payment process object.
            $paymentProcess        = new DataObject();
            $paymentProcess->step1 = false;
            $app->setUserState($paymentProcessContext, $paymentProcess);

            // Send response to the browser
            $response
                ->failure()
                ->setTitle(ArrayHelper::getValue($output, 'title'))
                ->setContent(ArrayHelper::getValue($output, 'text'));
        } else { // If all is OK...
            $response
                ->success()
                ->setTitle(ArrayHelper::getValue($output, 'title'))
                ->setContent(ArrayHelper::getValue($output, 'text'))
                ->setData(ArrayHelper::getValue($output, 'data'));
        }

        echo $response;
        $app->close();
    }
}
