<?php
/**
 * @package      VirtualCurrency
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
                'amount', 'a.amount',
                'title', 'b.title'
            );
        }

        parent::__construct($config);
    }

    protected function populateState($ordering = null, $direction = null)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $value = JFactory::getUser()->get('id');
        $this->setState('filter.user_id', $value);

        // Load the component parameters.
        $params = $app->getParams($this->option);
        $this->setState('params', $params);

        // List state information.
        parent::populateState('b.title', 'desc');
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
        $id .= ':' . $this->getState('filter.user_id');

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
        /** @var $db JDatabaseDriver */
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.amount, a.currency_id, ' .
                'b.title'
            )
        );
        $query->from($db->quoteName('#__vc_accounts', 'a'));
        $query->innerJoin($db->quoteName('#__vc_currencies', 'b') .' ON a.currency_id = b.id');

        // Filter by receiver
        $query->where('a.user_id=' . (int)$this->getState('filter.user_id'));
        $query->where('b.published = ' . (int)Prism\Constants::PUBLISHED);

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
