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

/**
 * Version information
 *
 * @package 	 ITPrism Components
 * @subpackage   Virtual Currency
  */
class VirtualCurrencyCurrencies {
    
    protected $data = array();
    
    /**
     * 
     * Load all currencies
     * @param mixed $state 1 = published, 2 = unpublished, null = all
     */
    public function load($state = null) {
        
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        
        $query
            ->select("a.id, a.title, a.code, a.symbol, a.amount, a.currency, a.minimum, a.published")
            ->from($db->quoteName("#__vc_currencies") . " AS a");
            
        if(!is_null($state)) {
            $state = (!$state) ? 0 : 1;
            $query->where("a.published = ". (int)$state);
        }
            
        $db->setQuery($query);
        $results = $db->loadAssocList();
        
        if(!empty($results)) {
            $this->data = $results;
        }
    }
    
    public function getData() {
        return $this->data;
    }
    
    /**
     * Return a currency
     * @param integer $id
     */
    public function getCurrency($id) {
        
        $currency = null;
        
        foreach($this->data as $currency) {
            if($currency["id"] == $id) {
                break;
            }
        }
        
        return $currency;
        
    }
}
