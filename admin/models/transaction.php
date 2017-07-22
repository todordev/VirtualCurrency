<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\Utilities\ArrayHelper;

// no direct access
defined('_JEXEC') or die;

class VirtualcurrencyModelTransaction extends JModelAdmin
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type    The table type to instantiate
     * @param   string  $prefix A prefix for the table class name. Optional.
     * @param   array   $config Configuration array for model. Optional.
     *
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Transaction', $prefix = 'VirtualcurrencyTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param   array   $data     An optional array of data for the form to interogate.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.transaction', 'transaction', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed   The data for the form.
     * @since   1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState($this->option . '.edit.transaction.data', array());
        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Save data into the DB
     *
     * @param array $data The data about item
     *
     * @return mixed  Item ID or null
     */
    public function save($data)
    {
        $id          = ArrayHelper::getValue($data, 'id', 0, 'int');
        $title       = ArrayHelper::getValue($data, 'title');
        $units       = ArrayHelper::getValue($data, 'units', 0.00, 'float');
        $txnId       = ArrayHelper::getValue($data, 'txn_id');
        $txnAmount   = ArrayHelper::getValue($data, 'txn_amount');
        $txnCurrency = ArrayHelper::getValue($data, 'txn_currency');
        $txnStatus   = ArrayHelper::getValue($data, 'txn_status');
        $txnDate     = ArrayHelper::getValue($data, 'txn_date');
        $senderId    = ArrayHelper::getValue($data, 'sender_id', 0, 'int');
        $receiverId  = ArrayHelper::getValue($data, 'receiver_id', 0, 'int');

        $serviceProvider = ArrayHelper::getValue($data, 'service_provider');

        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);

        $row->set('title', $title);
        $row->set('units', $units);
        $row->set('txn_id', $txnId);
        $row->set('txn_amount', $txnAmount);
        $row->set('txn_currency', $txnCurrency);
        $row->set('txn_status', $txnStatus);
        $row->set('txn_date', $txnDate);
        $row->set('sender_id', $senderId);
        $row->set('receiver_id', $receiverId);
        $row->set('service_provider', $serviceProvider);

        $row->store();

        return $row->get('id');
    }
}
