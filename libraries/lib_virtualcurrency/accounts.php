<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality 
 * for managing user accounts.
 *
 * @package 	 VirtualCurrency
 * @subpackage   Library
 */
class VirtualCurrencyAccounts {
    
    protected $db               = null;
    protected $accounts         = array();
    
    protected static $instances = array();
    
    /**
     * Initialize the object loading accounts data.
     * 
     * <code>
     * 
     *  // Create an object that represents user accounts.
     *  $userId    = 1;
     *  $accounts  = new VirtualCurrencyAccounts($userId);
     *  
     * </code>
     * 
     * @param integer $userId
     */
    public function __construct($userId = 0) {
        
        // Set database driver
        $this->db = JFactory::getDbo();
        
        // Load data
        if(!empty($userId)) {
            $this->load($userId);
        }
    }
    
    /**
     * Create an object and store it as an instance.
     *
     * <code>
     * 
     *  // Create an object that represents user accounts and stores it as an instance.
     *  $userId    = 1;
     *  $accounts  = VirtualCurrencyAccounts::getInstance($userId);
     *  
     * </code>
     * 
     * @param integer $userId
     * @return VirtualCurrencyAccounts
     */
    public static function getInstance($userId = 0)  {
    
        if (empty(self::$instances[$userId])){
            $accounts = new VirtualCurrencyAccounts($userId);
            self::$instances[$userId] = $accounts;
        }
    
        return self::$instances[$userId];
    }
    
    /**
     * Load the data for all user accounts by userId
     * 
     * <code>
     * 
     *  // Load the data of all user virtual accounts.
     *  $userId    = 1;
     *  $accounts  = VirtualCurrencyAccounts::getInstance();
     *  $accounts->load($userId);
     *  
     * </code>
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
    
    /**
     * Return accounts data.
     * 
     * <code>
     * 
     *  // Get the data of all user virtual accounts.
     *  $userId    = 1;
     *  $accounts  = VirtualCurrencyAccounts::getInstance($userId);
     *  $data      = $accounts->getAccounts();
     *  
     * </code>
     * 
     * @return array
     */
    public function getAccounts() {
        return $this->accounts;
    }
    
}
