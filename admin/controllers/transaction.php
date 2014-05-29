<?php
/**
 * @package      Virtual Currency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('itprism.controller.form.backend');

/**
 * VirtualCurrency Transaction controller class.
 *
 * @package        VirtualCurrency
 * @subpackage     Components
 * @since          1.6
 */
class VirtualCurrencyControllerTransaction extends ITPrismControllerFormBackend
{
    /**
     * Save an item
     */
    public function save($key = null, $urlVar = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app = JFactory::getApplication();
        /** @var $app JApplicationAdministrator * */

        $data   = $app->input->post->get('jform', array(), 'array');
        $itemId = JArrayHelper::getValue($data, "id");

        // Prepare return data
        $redirectOptions = array(
            "task" => $this->getTask(),
            "id"   => $itemId
        );

        $model = $this->getModel();
        /** @var $model VirtualCurrencyModelTransaction * */

        $form = $model->getForm($data, false);
        /** @var $form JForm * */

        if (!$form) {
            throw new Exception(JText::_("COM_VIRTUALCURRENCY_ERROR_FORM_CANNOT_BE_LOADED"), 500);
        }

        // Validate the form
        $validData = $model->validate($form, $data);

        // Check for errors.
        if ($validData === false) {
            $this->displayNotice($form->getErrors(), $redirectOptions);

            return;
        }

        try {

            $itemId = $model->save($validData);

            // Prepare return data
            $redirectOptions["id"] = $itemId;

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_VIRTUALCURRENCY_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_("COM_VIRTUALCURRENCY_TRANSACTION_SAVED"), $redirectOptions);
    }
}
