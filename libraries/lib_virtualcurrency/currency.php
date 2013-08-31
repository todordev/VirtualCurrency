<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

JLoader::register("VirtualCurrencyTableCurrency", JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_virtualcurrency".DIRECTORY_SEPARATOR."tables".DIRECTORY_SEPARATOR."currency.php");

/**
 * This class contains methods that are used for managing currency.
 *
 * @package      VirtualCurrency
 * @subpackage   Library
 */
class VirtualCurrencyCurrency extends VirtualCurrencyTableCurrency {
    
    protected static $instances = array();
    
    /**
     * Initialize the object and load currency data.
     *
     * <code>
     *
     *  $currencyId = 1;
     *  $currency   = new VirtualCurrencyCurrency($currencyId);
     *
     * </code>
     *
     * @param integer $id
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
     * Create a currency object, store it to the instances and return it.
     * 
     * <code>
     *
     *  $currencyId = 1;
     *  $currency   = VirtualCurrencyCurrency::getInstance($currencyId);
     *
     * </code>
     * 
     * @param  integer $id
     * 
     * @return VirtualCurrencyCurrency
     */
    public static function getInstance($id = 0)  {
    
        if (!isset(self::$instances[$id])){
            $currency = new VirtualCurrencyCurrency($id);
            self::$instances[$id] = $currency;
        }
    
        return self::$instances[$id];
    }
    
    /**
     * This method calculates the amount of the units. 
     * You have to give the number of your units that you would like to calculate. 
     * The method will calculate the price of those units.
     *
     * <code>
     *
     *  // Get the currency
     *  $currencyId  = 1;
     *  $currency    = VirtualCurrencyCurrency::getInstance($currencyId);
     *  
     *  // It is the number of units, that I would like to buy.
     *  $unitsNumber = 10;
     *  $amount      = $currency->calcualte($unitsNumber);
     *
     * </code>
     * 
     * @param  integer $number
     * 
     * @return float Amount
     */
    public function calculate($number) {
        
        $amount = 0;
        if(!empty($number)) { 
		    $amount = $this->amount * $number;
		} 
		
		return $amount;
        
    }
    
    /**
     * This method generates string, using symbol or code of the currency. 
     * That string represents an amount in the virtual currency.
     * 
     * <code>
     *
     *  // Get the currency
     *  $currencyId  = 1;
     *  $currency    = VirtualCurrencyCurrency::getInstance($currencyId);
     *  
     *  // It is the amount that I would like to present.
     *  $amount      = 100;
     *  $string      = $currency->getAmountString($amount);
     *
     * </code>
     * 
     * @param mixed $value This is a value used in the amount string.
     * @return string Amount
     */
    public function getAmountString($value) {
        
        if(!empty($this->symbol)) { // Symbol
            $amount = $this->symbol.$value;
        } else { // Currency Code
            $amount = $value.$this->code;
        }
        
        return $amount;
    }
    
    /**
     * Return the amount of the unit (virtual currency).
     * That is the price for one unit.
     * 
     * <code>
     *
     *  // Get the currency
     *  $currencyId  = 1;
     *  $currency    = VirtualCurrencyCurrency::getInstance($currencyId);
     *  
     *  // Get the amount
     *  $amount      = $currency->getAmount();
     *
     * </code>
     * 
     * @return  float
     */
    public function getAmount() {
        return $this->amount;
    }
    
    /**
     * Set the amount for one unit (virtual currency).
     * 
     * <code>
     *
     *  // Create an object of the currency
     *  $currencyId  = 1;
     *  $currency    = VirtualCurrencyCurrency::getInstance($currencyId);
     *  
     *  // Get the amount
     *  $amount      = 10;
     *  $currency->setAmount($amount);
     *
     * </code>
     * 
     * @param float $amount
     */
    public function setAmount($amount) {
        $this->amount = $amount;
    }
    
}
