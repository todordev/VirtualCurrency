<?php
/**
 * @package      Virtual Currency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
jimport('Prism.init');
jimport('Virtualcurrency.init');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package      Virtual Currency
 * @subpackage   Fields
 * @since        1.6
 */
class JFormFieldVcgoods extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var     string
     * @since   1.6
     */
    protected $type = 'vcgoods';

    protected $column_value;
    protected $column_text;

    /**
     * Method to get the field options.
     *
     * @return  array   The field option objects.
     * @since   1.6
     */
    protected function getOptions()
    {
        $commodities = new Virtualcurrency\Commodity\Commodities(JFactory::getDbo());
        $commodities->load();

        $this->column_value = (isset($this->element['column_value']) and $this->element['column_value']) ? (string)$this->element['column_value'] : 'id';
        $this->column_text = (isset($this->element['column_text']) and $this->element['column_text']) ? (string)$this->element['column_text'] : 'title';

        // Get the options.
        $options = $commodities->toOptions($this->column_value, $this->column_text);

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
