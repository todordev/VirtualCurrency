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

jimport('joomla.application.component.model');

class VirtualCurrencyModelOrdering extends JModelLegacy {
    
    protected $item;
    
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   type    The table type to instantiate
     * @param   string  A prefix for the table class name. Optional.
     * @param   array   Configuration array for model. Optional.
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Currency', $prefix = 'VirtualCurrencyTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }
    
    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     * @todo replace $_context with $context
     */
    protected function populateState() {
        
        $app     = JFactory::getApplication();

        // Load the parameters.
        $params  = $app->getParams($this->option);
        $this->setState('params', $params);
        
    }

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  	  The id of the primary key.
	 * @param   integer  $userId  The user Id 
	 *
	 * @since   11.1
	 */
	public function getItem($pk) {
	    
	    if($this->item) {
	        return $this->item;
	    }
	    
		// Initialise variables.
		$table = $this->getTable();

		if ($pk > 0) {
		    
			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError()) {
			    JLog::add($table->getError() . " [ VirtualCurrencyOrdering->getItem() ]");
				throw new Exception(JText::_("COM_VIRTUALCURRENCY_ERROR_SYSTEM"), ITPrismErrors::CODE_ERROR);
			}
			
		}

		// Convert to the JObject before adding other data.
		$properties = $table->getProperties();
		$this->item = JArrayHelper::toObject($properties, 'JObject');
		
		return $this->item;
	}
	
	public function isDebugMode() {
        
	    $params = $this->getState("params");
        $this->debugMode = $params->get("debug_payment_disabled", 0);
        if(!$this->debugMode) {
		    return false;
        }
		    
        return true;
    } 
	
}