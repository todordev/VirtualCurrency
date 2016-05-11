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

class VirtualcurrencyModelRealCurrency extends JModelAdmin
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type    The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  VirtualcurrencyTableRealCurrency  A database object
     * @since   1.6
     */
    public function getTable($type = 'RealCurrency', $prefix = 'VirtualcurrencyTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param   array   $data     An optional array of data for the form to interrogate.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {

        // Get the form.
        $form = $this->loadForm($this->option . '.realcurrency', 'realcurrency', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState($this->option . '.edit.realcurrency.data', array());
        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Save data into the DB
     *
     * @param array $data   The data about item
     *
     * @return  int   Item ID
     */
    public function save($data)
    {
        $id       = Joomla\Utilities\ArrayHelper::getValue($data, 'id');
        $title    = Joomla\Utilities\ArrayHelper::getValue($data, 'title');
        $code     = Joomla\Utilities\ArrayHelper::getValue($data, 'code');
        $symbol   = Joomla\Utilities\ArrayHelper::getValue($data, 'symbol');
        $position = Joomla\Utilities\ArrayHelper::getValue($data, 'position');

        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);

        $row->set('title', $title);
        $row->set('code', $code);
        $row->set('symbol', $symbol);
        $row->set('position', $position);

        $row->store();

        return $row->get('id');
    }
}
