<?php
/**
* @package      VirtualCurrency
* @subpackage   Library
* @author       Todor Iliev
* @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
* @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

defined('JPATH_PLATFORM') or die;

JLoader::register("VirtualCurrencyTableAccount", JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_virtualcurrency".DIRECTORY_SEPARATOR."tables".DIRECTORY_SEPARATOR."account.php");

/**
 * This class contains methods, 
 * which are used for managing virtual bank account.
 *
 * @package      VirtualCurrency
 * @subpackage   Library
 */
class VirtualCurrencyAccount extends VirtualCurrencyTableAccount {
    
    /**
     * This variable collects the instances of this object.
     * 
     * @var array
     */
    protected static $instances = array();
    
    /**
     * This method initializes the object 
     * and loads the data for a virtual bank account.
     * 
     * <code>
     * 
     *  // Get user account by keys
     *  $keys = array(
     *      "user_id" => 1,
     *      "currency_id" => 10
     *  );
     *  $account = new VirtualCurrencyAccount($keys);
     *  
     *  // Get user account by account ID
     *  $accountId = 1;
     *  $account   = new VirtualCurrencyAccount($accountId);
     * </code>
     * 
     * @param array|integer $id
     * 
     */
    public function __construct($id = 0) {
        
        // Set database driver
        $db = JFactory::getDbo();
        parent::__construct($db);
        
        if(!empty($id)) {
            $this->load($id);
        }
    }
    
    /**
     * This method creats an object and store it as an instance.
     * When the object is created, the system can initialize it, 
     * loading a account data base on account ID or other keys 
     * ( user_id and currency_id ). 
     * 
     * <code>
     * 
     *  // Get user account by keys
     *  $keys = array(
     *      "user_id" => 1,
     *      "currency_id" => 10
     *  );
     *  $account = VirtualCurrencyAccount::getInstance($keys);
     *  
     *  // Get user account by account ID
     *  $accountId = 1;
     *  $account   = VirtualCurrencyAccount::getInstance($accountId);
     * </code>
     * 
     * @param  array|integer These are the keys, by which the object will be initialized.
     * 
     * @return VirtualCurrencyAccount
     */
    public static function getInstance($id = 0)  {
        
        // If it is array with user id and currency id, 
        // I am going to generate a new index from the values.
        if(is_array($id)) {
            $index = md5($id["user_id"].":".$id["currency_id"]);
        } else {
            $index = $id;
        }
        
        if (!isset(self::$instances[$index])) {
            $account = new VirtualCurrencyAccount($id);
            self::$instances[$index] = $account;
        }
    
        return self::$instances[$index];
    }
    
    /**
     * Increase the number of units ( virtual currency ).
     *
     * <code>
     * 
     *  // Get user account by account ID
     *  $accountId = 1;
     *  $account   = VirtualCurrencyAccount::getInstance($accountId);
     *  
     *  // Increase the amount and store the new value.
     *  $account->increaseAmount(50);
     *  $account->store();
     *  
     * </code>
     * 
     * @param integer $value
     * 
     * @return VirtualCurrencyAccount
     */
    public function increaseAmount($value) {
        
        if(is_numeric($value)) {
            $this->amount += $value;
        }
        
        return $this;
    }
    
    /**
     * Decrease the number of units ( virtual currency )
     * 
     * <code>
     * 
     *  // Get user account by account ID
     *  $accountId = 1;
     *  $account   = VirtualCurrencyAccount::getInstance($accountId);
     *  
     *  // Decrease the amount and store the new value.
     *  $account->decreaseAmount(50);
     *  $account->store();
     *  
     * </code>
     * 
     * @param integer $value
     *
     * @return VirtualCurrencyAccount
     */
    public function decreaseAmount($value) {
    
        if(is_numeric($value)) {
            $this->amount -= $value;
        }
    
        return $this;
    }
    
}
