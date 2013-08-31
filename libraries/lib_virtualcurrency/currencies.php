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
 * This class contains methods used for managing a set of currencies.
 *
 * @package      VirtualCurrency
 * @subpackage   Library
 */
class VirtualCurrencyCurrencies {
    
    protected $db               = null;
    protected $currencies       = array();
    
    /**
     * Initialize the object and load currencies data.
     *
     * <code>
     * 
     *  // The state could be 1 = published, 0 = unpublished, null = all
     *  $options = array(
     *      "state" => 1
     *  );
     *  
     *  $currencies = new VirtualCurrencyCurrencies($options);
     *  
     * </code>
     * 
     * @param array $options
     *
     */
    public function __construct($options = array()) {
        
        // Set database driver
        $this->db = JFactory::getDbo();
        
        $this->load($options);
        
    }
    
    /**
     * Load the data of the currencies.
     * 
     * <code>
     *  
     *  // The state could be 1 = published, 0 = unpublished, null = all
     *  $options = array(
     *      "state" => 1
     *  );
     *  
     *  $currencies = new VirtualCurrencyCurrencies();
     *  $currencies->load($options);
     *  
     * </code>
     * 
     * @param array $options 
     * 
     */
    public function load($options = array()) {
        
        $query = $this->db->getQuery(true);
        
        $query
            ->select("a.id, a.title, a.code, a.symbol, a.amount, a.currency, a.minimum, a.published")
            ->from($this->db->quoteName("#__vc_currencies") . " AS a");
        
        $state = JArrayHelper::getValue($options, "state");
        if(!is_null($state)) {
            $state = (!$state) ? 0 : 1;
            $query->where("a.published = ". (int)$state);
        }
            
        $this->db->setQuery($query);
        $results = $this->db->loadAssocList("id");
        
        if(!empty($results)) {
            $this->currencies = $results;
        }
    }
    
    /**
     * Return the array that contains the data of the currencies.
     * 
     * <code>
     *  
     *  // Get the data of the currencies
     *  $currencies = new VirtualCurrencyCurrencies();
     *  $data       = $currencies->getCurrencies();
     *  
     * </code>
     * 
     * @return array
     */
    public function getCurrencies() {
        return $this->currencies;
    }
    
    /**
     * Return a currency data, getting it by currency ID.
     * 
     * <code>
     *  
     *  // Get a data of a currency.
     *  $currencies = new VirtualCurrencyCurrencies();
     *  $currencyId = 1;
     *  $data       = $currencies->getCurrency($currencyId);
     *  
     * </code>
     * 
     * @param integer $id
     * 
     * @return array|null
     */
    public function getCurrency($id) {
        return (!isset($this->currencies[$id])) ? null : $this->currencies[$id];
    }
}
