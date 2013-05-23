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

jimport('joomla.application.component.controller');

/**
 * @package		VirtualCurrency
 * @subpackage	Payments
 * @since		2.5
 */
class VirtualCurrencyControllerNotifier extends JController {
   
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
    public function getModel($name = 'Notifier', $prefix = '', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * Catch the response from PayPal and store data about transaction
     */
    public function notify() {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        $params    = $app->getParams("com_virtualcurrency");
        
        // Check for disabled payment functionality
        if($params->get("debug_payment_disabled", 0)) {
            $error  = JText::_("COM_VIRTUALCURRENCY_ERROR_PAYMENT_HAS_BEEN_DISABLED");
            $error .= "\n". JText::sprintf("COM_VIRTUALCURRENCY_TRANSACTION_DATA", var_export($_POST, true));
			JLog::add($error);
			return null;
        }
        
        $requestMethod = $app->input->getMethod();
        if("POST" != $requestMethod) {
            $error  = "COM_VIRTUALCURRENCY_ERROR_INVALID_TRANSACTION_REQUEST_METHOD (" .$requestMethod . "):\n";
            $error .= "INPUT: " . var_export($app->input, true) . "\n";
            JLog::add($error);
            return;
        }
        
        // Save data
        try {
            
            // Events
            $dispatcher	       = JDispatcher::getInstance();
            
            // Event Notify
            JPluginHelper::importPlugin('virtualcurrencypayment');
            $results     = $dispatcher->trigger('onPaymenNotify', array('com_virtualcurrency.notify', $_POST, $params));
            
            $transaction = null;
            
            if(!empty($results)) {
                $transaction = array_shift($results);
            }
            
            // Check for error.
            if(empty($transaction)) {
                return;
            }
            
            // Event After Payment
            JPluginHelper::importPlugin('virtualcurrencypayment');
            $dispatcher->trigger('onAfterPayment', array('com_virtualcurrency.notify', &$transaction, $params));
        		
            $model = $this->getModel();
            
            // Send email to administrator
            if($params->get("security_send_mail_admin")) {
                $model->sendMailToAdministrator($transaction);
            }
                
            // Send email to user
            if($params->get("security_send_mail_user")) {
                $model->sendMailToUser($transaction);
            }
            
        } catch (Exception $e) {
            JLog::add($e->getMessage());
            return;
        }
        
    }
    
}
