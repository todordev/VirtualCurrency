<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Get a list of items
 */
class VirtualCurrencyModelTransactions extends JModelList
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
                'units', 'a.units',
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
    protected function populateState($ordering = null, $direction = null)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $value = JFactory::getUser()->get("id");
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
     * @param   string $id A prefix for the store id.
     *
     * @return  string      A store id.
     * @since   1.6
     */
    protected function getStoreId($id = '')
    {
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
    protected function getListQuery()
    {
        // Create a new query object.
        $db = $this->getDbo();
        /** @var $db JDatabaseMySQLi */

        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.units, a.txn_amount, a.txn_date, a.txn_currency, a.txn_id, a.txn_status, ' .
                'a.currency_id, a.sender_id, a.receiver_id, a.service_provider, ' .
                'b.title AS title, ' .
                'c.name AS sender, ' .
                'd.name AS receiver'
            )
        );
        $query->from($db->quoteName('#__vc_transactions', 'a'));
        $query->innerJoin($db->quoteName('#__vc_currencies', 'b') . ' ON a.currency_id = b.id');
        $query->innerJoin($db->quoteName('#__users', 'c') . ' ON a.sender_id = c.id');
        $query->innerJoin($db->quoteName('#__users', 'd') . ' ON a.receiver_id = d.id');

        // Filter by receiver
        $userId = $this->getState('filter.receiver_id');
        $query->where('a.sender_id   =' . (int)$userId, "OR");
        $query->where('a.receiver_id =' . (int)$userId);

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
