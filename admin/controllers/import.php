<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Virtualcurrency import controller.
 *
 * @package      Virtualcurrency
 * @subpackage   Components
 */
class VirtualcurrencyControllerImport extends Prism\Controller\Form\Backend
{
    public function getModel($name = 'Import', $prefix = 'VirtualcurrencyModel', $config = array('ignore_request' => false))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function realCurrencies()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $data = $this->input->post->get('jform', array(), 'array');
        $file = $this->input->files->get('jform', array(), 'array');
        $data = array_merge($data, $file);

        $redirectOptions = array(
            'view' => 'realcurrencies'
        );

        $model = $this->getModel();
        /** @var $model VirtualcurrencyModelImport */

        $form = $model->getForm($data, false);
        /** @var $form JForm */

        if (!$form) {
            throw new Exception(JText::_('COM_VIRTUALCURRENCY_ERROR_FORM_CANNOT_BE_LOADED'));
        }

        // Validate the form
        $validData = $model->validate($form, $data);

        // Check for errors.
        if ($validData === false) {
            $this->displayNotice($form->getErrors(), $redirectOptions);
            return;
        }

        $fileData = Joomla\Utilities\ArrayHelper::getValue($data, 'data');
        if (!$fileData or empty($fileData['name'])) {
            $this->displayNotice(JText::_('COM_VIRTUALCURRENCY_ERROR_FILE_CANT_BE_UPLOADED'), $redirectOptions);
            return;
        }

        try {
            $filePath  = $model->uploadFile($fileData, 'currencies');

            $resetId   = Joomla\Utilities\ArrayHelper::getValue($data, 'reset_id', false, 'bool');
            $removeOld = Joomla\Utilities\ArrayHelper::getValue($data, 'remove_old', false, 'bool');
            if ($removeOld) {
                $model->removeAll();
            }

            $model->importCurrencies($filePath, $resetId);
        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_virtualcurrency');
            throw new Exception(JText::_('COM_VIRTUALCURRENCY_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_VIRTUALCURRENCY_REAL_CURRENCIES_IMPORTED'), $redirectOptions);
    }
}
