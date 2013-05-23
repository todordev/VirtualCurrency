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

jimport('joomla.application.component.modelform');

class VirtualCurrencyModelTemporary extends JModel {
    
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   type    The table type to instantiate
     * @param   string  A prefix for the table class name. Optional.
     * @param   array   Configuration array for model. Optional.
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Temporary', $prefix = 'VirtualCurrencyTable', $config = array()){
        return JTable::getInstance($type, $prefix, $config);
    }
    
    /**
     * Method to save the form data.
     *
     * @param	array		The form data.
     * @return	mixed		The record id on success, null on failure.
     * @since	1.6
     */
    public function save($data) {
        
        $userId       = JArrayHelper::getValue($data, "user_id");
        $currencyId   = JArrayHelper::getValue($data, "currency_id");
        $number       = JArrayHelper::getValue($data, "number");
        
        // Load a record from the database
        $row = $this->getTable();
        
        $row->set("user_id",     $userId);
        $row->set("currency_id", $currencyId);
        $row->set("number",      $number);
        
        $row->store();
        
        return $row->id;
        
    }
    
    /**
     * 
     * Remove old records
     */
    public function remove() {
        
        $db     = JFactory::getDbo();
        $query  = $db->getQuery(true);
        $query
            ->delete( $db->quoteName("#__vc_tmp") )
            ->where("record_date < ( NOW() - INTERVAL 2 DAY )");
            
        $db->setQuery($query);
        $db->query();
        
    }
    
}