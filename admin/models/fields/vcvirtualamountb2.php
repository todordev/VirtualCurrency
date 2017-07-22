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
class JFormFieldVcvirtualamountb2 extends JFormField
{
    /**
     * The form field type.
     *
     * @var     string
     * @since   1.6
     */
    protected $type = 'vcvirtualamountb2';

    protected function getInput()
    {
        // Initialize some field attributes.
        $size      = $this->element['size'] ? ' size="' . (int)$this->element['size'] . '"' : '';
        $maxLength = $this->element['maxlength'] ? ' maxlength="' . (int)$this->element['maxlength'] . '"' : '';
        $readonly  = ((string)$this->element['readonly'] === 'true') ? ' readonly="readonly"' : '';
        $disabled  = ((string)$this->element['disabled'] === 'true') ? ' disabled="disabled"' : '';
        $class     = (!empty($this->element['class'])) ? ' class="' . (string)$this->element['class'] . '"' : '';

        // Initialize JavaScript field attributes.
        $onchange = $this->element['onchange'] ? ' onchange="' . (string)$this->element['onchange'] . '"' : '';

        $mapper       = new Virtualcurrency\Currency\Mapper(new \Virtualcurrency\Currency\Gateway\JoomlaGateway(JFactory::getDbo()));
        $repository   = new Virtualcurrency\Currency\Repository($mapper);
        $currencies   = $repository->fetchAll();
        /** @var Virtualcurrency\Currency\Currencies $currencies */

        // Get the options.
        $options = $currencies->toOptions('id', 'code');

        $value  = json_decode($this->value, true);
        if (!is_array($value)) {
            $value = array();
        }

        $amount     = (!array_key_exists('amount', $value)) ? '' : $value['amount'];
        $currencyId = (!array_key_exists('currency_id', $value)) ? '' : $value['currency_id'];

        $html[] = '<input type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="' . htmlspecialchars($amount, ENT_COMPAT, 'UTF-8') . '"' .
            $class . $size . $disabled . $readonly . $onchange . $maxLength . '/>';

        $html[] = JHtml::_('select.genericlist', $options, 'virtual_currency_id', '', 'value', 'text', (int)$currencyId, 'js-vc-currency');

        return implode("\n", $html);
    }
}
