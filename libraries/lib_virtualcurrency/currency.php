<?php
/**
* @package      ITPrism Components
* @subpackage   Virtual Currency
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
 * @package 	 ITPrism Components
 * @subpackage   Virtual Currency
  */
class VirtualCurrencyCurrency extends VirtualCurrencyTableCurrency{
    
    public function __construct( $db ) {
        parent::__construct( $db );
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
