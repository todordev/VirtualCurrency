<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Commodities
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Virtualcurrency commodities controller
 *
 * @package     Virtualcurrency
 * @subpackage  Commodities
 */
class VirtualcurrencyControllerCommodities extends Prism\Controller\Admin
{
    public function getModel($name = 'Commodity', $prefix = 'VirtualcurrencyModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    public function delete()
    {
        // Check for request forgeries
        JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

        // Get items to remove from the request.
        $cid = $this->input->get('cid', array(), 'array');

        $redirectOptions = array (
            'view' => 'commodities'
        );

        $model = $this->getModel();
        /** @var $model VirtualcurrencyModelCommodity */

        // Check for errors.
        if (count($cid) === 0) {
            $this->displayNotice(JText::_('COM_VIRTUALCURRENCY_ERROR_INVALID_ITEMS'), $redirectOptions);
            return;
        }

        $filteredData = $model->prepareDependencies($cid);

        try {
            if (count($filteredData['ids']) > 0) {
                $model->delete($filteredData['ids']);
            }
        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_virtualcurrency');
            throw new Exception(JText::_('COM_VIRTUALCURRENCY_ERROR_SYSTEM'));
        }

        $message = '';
        if (count($filteredData['ids']) > 0) {
            $message = JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($filteredData['ids'])) . ' ';
        }

        if (count($filteredData['excluded']) > 0) {
            JFactory::getApplication()->enqueueMessage(JText::plural($this->text_prefix . '_N_ITEMS_NOT_DELETED', count($filteredData['excluded'])), 'warning');
        }

        $this->displayMessage($message, $redirectOptions);
    }
}
