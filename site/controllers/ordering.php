<?php
/**
 * @package      ITPrism Components
 * @subpackage   VirtualCurrency
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * VirtualCurrency is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('itprism.controller.form.frontend');

/**
 * VirtualCurrency ordering controller
 *
 * @package     ITPrism Components
 * @subpackage  VirtualCurrency
  */
class VirtualCurrencyControllerOrdering extends ITPrismControllerFormFrontend {
    
	/**
     * Method to get a model object, loading it if required.
     *
     * @param	string	$name	The model name. Optional.
     * @param	string	$prefix	The class prefix. Optional.
     * @param	array	$config	Configuration array for model. Optional.
     *
     * @return	object	The model.
     * @since	1.5
     */
    public function getModel($name = 'Ordering', $prefix = 'VirtualCurrencyModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    public function step1() {
        
        // Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
        $userId = JFactory::getUser()->id;
        if(!$userId) {
            $redirectData = array(
                "force_direction" => "login_form"
            );
            $this->displayNotice(JText::_('COM_VIRTUALCURRENCY_ERROR_NOT_LOG_IN'), $redirectData);
            return;
        }
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
		// Get the data from the form
		$itemId    = $app->input->post->getInt('id', 0);
		
		$redirectDataError = array(
	        "view" => "ordering"
		);
		
        $model     = $this->getModel();
        /** @var $model VirtualCurrencyModelOrdering **/
        
        // Check for maintenance (debug) state
        $params        = $app->getParams($this->option);
        if( $this->inDebugMode($params) ) {
            return;
        }
        
		// Check terms and use
        if($params->get("ordering_service_terms", 0)) {
            
            $terms = $app->input->post->get("terms", 0);
            if(!$terms) {
                $this->displayNotice(JText::_("COM_VIRTUALCURRENCY_ERROR_TERMS_NOT_ACCEPTED"), $redirectDataError);
                return; 
            }
        }
        
        // Check for valid amount
        $amount       = $app->input->post->get("amount", 0, "float");
        if(!$amount) {
            $this->displayNotice(JText::_('COM_VIRTUALCURRENCY_ERROR_INVALID_AMOUNT'), $redirectDataError);
            return; 
        }
        
        
        // Check for valid item
        $item   = $model->getItem($itemId);
        if(empty($item->id))  {
            $this->displayNotice(JText::_('COM_VIRTUALCURRENCY_ERROR_INVALID_CURRENCY'), $redirectDataError);
            return;
        }
        
        // Check for valid allowed items for buying
        if($amount < $item->minimum) {
            $this->displayNotice(JText::_('COM_VIRTUALCURRENCY_ERROR_INVALID_AMOUNT'), $redirectDataError);
            return; 
        }
        
        // Initialize step one
        $app->setUserState("ordering.step1",  false);
        $app->setUserState("ordering.tmp_id", 0);
        
        // Store the ID of the selected item to the session
        $app->setUserState("ordering.item_id", $item->id);
        
        // Set amount to the session
        $app->setUserState("ordering.amount", $amount);
        
        // Set the flag of step 1 to true
        $app->setUserState("ordering.step1", true);
        
        // Store data to temporary table
        $data = array(
            "user_id"     => $userId,
            "currency_id" => $item->id,
            "number"	  => $amount
        );
        
        $temporary = $this->getModel("Temporary");
        $tmpId     = $temporary->save($data);
        
        // Remove old temporary records
        $temporary->remove();
        
        // Set the temporary ID to the session
        $app->setUserState("ordering.tmp_id", $tmpId);
        
        // Redirect to next page
        $redirectData = array(
            "view"   => "ordering",
            "layout" => "payment"
        );
        
        $link = $this->prepareRedirectLink($redirectData);
		$this->setRedirect(JRoute::_($link, false));
    }
    
    protected function inDebugMode($params) {
        
        $this->debugMode = $params->get("debug_payment_disabled", 0);
        if(!$this->debugMode) {
		    return false;
        }
        
        $msg = JString::trim($params->get("debug_disabled_functionality_msg"));
        if(!$msg) {
            $msg = JText::_("COM_VIRTUALCURRENCY_DEBUG_MODE_DEFAULT_MSG");
        }
        
        $redirectData = array(
            "view"   => "ordering"
        );
        
        $this->displayNotice($msg, $redirectData);
    } 
    
}