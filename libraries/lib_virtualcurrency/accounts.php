<?php
/**
* @package      Virtual Currency
* @subpackage   Library
* @author       Todor Iliev
* @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
* @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Virtual Currency is free software. This vpversion may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality 
 * for managing user accounts.
 *
 * @package 	 Virtual Currency
 * @subpackage   Library
 */
class VirtualCurrencyAccounts {
    
    protected $db               = null;
    protected $accounts         = array();
    
    protected static $instances = array();
    
    public function __construct($userId = 0) {
        
        // Set database driver
        $this->db = JFactory::getDbo();
        
        // Load data
        if(!empty($userId)) {
            $this->load($userId);
        }
    }
    
    public static function getInstance($userId = 0)  {
    
        if (empty(self::$instances[$userId])){
            $accounts = new VirtualCurrencyAccounts($userId);
            self::$instances[$userId] = $accounts;
        }
    
        return self::$instances[$userId];
    }
    
    /**
     * Load all accounts by userId
     * 
     * @param integer $userId 
     */
    public function load($userId) {
        
        $query = $this->db->getQuery(true);
        
        $query
            ->select(
                "a.id, a.amount, a.note, a.currency_id, a.user_id, " .
                "b.title, b.code, b.symbol, " .
                "c.name"
            )
            ->from($this->db->quoteName("#__vc_accounts") . " AS a")
            ->innerJoin($this->db->quoteName("#__vc_currencies") . " AS b ON a.currency_id = b.id")
            ->innerJoin($this->db->quoteName("#__users") . " AS c ON a.user_id = c.id")
            ->where("a.user_id = ". (int)$userId);
            
        $this->db->setQuery($query);
        $results = $this->db->loadAssocList();
        
        if(!empty($results)) {
            $this->accounts = $results;
        }
    }
    
    public function getAccounts() {
        return $this->accounts;
    }
    
}
