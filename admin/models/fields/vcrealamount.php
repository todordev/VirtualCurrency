<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

class JFormFieldVcRealAmount extends JFormField
{
    /**
     * The form field type.
     *
     * @var    string
     *
     * @since  11.1
     */
    protected $type = 'vcrealamount';

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
        $readonly  = ((string)$this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
        $disabled  = ((string)$this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
        $class     = (!empty($this->element['class'])) ? ' class="' . (string)$this->element['class'] . '"' : "";

        // Initialize JavaScript field attributes.
        $onchange = $this->element['onchange'] ? ' onchange="' . (string)$this->element['onchange'] . '"' : '';

        $params     = JComponentHelper::getParams("com_virtualcurrency");
        /** @var  $params Joomla\Registry\Registry */

        $currencyId = $params->get("payments_currency_id");

        jimport("virtualcurrency.realcurrency");
        $currency = VirtualCurrencyRealCurrency::getInstance(JFactory::getDbo(), $currencyId, $params);

        if ($currency->getSymbol()) { // Prepended
            $html = '<div class="input-prepend input-append"><span class="add-on">' . $currency->getSymbol() . '</span>';
        } else { // Append
            $html = '<div class="input-append">';
        }

        $html .= '<input type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' .
            $class . $size . $disabled . $readonly . $onchange . $maxLength . '/>';

        // Appended
        $html .= '<span class="add-on">' . $currency->getAbbr() . '</span></div>';

        return $html;
    }
}
