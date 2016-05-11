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

class VirtualcurrencyModelAccounts extends JModelList
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
                'amount', 'a.amount',
                'name', 'b.name',
                'title', 'c.title',
                'created', 'a.created_at',
                'published', 'a.published'
            );
        }

        parent::__construct($config);
    }
    
    protected function populateState($ordering = null, $direction = null)
    {
        // Filter search.
        $value = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $value);

        // Filter state.
        $value = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state');
        $this->setState('filter.state', $value);

        // Filter currency.
        $value = $this->getUserStateFromRequest($this->context.'.filter.currency', 'filter_currency', 0, 'int');
        $this->setState('filter.currency', $value);

        // Load the component parameters.
        $params = JComponentHelper::getParams($this->option);
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.created_at', 'desc');
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
        $id .= ':' . $this->getState('filter.currency');

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
                'a.id, a.amount, a.currency_id, a.user_id, a.created_at, a.published, ' .
                'b.name, ' .
                'c.title AS currency_title, c.code AS currency_code'
            )
        );
        $query->from($db->quoteName('#__vc_accounts', 'a'));
        $query->innerJoin($db->quoteName('#__users', 'b') . ' ON a.user_id = b.id');
        $query->innerJoin($db->quoteName('#__vc_currencies', 'c') . ' ON a.currency_id = c.id');

        // Filter by currency.
        $currencyId = (int)$this->getState('filter.currency');
        if ($currencyId > 0) {
            $query->where('a.currency_id = ' . (int)$currencyId);
        }
        
        // Filter by state
        $state = $this->getState('filter.state');
        if (is_numeric($state)) {
            $query->where('a.published = ' . (int)$state);
        } elseif ($state === '') {
            $query->where('(a.published IN (0, 1))');
        }

        // Filter by search in title
        $search = (string)$this->getState('filter.search');
        if ($search !== '') {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int)substr($search, 3));
            } else {
                $escaped = $db->escape($search, true);
                $quoted  = $db->quote('%' . $escaped . '%', false);
                $query->where('b.name LIKE ' . $quoted);
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
