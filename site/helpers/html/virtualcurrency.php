<?php
/**
 * @package      ITPrism Components
 * @subpackage   VirtualCurrency
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * VirtualCurrency is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

/**
 * VirtualCurrency Html Helper
 *
 * @package		ITPrism Components
 * @subpackage	VirtualCurrency
 * @since		1.6
 */
abstract class JHtmlVirtualCurrency {
    
    /**
     * 
     * Display an input field for amount
     * @param float $value
     * @param array $currency
     * @param array $options
     */
    public static function inputAmount($value, $currency, $options) {
        
        $class = "";
        if(!empty($currency["symbol"])){
            $class = "input-prepend ";
        }
        
        $class .= "input-append";
        
        $html = '<div class="'.$class.'">';
        
        if(!empty($currency["symbol"])){
            $html .= '<span class="add-on">'. $currency["symbol"] .'</span>';
        }
            
        $name = JArrayHelper::getValue($options, "name");
        
        $id   = "";
        if(JArrayHelper::getValue($options, "id")) {
            $id = 'id="'.JArrayHelper::getValue($options, "id").'"';
        }
        
        if(!$value OR !is_numeric($value)) {
            $value = 0;
        }
        
        if(JArrayHelper::getValue($options, "class")) {
            $class = 'class="'.JArrayHelper::getValue($options, "class").'"';
        }
        
        $html .= '<input type="text" name="'.$name.'" value="'.$value.'" '.$id.' '.$class.' />';
        
        if(!empty($currency["code"])) {
            $html .= '<span class="add-on">'.$currency["code"].'</span>';
        }
            
        $html .= '</div>';
        
        return $html;
        
    }
    
    /**
     * Add symbol or code to a currency
     * 
     * @param float $value
     * @param array $currency
     */
    public static function amount($value, $currency) {
        
        if(!empty($currency["symbol"])) { // Prepended
		    $amount = $currency["symbol"].$value;
		} else { // Append
		    $amount = $value.$currency["code"];
		}
		
		return $amount;
    } 
    
    /**
     * 
     * Add symbol or code to a currency
     * 
     * @param float $number Number of items
     * @param array $rate	Value for one item
     */
    public static function total($number, $value) {
        
        $amount = 0;
        if(!empty($value)) { 
		    $amount = $number * $value;
		} 
		
		return $amount;
    } 
    
    
}
