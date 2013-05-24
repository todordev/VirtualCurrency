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
	
    const PUBLISHED = 1;
    
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
		    
		    $db     = JFactory::getDbo();
			$userId = JArrayHelper::getValue($user, 'id');
			
			// Create accounts
			$this->createAccount($userId, $db);
			
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
	    
	    $this->createAccount($userId, $db);
	    
	}
	
	/**
	 * This method checks for existing accounts current currencies.
	 * If there is no account for a currency, it creates new one.
	 * 
	 * @param integer $userId
	 * @param object  $db
	 */
	protected function createAccount($userId, $db) {
	    
	    // Get Accounts
	    jimport('virtualcurrency.accounts');
	    $accounts = new VirtualCurrencyAccounts($db);
	    $accounts->load($userId);
	    $a = $accounts->getAccounts();
	     
	    $accountIds = array();
	    foreach($a as $value) {
	        $accountIds[] = $value["currency_id"];
	    }
	     
	    // Get currencies
	    jimport('virtualcurrency.currencies');
	    $currencies = new VirtualCurrencyCurrencies($db);
	    $currencies->load(self::PUBLISHED);
	     
	    $c = $currencies->getCurrencies();
	     
	    // Check and create accounts
	    foreach($c as $currency) {
	        if(!in_array($currency["id"], $accountIds)) {
	            jimport("virtualcurrency.account");
	            $account = new VirtualCurrencyAccount($db);
	            $account->amount      = 0;
	            $account->currency_id = $currency["id"];
	            $account->user_id     = $userId;
	             
	            $account->store();
	        }
	    }
	    
	} 

}
