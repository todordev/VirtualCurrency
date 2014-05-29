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

jimport('joomla.application.component.modeladmin');

class VirtualCurrencyModelTransaction extends JModelAdmin
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
    public function getTable($type = 'Transaction', $prefix = 'VirtualCurrencyTable', $config = array())
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
        $id          = JArrayHelper::getValue($data, "id");
        $units       = JArrayHelper::getValue($data, "units");
        $currencyId  = JArrayHelper::getValue($data, "currency_id");
        $txnId       = JArrayHelper::getValue($data, "txn_id");
        $txnAmount   = JArrayHelper::getValue($data, "txn_amount");
        $txnCurrency = JArrayHelper::getValue($data, "txn_currency");
        $txnStatus   = JArrayHelper::getValue($data, "txn_status");
        $txnDate     = JArrayHelper::getValue($data, "txn_date");
        $senderId    = JArrayHelper::getValue($data, "sender_id");
        $receiverId  = JArrayHelper::getValue($data, "receiver_id");

        $serviceProvider = JArrayHelper::getValue($data, "service_provider");


        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);

        $row->set("currency_id", $currencyId);
        $row->set("units", $units);
        $row->set("txn_id", $txnId);
        $row->set("txn_amount", $txnAmount);
        $row->set("txn_currency", $txnCurrency);
        $row->set("txn_status", $txnStatus);
        $row->set("txn_date", $txnDate);
        $row->set("txn_date", $txnDate);
        $row->set("sender_id", $senderId);
        $row->set("receiver_id", $receiverId);
        $row->set("service_provider", $serviceProvider);

        $row->store();

        return $row->get("id");
    }
}
