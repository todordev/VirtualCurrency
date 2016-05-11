<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class VirtualcurrencyModelTransactions extends JModelList
{
    /**
     * Constructor.
     *
     * @param   array $config An optional associative array of configuration settings.
     *
     * @see     JController
     * @since   1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'title', 'a.title',
                'units', 'a.units',
                'date', 'a.txn_date',
                'amount', 'a.txn_amount',
                'sender', 'c.name',
                'receiver', 'd.name',
                'txn_id', 'a.txn_id',
                'txn_status', 'a.txn_status',
            );
        }

        parent::__construct($config);
    }

    protected function populateState($ordering = null, $direction = null)
    {
        // Load the component parameters.
        $params = JComponentHelper::getParams($this->option);
        $this->setState('params', $params);

        // Load the filter search.
        $value = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $value);

        // Load the filter state.
        $value = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state');
        $this->setState('filter.state', $value);

        // List state information.
        parent::populateState('a.id', 'asc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param   string $id A prefix for the store id.
     *
     * @return  string      A store id.
     * @since   1.6
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.state');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  JDatabaseQuery
     * @since   1.6
     */
    protected function getListQuery()
    {
        $db = $this->getDbo();
        /** @var $db JDatabaseDriver */

        // Create a new query object.
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.title, a.units, a.txn_amount, a.txn_date, a.txn_currency, a.txn_id, a.txn_status, ' .
                'a.item_id, a.item_type, a.sender_id, a.receiver_id, a.service_provider, ' .
                'c.name AS sender, ' .
                'd.name AS receiver'
            )
        );
        $query->from($db->quoteName('#__vc_transactions', 'a'));
        $query->leftJoin($db->quoteName('#__users', 'c') . ' ON a.sender_id = c.id');
        $query->leftJoin($db->quoteName('#__users', 'd') . ' ON a.receiver_id = d.id');

        // Filter by state
        $state = (string)$this->getState('filter.state');
        if ($state !== '') {
            $query->where('a.txn_status = ' . $db->quote($state));
        }

        // Filter by search in title
        $search = (string)$this->getState('filter.search');
        if ($search !== '') {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int)substr($search, 3));
            } elseif (stripos($search, 'tid:') === 0) {
                $query->where('a.txn_id = ' . $db->quote(substr($search, 3)));
            } else {
                $escaped = $db->escape($search, true);
                $quoted  = $db->quote('%' . $escaped . '%', false);
                $query->where('(c.name LIKE ' . $quoted . ') OR ( d.name LIKE ' . $quoted . ')');
            }
        }

        // Add the list ordering clause.
        $orderString = $this->getOrderString();
        $query->order($db->escape($orderString));

        return $query;
    }

    protected function getOrderString()
    {
        $orderCol  = $this->getState('list.ordering');
        $orderDirn = $this->getState('list.direction');

        return $orderCol . ' ' . $orderDirn;
    }
}
