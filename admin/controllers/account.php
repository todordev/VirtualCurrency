<?php
/**
 * @package      ITPrism Components
 * @subpackage   Virtual Currency
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Virtual Currency is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

jimport('itprism.controller.form.backend');

/**
 * VirtualCurrency Account controller class.
 *
 * @package		ITPrism Components
 * @subpackage	VirtualCurrency
 * @since		1.6
 */
class VirtualCurrencyControllerAccount extends ITPrismControllerFormBackend {
    
    /**
     * Save an item
     */
    public function save(){
        
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        $msg     = "";
        $link    = "";
        $data    = $app->input->post->get('jform', array(), 'array');
        $itemId  = JArrayHelper::getValue($data, "id");
        
        $model   = $this->getModel();
        /** @var $model Virtual CurrencyModelAccount **/
        
        $form    = $model->getForm($data, false);
        /** @var $form JForm **/
        
        if(!$form){
            throw new Exception($model->getError(), 500);
        }
            
        // Validate the form
        $validData = $model->validate($form, $data);
        
        // Check for errors.
        if($validData === false){
            
            $link = $this->prepareRedirectLink();
            
            $this->setMessage($model->getError(), "notice");
            $this->setRedirect(JRoute::_($link, false));
            return;
        }
        
        // Check user ID
        $userId = JArrayHelper::getValue($validData, "user_id");
        if(!$userId){
            
            $link = $this->prepareRedirectLink();
            
            $this->setMessage(JText::_("COM_VIRTUALCURRENCY_ERROR_INVALID_USER"), "notice");
            $this->setRedirect(JRoute::_($link, false));
            return;
        }
            
        try{
            $itemId = $model->save($validData);
        }catch(Exception $e){
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_VIRTUALCURRENCY_ERROR_SYSTEM'));
        }
        
        $msg  = JText::_('COM_VIRTUALCURRENCY_ACCOUNT_SAVED');
        $link = $this->prepareRedirectLink($itemId);
        
        $this->setRedirect(JRoute::_($link, false), $msg);
    
    }
    
}