<?php
/**
* @package      Virtual Currency
* @subpackage   Libarary
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
 * This class contains methods used for managing a set of currencies.
 *
 * @package      Virtual Currency
 * @subpackage   Library
 */
class VirtualCurrencyCurrencies {
    
    protected $db               = null;
    protected $currencies       = array();
    
    public function __construct($state = null) {
        
        // Set database driver
        $this->db = JFactory::getDbo();
        
        if(!is_null($state)) {
            $this->load($state);
        }
        
    }
    
    /**
     * Load all currencies
     * 
     * @param integer $state 1 = published, 2 = unpublished, 0 = all
     */
    public function load($state = 0) {
        
        $query = $this->db->getQuery(true);
        
        $query
            ->select("a.id, a.title, a.code, a.symbol, a.amount, a.currency, a.minimum, a.published")
            ->from($this->db->quoteName("#__vc_currencies") . " AS a");
            
        if(!is_null($state)) {
            $state = (!$state) ? 0 : 1;
            $query->where("a.published = ". (int)$state);
        }
            
        $this->db->setQuery($query);
        $results = $this->db->loadAssocList();
        
        if(!empty($results)) {
            $this->currencies = $results;
        }
    }
    
    public function getCurrencies() {
        return $this->currencies;
    }
    
    /**
     * Return a currency
     * @param integer $id
     */
    public function getCurrency($id) {
        
        $currency = null;
        
        foreach($this->currencies as $currency) {
            if($currency["id"] == $id) {
                break;
            }
        }
        
        return $currency;
        
    }
}
