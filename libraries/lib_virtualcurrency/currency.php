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

JLoader::register("VirtualCurrencyTableCurrency", JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_virtualcurrency".DIRECTORY_SEPARATOR."tables".DIRECTORY_SEPARATOR."currency.php");

/**
 * This class contains methods that are used for managing currency.
 *
 * @package      Virtual Currency
 * @subpackage   Library
 */
class VirtualCurrencyCurrency extends VirtualCurrencyTableCurrency {
    
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
    
        if (empty(self::$instances[$id])){
            $currency = new VirtualCurrencyCurrency($id);
            self::$instances[$id] = $currency;
        }
    
        return self::$instances[$id];
    }
    
    /*
     * This method calculates an amount 
     * that will cost for a number of units. 
     */
    public function calculate($number) {
        
        $amount = 0;
        if(!empty($number)) { 
		    $amount = $this->amount * $number;
		} 
		
		return $amount;
        
    }
    
}
