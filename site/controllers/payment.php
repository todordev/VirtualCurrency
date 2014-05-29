<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('itprism.controller.form.frontend');

/**
 * VirtualCurrency ordering controller
 *
 * @package     VirtualCurrency
 * @subpackage  Components
 *
 * @todo replace validation functionality with validators objects
 */
class VirtualCurrencyControllerPayment extends ITPrismControllerFormFrontend
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
    public function getModel($name = 'Ordering', $prefix = 'VirtualCurrencyModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    /**
     * Process step 1.
     */
    public function step1()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $userId = JFactory::getUser()->get("id");
        if (!$userId) {
            $redirectOptions = array(
                "force_direction" => "login_form"
            );
            $this->displayNotice(JText::_('COM_VIRTUALCURRENCY_ERROR_NOT_LOG_IN'), $redirectOptions);

            return;
        }

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Get the data from the form
        $itemId = $this->input->post->getInt('id', 0);

        $redirectOptionsError = array(
            "view" => "peyment"
        );

        // Check for maintenance (debug) state
        $params = JComponentHelper::getParams($this->option);
        /** @var $params Joomla\Registry\Registry */

        if ($this->inDebugMode($params)) {
            return;
        }

        // Check terms and use
        if ($params->get("ordering_service_terms", 0)) {

            $terms = $app->input->post->get("terms", 0);
            if (!$terms) {
                $this->displayNotice(JText::_("COM_VIRTUALCURRENCY_ERROR_TERMS_NOT_ACCEPTED"), $redirectOptionsError);
                return;
            }
        }

        // Check for valid number of units.
        $amount = $app->input->post->get("amount", 0, "float");
        if (!$amount) {
            $this->displayNotice(JText::_('COM_VIRTUALCURRENCY_ERROR_INVALID_AMOUNT'), $redirectOptionsError);

            return;
        }

        // Check for valid item
        $item = new VirtualCurrencyCurrency(JFactory::getDbo());
        $item->load($itemId);

        if (!$item->getId()) {
            $this->displayNotice(JText::_('COM_VIRTUALCURRENCY_ERROR_INVALID_CURRENCY'), $redirectOptionsError);
            return;
        }

        // Check for valid allowed items for buying
        if ($amount < $item->getParam("minimum")) {
            $this->displayNotice(JText::_('COM_VIRTUALCURRENCY_ERROR_INVALID_AMOUNT'), $redirectOptionsError);
            return;
        }

        $paymentProcessData = $app->getUserState("payment.data");

        $paymentProcessData["item_id"] = $item->getId();
        $paymentProcessData["amount"] = $amount;
        $paymentProcessData["step1"] = true;

        // Store data to temporary table
        $data = array(
            "user_id"     => $userId,
            "currency_id" => $item->getId(),
            "amount"      => $amount
        );

        jimport("virtualcurrency.payment.session");
        $paymentSession = new VirtualCurrencyPaymentSession(JFactory::getDbo());
        if (!empty($paymentProcessData["payment_id"])) {
            $paymentSession->load($paymentProcessData["payment_id"]);
        }

        $paymentSession->bind($data);
        $paymentSession->store();

        // Remove old payment session records
        $paymentSession->cleanOld();

        $paymentProcessData["payment_id"] = $paymentSession->getId();

        // Set payment data to the sessions
        $app->setUserState("payment.data", $paymentProcessData);

        // Redirect to next page
        $redirectOptions = array(
            "view"   => "payment",
            "layout" => "services"
        );

        $link = $this->prepareRedirectLink($redirectOptions);
        $this->setRedirect(JRoute::_($link, false));
    }

    /**
     * @param Joomla\Registry\Registry $params
     *
     * @return bool
     */
    protected function inDebugMode($params)
    {
        if (!$params->get("debug_payment_disabled", 0)) {
            return false;
        }

        $msg = JString::trim($params->get("debug_disabled_functionality_msg"));
        if (!$msg) {
            $msg = JText::_("COM_VIRTUALCURRENCY_DEBUG_MODE_DEFAULT_MSG");
        }

        $redirectOptions = array(
            "view" => "payment"
        );

        $this->displayNotice($msg, $redirectOptions);

        return true;
    }
}
