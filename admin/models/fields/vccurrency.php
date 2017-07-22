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
class JFormFieldVccurrency extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var     string
     * @since   1.6
     */
    protected $type = 'vccurrency';

    /**
     * Method to get the field options.
     *
     * @return  array   The field option objects.
     * @since   1.6
     */
    protected function getOptions()
    {
        $mapper     = new Virtualcurrency\Currency\Mapper(new Virtualcurrency\Currency\Gateway\JoomlaGateway(JFactory::getDbo()));
        $repository = new Virtualcurrency\Currency\Repository($mapper);
        $currencies = $repository->fetchAll();

        $columnValue = (isset($this->element['column_value']) and $this->element['column_value']) ? (string)$this->element['column_value'] : 'id';
        $columnText  = (isset($this->element['column_text']) and $this->element['column_text']) ? (string)$this->element['column_text'] : 'title';

        // Get the options.
        $options = $currencies->toOptions($columnValue, $columnText);

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
