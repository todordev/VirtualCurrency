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

JLoader::register("VirtualCurrencyTableAccount", JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_virtualcurrency".DIRECTORY_SEPARATOR."tables".DIRECTORY_SEPARATOR."account.php");

/**
 * This class contains methods that are used for managing currency.
 *
 * @package      Virtual Currency
 * @subpackage   Library
 */
class VirtualCurrencyAccount extends VirtualCurrencyTableAccount {
    
    protected static $instances = array();
    
    public function __construct($id = 0) {
        
        // Set database driver
        $db = JFactory::getDbo();
        parent::__construct($db);
        
        if(!empty($id)) {
            $this->load($id);
        }
    }
    
    public static function getInstance($id = 0)  {
        
        // If it is array with user id and currency id, 
        // I will generate a new array index.
        if(is_array($id)) {
            $index = $id["user_id"].":".$id["currency_id"];
        } else {
            $index = $id;
        }
        
        if (empty(self::$instances[$index])){
            $account = new VirtualCurrencyAccount($id);
            self::$instances[$index] = $account;
        }
    
        return self::$instances[$index];
    }
    
    public function increaseAmount($value) {
        
        if(is_numeric($value)) {
            $this->amount += $value;
        }
        
        return $this;
    }
    
    public function decreaseAmount($value) {
    
        if(is_numeric($value)) {
            $this->amount -= $value;
        }
    
        return $this;
    }
    
}
