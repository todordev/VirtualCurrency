<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * VirtualCurrency cart controller
 *
 * @package     VirtualCurrency
 * @subpackage  Components
 */
class VirtualcurrencyControllerCart extends Prism\Controller\Form\Frontend
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return   VirtualCurrencyModelCart    The model.
     * @since    1.5
     */
    public function getModel($name = 'Cart', $prefix = 'VirtualcurrencyModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * Process step 1.
     */
    public function process()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $userId = JFactory::getUser()->get('id');
        if (!$userId) {
            $redirectOptions = array(
                'force_direction' => 'login_form'
            );
            $this->displayNotice(JText::_('COM_VIRTUALCURRENCY_ERROR_NOT_LOG_IN'), $redirectOptions);
            return;
        }

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $redirectOptions = array(
            'view' => 'cart'
        );

        // Check for maintenance (debug) state
        $params = JComponentHelper::getParams($this->option);
        /** @var $params Joomla\Registry\Registry */

        if ($params->get('debug_payment_disabled', 0)) {
            $msg = trim($params->get('debug_disabled_functionality_msg'));
            if (!$msg) {
                $msg = JText::_('COM_VIRTUALCURRENCY_DEBUG_MODE_DEFAULT_MSG');
            }

            $this->displayWarning($msg, $redirectOptions);
            return;
        }

        // Check terms and use
        if ($params->get('payments_service_terms', 0) and !$this->input->post->getInt('terms', 0)) {
            $this->displayWarning(JText::_('COM_VIRTUALCURRENCY_ERROR_TERMS_NOT_ACCEPTED'), $redirectOptions);
            return;
        }

        // Check for valid number of units.
        $numberOfItems = (int)abs($app->input->post->get('amount', 0, 'int'));
        if (!$numberOfItems) {
            $this->displayWarning(JText::_('COM_VIRTUALCURRENCY_ERROR_INVALID_UNITS_NUMBER'), $redirectOptions);
            return;
        }

        $itemType = $this->input->post->getCmd('type');
        if (!$itemType or !in_array($itemType, array('currency', 'commodity'), true)) {
            $this->displayError(JText::_('COM_VIRTUALCURRENCY_ERROR_INVALID_ITEM_TYPE'), $redirectOptions);
            return;
        }

        $itemId = $this->input->post->getInt('id', 0);
        if (!$itemId) {
            $this->displayError(JText::_('COM_VIRTUALCURRENCY_ERROR_INVALID_ITEM'), $redirectOptions);
            return;
        }

        // Get the item.
        if (strcmp('currency', $itemType) === 0) {
            $item = new Virtualcurrency\Currency\Currency(JFactory::getDbo());
            $item->load($itemId);
        } else { // Commodity
            $item = new Virtualcurrency\Commodity\Commodity(JFactory::getDbo());
            $item->load($itemId);
        }

        if (!$item->getId()) {
            $this->displayError(JText::_('COM_VIRTUALCURRENCY_ERROR_INVALID_ITEM'), $redirectOptions);
            return;
        }

        // Check for valid allowed items for buying
        $minimum = (int)$item->getParam('minimum');
        if (strcmp('currency', $itemType) === 0) {
            if ((int)$numberOfItems < $minimum) {
                $this->displayWarning(JText::sprintf('COM_VIRTUALCURRENCY_ERROR_LESS_THAN_MINIMUM_D', $minimum), $redirectOptions);
                return;
            }
        } else {// Commodity
            if ((int)$numberOfItems < $minimum) {
                $this->displayWarning(JText::sprintf('COM_VIRTUALCURRENCY_ERROR_LESS_THAN_MINIMUM_D', $minimum), $redirectOptions);
                return;
            }

            // Check the number of available units.
            $inStock = $item->getInStock();
            if (is_numeric($inStock) and (int)$inStock < (int)$numberOfItems) {
                $this->displayWarning(JText::sprintf('COM_VIRTUALCURRENCY_ERROR_IN_STOCK_EXCEEDED', (int)$inStock), $redirectOptions);
                return;
            }
        }

        $cartSession = $app->getUserState(Virtualcurrency\Constants::PAYMENT_SESSION_CONTEXT);

        $cartSession->item_id      = $item->getId();
        $cartSession->item_type    = $itemType;
        $cartSession->items_number = $numberOfItems;
        $cartSession->step1        = true;

        // Store data to payment session table.
        $data = array(
            'user_id'       => $userId,
            'item_id'       => $item->getId(),
            'item_type'     => $itemType,
            'items_number'  => $numberOfItems,
            'session_id'    => $cartSession->session_id
        );

        $paymentSession = new Virtualcurrency\Payment\Session(JFactory::getDbo());
        if ($cartSession->payment_id) {
            $paymentSession->load($cartSession->payment_id);
        }

        $paymentSession->bind($data);
        $paymentSession->store();

        // Remove old payment session records
        $paymentSession->cleanOld();

        $cartSession->payment_id = $paymentSession->getId();

        // Set payment data to the sessions
        $app->setUserState(Virtualcurrency\Constants::PAYMENT_SESSION_CONTEXT, $cartSession);

        $this->setRedirect(JRoute::_(VirtualcurrencyHelperRoute::getCartRoute('payment'), false));
    }
}
