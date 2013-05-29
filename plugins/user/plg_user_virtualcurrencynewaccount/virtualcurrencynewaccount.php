<?php
/**
 * @package      Virtual Currency
 * @subpackage   Plugins
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

/**
 * This class provides functionality 
 * for creating accounts used for storing 
 * and managing virtual currency.
 *
 * @package		Virtual Currency
 * @subpackage	Plugins
 */
class plgUserVirtualCurrencyNewAccount extends JPlugin {
	
	/**
	 *
	 * Method is called after user data is stored in the database
	 *
	 * @param	array		$user		Holds the new user data.
	 * @param	boolean		$isnew		True if a new user is stored.
	 * @param	boolean		$success	True if user was succesfully stored in the database.
	 * @param	string		$msg		Message.
	 *
	 * @return	void
	 * @since	1.6
	 * @throws	Exception on error.
	 */
	public function onUserAfterSave($user, $isnew, $success, $msg) {
	    
		if ($isnew) {
		    
			$userId = JArrayHelper::getValue($user, 'id');
			
			// Create accounts
			$this->createAccount($userId);
			
			if($this->params->get("give_units", 0)) {
			    $this->giveUnits($userId);
			}
			
		}
		
	}
	
	/**
	 *
	 * Method is called after user log in to the system.
	 *
	 * @param	array		$user		An associative array of JAuthenticateResponse type.
	 * @param	array 		$options    An associative array containing these keys: ["remember"] => bool, ["return"] => string, ["entry_url"] => string.
	 *
	 * @return	void
	 * @since	1.6
	 * @throws	Exception on error.
	 */
	public function onUserLogin($user, $options) {

	    // Get user id
	    $userName = JArrayHelper::getValue($user, 'username');
	     
	    $db       = JFactory::getDbo();
	    $query    = $db->getQuery(true);
	     
	    $query
    	    ->select("a.id")
    	    ->from($db->quoteName("#__users") . " AS a")
    	    ->where("a.username = " .$db->quote($userName));
	     
	    $db->setQuery($query, 0, 1);
	    $userId = $db->loadResult();
	    
	    $this->createAccount($userId);
	    
	}
	
	/**
	 * This method checks for existing accounts current currencies.
	 * If there is no account for a currency, it creates new one.
	 * 
	 * @param integer $userId
	 */
	protected function createAccount($userId) {
	    
	    // Get Accounts
	    jimport('virtualcurrency.accounts');
	    $accounts = new VirtualCurrencyAccounts();
	    $accounts->load($userId);
	    $a = $accounts->getAccounts();
	     
	    $accountIds = array();
	    foreach($a as $value) {
	        $accountIds[] = $value["currency_id"];
	    }
	     
	    // Get currencies
	    jimport('virtualcurrency.currencies');
	    
	    $published = 1;
	    
	    $currencies = new VirtualCurrencyCurrencies();
	    $currencies->load($published);
	    
	    $c = $currencies->getCurrencies();
	    
	    // Check and create accounts
	    foreach($c as $currency) {
	        if(!in_array($currency["id"], $accountIds)) {
	            jimport("virtualcurrency.account");
	            $account              = new VirtualCurrencyAccount();
	            $account->amount      = 0;
	            $account->currency_id = $currency["id"];
	            $account->user_id     = $userId;
	             
	            $account->store();
	            
	        }
	    }
	    
	} 
	
	/**
	 * 
	 * Add virtual currency to user account after registration.
	 * 
	 * @param integer $userId
	 */
	protected function giveUnits($userId) {
	    
	    $number     = (int)$this->params->get("give_units_number", 0);
	    $currencyId = $this->params->get("give_units_unit");
	    
	    if(!empty($number) AND !empty($currencyId)) {
	        
	        jimport("virtualcurrency.currency");
	        $currency     = VirtualCurrencyCurrency::getInstance($currencyId);
	        
	        if($currency->id) {
	            
	            // Get the id of the sender ( the bank that generates curreny )
	            $componentParams = JComponentHelper::getParams("com_virtualcurrency");
	            $senderId        = $componentParams->get("ordering_bank_id");
	            
	            // Get account ID
	            JLoader::register("VirtualCurrencyHelper", JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_virtualcurrency".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."virtualcurrency.php");
	            $accountId = VirtualCurrencyHelper::getAccountId($userId, $currency->id);
	            
	            // Add the units to the account
	            jimport("virtualcurrency.account");
	            $account  = VirtualCurrencyAccount::getInstance($accountId);
	            $account->addAmount($number);
	            $account->store();

	            // Store transaction
	            jimport("virtualcurrency.transaction");
	            $transaction  = new VirtualCurrencyTransaction();
	            
	            $seed         = substr(md5(uniqid(time() * rand(), true)), 0, 16);
	            
	            $data = array(
                    "number"             => $number,
                    "txn_id"             => JString::strtoupper("GEN_".JString::substr(JApplication::getHash($seed), 0, 16)),
                    "txn_amount"         => 0,
	                "txn_currency"       => $currency->currency,
	                "txn_status"         => "completed",
	                "service_provider"   => "System",
	                "currency_id"        => $currency->id,
	                "sender_id"          => $senderId,
	                "receiver_id"        => $userId
                );
	            
	            $transaction->bind($data);
	            $transaction->store();
	        }
	        
	        // Integrate with notifier
	        
	        // Notification services
	        $nServices = $this->params->get("give_units_integrate");
	        if(!empty($nServices)) {

	            $this->loadLanguage();
	            $message = JText::sprintf("PLG_USER_VIRTUALCURRENCYNEWACCOUNT_NOTIFICATION_AFTER_REGISTRATION", $number, $currency->title);
	            $this->notify($nServices, $message, $userId);
	            
	        }
	        
	        
	    }
	    
	}
	
	public function notify($nServices, $message, $userId) {
	    
	    switch($nServices) {
	        
	        case "gamification":
	            
	            jimport("itprism.integrate.notification.gamification");
	            $notifier = new ITPrismIntegrateNotificationGamification($userId, $message);
	            $notifier->send();
	            
	            break;
	            
            case "socialcommunity":
	                 
                jimport("itprism.integrate.notification.socialcommunity");
                $notifier = new ITPrismIntegrateNotificationSocialCommunity($userId, $message);
                $notifier->send();
                 
                break;
	                
            default:
                
                break;
                
	    }
	    
	}
	

}
