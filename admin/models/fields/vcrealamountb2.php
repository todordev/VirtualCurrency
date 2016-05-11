<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

jimport('Prism.init');
jimport('Virtualcurrency.init');

class JFormFieldVcrealamountb2 extends JFormField
{
    /**
     * The form field type.
     *
     * @var    string
     *
     * @since  11.1
     */
    protected $type = 'vcrealamountb2';

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     *
     * @since   11.1
     */
    protected function getInput()
    {
        // Initialize some field attributes.
        $size      = $this->element['size'] ? ' size="' . (int)$this->element['size'] . '"' : '';
        $maxLength = $this->element['maxlength'] ? ' maxlength="' . (int)$this->element['maxlength'] . '"' : '';
        $readonly  = ((string)$this->element['readonly'] === 'true') ? ' readonly="readonly"' : '';
        $disabled  = ((string)$this->element['disabled'] === 'true') ? ' disabled="disabled"' : '';
        $class     = (!empty($this->element['class'])) ? ' class="' . (string)$this->element['class'] . '"' : "";

        // Initialize JavaScript field attributes.
        $onchange = $this->element['onchange'] ? ' onchange="' . (string)$this->element['onchange'] . '"' : '';

        $params     = JComponentHelper::getParams('com_virtualcurrency');
        /** @var  $params Joomla\Registry\Registry */

        $currency   = new Virtualcurrency\Currency\RealCurrency(JFactory::getDbo());
        $currency->load($params->get('currency_id'));

        $html = '<div class="input-append">';

        if ($currency->getSymbol()) { // Prepended
            $html = '<div class="input-prepend input-append"><span class="add-on">' . $currency->getSymbol() . '</span>';
        }

        $html .= '<input type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' .
            $class . $size . $disabled . $readonly . $onchange . $maxLength . '/>';

        // Appended
        $html .= '<span class="add-on">' . $currency->getCode() . '</span></div>';

        return $html;
    }
}
