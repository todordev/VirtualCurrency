<?php
/**
 * @package      Virtual Currency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Virtualcurrency Currency controller class.
 *
 * @package        Virtualcurrency
 * @subpackage     Components
 * @since          1.6
 */
class VirtualcurrencyControllerCurrency extends Prism\Controller\Form\Backend
{
    /**
     * @param string $name
     * @param string $prefix
     * @param array  $config
     *
     * @return VirtualcurrencyModelCurrency
     */
    public function getModel($name = 'Currency', $prefix = 'VirtualcurrencyModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    public function save($key = null, $urlVar = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $data   = $this->input->post->get('jform', array(), 'array');
        $itemId = Joomla\Utilities\ArrayHelper::getValue($data, 'id');

        $redirectOptions = array(
            'task' => $this->getTask(),
            'id'   => $itemId
        );

        $model = $this->getModel();
        /** @var $model VirtualcurrencyModelCurrency */

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

        try {
            // Get files from form.
            $files = $this->input->files->get('jform', array(), 'array');

            // Upload image
            $file = Joomla\Utilities\ArrayHelper::getValue($files, 'image');
            if (!empty($file['name'])) {
                $validData['image'] = $model->uploadImage($file, 'image');
            }

            // Upload icon
            $file = Joomla\Utilities\ArrayHelper::getValue($files, 'icon');
            if (!empty($file['name'])) {
                $validData['image_icon'] = $model->uploadImage($file, 'icon');
            }

            $itemId = $model->save($validData);

            $redirectOptions['id'] = $itemId;

        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_virtualcurrency');
            throw new Exception(JText::_('COM_VIRTUALCURRENCY_ERROR_SYSTEM'));

        }

        $this->displayMessage(JText::_('COM_VIRTUALCURRENCY_CURRENCY_SAVED'), $redirectOptions);
    }

    /**
     * Delete image
     */
    public function removeImage()
    {
        // Check for request forgeries.
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        $itemId = $this->input->get->getUint('id');
        $type   = $this->input->get->getCmd('type');

        $redirectOptions = array(
            'view' => 'currency',
            'id'   => $itemId
        );

        try {
            $params = JComponentHelper::getParams('com_virtualcurrency');

            $mediaFolder = JPath::clean(JPATH_ROOT .'/'. $params->get('media_folder', 'images/virtualcurrency'));

            $model = $this->getModel();
            $model->removeImage($itemId, $mediaFolder, $type);

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_VIRTUALCURRENCY_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_VIRTUALCURRENCY_IMAGE_DELETED'), $redirectOptions);
    }
}
