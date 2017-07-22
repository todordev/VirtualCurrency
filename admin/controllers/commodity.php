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
 * Virtualcurrency Commodity controller class.
 *
 * @package        Virtualcurrency
 * @subpackage     Components
 * @since          1.6
 */
class VirtualcurrencyControllerCommodity extends Prism\Controller\Form\Backend
{
    /**
     * @param string $name
     * @param string $prefix
     * @param array  $config
     *
     * @return VirtualcurrencyModelCommodity
     */
    public function getModel($name = 'Commodity', $prefix = 'VirtualcurrencyModel', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
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
        /** @var $model VirtualcurrencyModelCommodity */

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
            // Upload image
            $file = $this->input->files->get('jform', array(), 'array');
            $file = Joomla\Utilities\ArrayHelper::getValue($file, 'image');
            if (!empty($file['name'])) {
                $filename = $model->uploadImage($file, 'image');
                $validData['image'] = $filename;
            }

            // Upload icon.
            $file = $this->input->files->get('jform', array(), 'array');
            $file = Joomla\Utilities\ArrayHelper::getValue($file, 'icon');
            if (!empty($file['name'])) {
                $filename = $model->uploadImage($file, 'icon');
                $validData['image_icon'] = $filename;
            }

            $itemId = $model->save($validData);

            $redirectOptions['id'] = $itemId;
        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_virtualcurrency');
            throw new Exception(JText::_('COM_VIRTUALCURRENCY_ERROR_SYSTEM'));

        }

        $this->displayMessage(JText::_('COM_VIRTUALCURRENCY_PRODUCT_SAVED'), $redirectOptions);
    }

    /**
     * Delete image
     */
    public function removeImage()
    {
        // Check for request forgeries.
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        $itemId = $this->input->getInt('id', 0);
        $type   = $this->input->getCmd('type');

        $redirectOptions = array(
            'view' => 'commodity',
            'id'   => $itemId
        );

        try {
            $params = JComponentHelper::getParams('com_virtualcurrency');

            $mediaFolder = JPath::clean(JPATH_ROOT .'/'. $params->get('media_folder', 'images/virtualcurrency'));

            $model = $this->getModel();
            $model->removeImage($itemId, $mediaFolder, $type);
        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_virtualcurrency');
            throw new Exception(JText::_('COM_VIRTUALCURRENCY_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_VIRTUALCURRENCY_IMAGE_DELETED'), $redirectOptions);
    }
}
