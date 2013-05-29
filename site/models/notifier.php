<?php
/**
 * @package      ITPrism Components
 * @subpackage   ItpDonate
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * ItpDonate is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

class VirtualCurrencyModelNotifier extends JModel {
    
    /**
     * Send mail to administrator and notify him about new transaction.
     * @param array  $data		Transaction data
     */
    public function sendMailToAdministrator($data) {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
         // Send email to the administrator
        $subject = JText::_("COM_VIRTUALCURRENCY_NEW_ORDER_ADMIN_SUBJECT");
        $body    = JText::sprintf("COM_VIRTUALCURRENCY_NEW_ORDER_ADMIN_BODY", $app->getCfg("sitename"));
        $return  = JFactory::getMailer()->sendMail($app->getCfg("mailfrom"), $app->getCfg("fromname"), $app->getCfg("mailfrom"), $subject, $body);
		
		// Check for an error.
		if ($return !== true) {
		    $error = JText::sprintf("COM_VIRTUALCURRENCY_ERROR_MAIL_SENDING_ADMIN");
			JLog::add($error);
		}
        
    }
    
	/**
     * Send mail to user
     * 
     * @param float  $data	Transaction data
     */
    public function sendMailToUser($data) {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        $user = JFactory::getUser($data["receiver_id"]);
        
        // Get currency
        jimport("virtualcurrency.currency");
        $currency = VirtualCurrencyCurrency::getInstance($data["currency_id"]);
        
         // Send email to the administrator
        $subject = JText::sprintf("COM_VIRTUALCURRENCY_NEW_ORDER_USER_SUBJECT", $app->getCfg("sitename"));
        $body    = JText::sprintf("COM_VIRTUALCURRENCY_NEW_ORDER_USER_BODY", $data["number"], $currency->title, $app->getCfg("sitename") );
        $return  = JFactory::getMailer()->sendMail($app->getCfg("mailfrom"), $app->getCfg("fromname"), $user->email, $subject, $body);
		
		// Check for an error.
		if ($return !== true) {
		    $error = JText::_("COM_VIRTUALCURRENCY_ERROR_MAIL_SENDING_USER");
			JLog::add($error);
		}
        
    }
    
}