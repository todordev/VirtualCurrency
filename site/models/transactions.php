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
defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.modellist' );

/**
 * Get a list of items
 */
class VirtualCurrencyModelTransactions extends JModelList {
    
	 /**
     * Constructor.
     *
     * @param   array   An optional associative array of configuration settings.
     * @see     JController
     * @since   1.6
     */
    public function  __construct($config = array()) {
        
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
            	'number', 'a.number',
            	'date', 'a.txn_date',
            	'amount', 'a.txn_amount',
            	'title', 'b.title',
            	'sender', 'c.name',
            	'receiver', 'd.name',
            	'txn_id', 'a.txn_id',
            	'txn_status', 'a.txn_status',
            );
        }

        parent::__construct($config);
		
    }
    
    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since   1.6
     */
    protected function populateState($ordering = null, $direction = null) {
        
        $app       = JFactory::getApplication();
        /** @var $app JSite **/

        $value = JFactory::getUser()->id;
        $this->setState('filter.receiver_id', $value);
         
        // Load the component parameters.
        $params = $app->getParams($this->option);
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.txn_date', 'desc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param   string      $id A prefix for the store id.
     * @return  string      A store id.
     * @since   1.6
     */
    protected function getStoreId($id = '') {
        
        // Compile the store id.
//        $id .= ':' . $this->getState('filter.search');
//        $id .= ':' . $this->getState('filter.state');
        $id .= ':' . $this->getState('filter.receiver_id');

        return parent::getStoreId($id);
    }
    
   /**
     * Build an SQL query to load the list data.
     *
     * @return  JDatabaseQuery
     * @since   1.6
     */
    protected function getListQuery() {
        
        // Create a new query object.
        $db     = $this->getDbo();
        /** @var $db JDatabaseMySQLi **/
        $query  = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.number, a.txn_amount, a.txn_date, a.txn_currency, a.txn_id, a.txn_status, ' .
                'a.currency_id, a.sender_id, a.receiver_id, a.service_provider, '.
                'b.title AS title, ' .
                'c.name AS sender, ' .
                'd.name AS receiver'
            )
        );
        $query->from($db->quoteName('#__vc_transactions').' AS a');
        $query->innerJoin($db->quoteName('#__vc_currencies').' AS b ON a.currency_id = b.id');
        $query->innerJoin($db->quoteName('#__users').' AS c ON a.sender_id = c.id');
        $query->innerJoin($db->quoteName('#__users').' AS d ON a.receiver_id = d.id');

        // Filter by receiver
        $userId = $this->getState('filter.receiver_id');
        $query->where('a.sender_id  ='.(int)$userId, "OR");
        $query->where('a.receiver_id='.(int)$userId);
        
        // Add the list ordering clause.
        $orderString = $this->getOrderString();
        $query->order($db->escape($orderString));

        return $query;
    }
    
    protected function getOrderString() {
        
        $orderCol   = $this->getState('list.ordering');
        $orderDirn  = $this->getState('list.direction');
        
        return $orderCol.' '.$orderDirn;
    }
}