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

class VirtualcurrencyModelAccount extends JModelAdmin
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type    The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Account', $prefix = 'VirtualcurrencyTable', $config = array())
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
        $form = $this->loadForm($this->option . '.account', 'account', array('control' => 'jform', 'load_data' => $loadData));
        if (!$form) {
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
        $data = JFactory::getApplication()->getUserState($this->option . '.edit.account.data', array());
        if (!$data) {
            $data = $this->getItem();

            $moneyFormatter = Virtualcurrency\Intl\Helper::factory('joomla')->getNumberFormatter();

            if ($data->amount !== '') {
                $data->amount = $moneyFormatter->format($data->amount);
            }
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
        $id         = ArrayHelper::getValue($data, 'id');
        $amount     = ArrayHelper::getValue($data, 'amount');
        $currencyId = ArrayHelper::getValue($data, 'currency_id');
        $userId     = ArrayHelper::getValue($data, 'user_id');
        $note       = ArrayHelper::getValue($data, 'note');

        $moneyParser     = Virtualcurrency\Intl\Helper::factory('joomla')->getNumberParser();
        $amount          = ($amount !== '') ? $moneyParser->parse($amount) : '0.00';

        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);

        if (!$row->get('id')) {
            $row->set('currency_id', $currencyId);
        }

        $row->set('amount', $amount);
        $row->set('user_id', $userId);
        $row->set('note', $note);

        $row->store();

        return $row->get('id');
    }
}
