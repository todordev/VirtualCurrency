<?php
/**
 * @package      VirtualCurrency
 * @subpackage   ItpDonate
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

class VirtualCurrencyModelNotifier extends JModelLegacy
{
    /**
     * Send mail to administrator and notify him
     * if there is an error during process of transaction.
     */
    public function sendMailToAdministrator()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Send email to the administrator
        $subject = JText::_("COM_VIRTUALCURRENCY_ERROR_PAYMENT_PROCESS_SUBJECT");
        $body    = JText::_("COM_VIRTUALCURRENCY_ERROR_PAYMENT_PROCESS_BODY");
        $return  = JFactory::getMailer()->sendMail($app->get("mailfrom"), $app->get("fromname"), $app->get("mailfrom"), $subject, $body);

        // Check for an error.
        if ($return !== true) {
            $error = JText::sprintf("COM_VIRTUALCURRENCY_ERROR_MAIL_SENDING_ADMIN");
            JLog::add($error);
        }
    }
}
