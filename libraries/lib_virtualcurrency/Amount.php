<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Amounts
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency;

use Joomla\Registry\Registry;
use Virtualcurrency\Currency\CurrencyInterface;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing an amount.
 *
 * @package      Challenges
 * @subpackage   Amounts
 */
class Amount
{
    /**
     * Amount value.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Currency object.
     *
     * @var CurrencyInterface
     */
    protected $currency;

    /**
     * @var Registry
     */
    protected $options;

    /**
     * Initialize the object.
     *
     * <code>
     * $options    = new Registry();
     * $options->set("intl", true);
     * $options->set("locale", "en_GB");
     * $options->set("format", "2/,/.");
     *
     * $amount = 1,500.25;
     *
     * $amount   = new Virtualcurrency\Amount($amount, $options);
     * </code>
     *
     * @param Registry $options
     * @param float $value
     */
    public function __construct(Registry $options = null, $value = 0.00)
    {
        $this->value = $value;

        // Create options object.
        $this->options = new Registry;

        if ($options !== null and ($options instanceof Registry)) {
            $this->setOption('intl', $options->get('locale_intl', false));
            $this->setOption('format', $options->get('amount_format', ''));
            $this->setOption('fraction_digits', $options->get('fraction_digits', 2));
        }
    }

    /**
     * Set the currency object.
     *
     * <code>
     * $currencyId = 1;
     * $currency   = Virtualcurrency\Currency\Currency::getInstance(\JFactory::getDbo(), $currencyId);
     *
     * $amount   = new Virtualcurrency\Amount();
     * $amount->setCurrency($currency);
     * </code>
     *
     * @param CurrencyInterface $currency
     *
     * @return self
     */
    public function setCurrency(CurrencyInterface $currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Return the currency object.
     *
     * <code>
     * $amount   = new Virtualcurrency\Amount();
     * $currency = $amount->getCurrency();
     * </code>
     *
     * @return null|CurrencyInterface
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * This method returns an amount as currency, with a symbol and currency code.
     *
     * <code>
     * // Create currency object.
     * $currencyId = 1;
     * $currency   = Virtualcurrency\Currency\Currency::getInstance(JFactory::getDbo(), $currencyId);
     *
     * // Create amount object.
     * $options    = new Joomla\Registry\Registry();
     * $options->set("intl", true);
     * $options->set("locale", "en_GB");
     * $options->set("format", "2/,/.");
     *
     * $amount = 1500.25;
     *
     * $amount   = new Virtualcurrency\Amount($amount, $options);
     * $amount->setCurrency($currency);
     *
     * // Return $1,500.25 or 1,500.25USD.
     * echo $amount->formatCurrency();
     * </code>
     *
     * @return string
     */
    public function formatCurrency()
    {
        $intl             = (bool)$this->options->get('intl', false);
        $fractionDigits   = abs($this->options->get('fraction_digits', 2));
        $format           = $this->options->get('format');

        $amount           = $this->value;

        // Use number_format.
        if (!$intl and \JString::strlen($format) > 0) {
            $value = $this->formatNumber($this->value);

            if (!$this->currency->getSymbol()) { // Symbol
                $amount = $value . ' '. $this->currency->getCode();
            } else { // Code
                if (0 === $this->currency->getPosition()) { // Symbol at beginning.
                    $amount = $this->currency->getSymbol() . $value;
                } else { // Symbol at end.
                    $amount = $value . $this->currency->getSymbol();
                }
            }
        }

        // Use PHP Intl library.
        if ($intl and extension_loaded('intl')) { // Generate currency string using PHP NumberFormatter ( Internationalization Functions )
            $locale = $this->options->get('locale');

            // Get current locale code.
            if (!$locale) {
                $lang   = \JFactory::getLanguage();
                $locale = str_replace('-', '_', $lang->getTag());
            }

            $numberFormat = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
            $numberFormat->setTextAttribute(\NumberFormatter::CURRENCY_CODE, $this->currency->getCode());
            $numberFormat->setAttribute(\NumberFormatter::FRACTION_DIGITS, $fractionDigits);

            $amount       = $numberFormat->formatCurrency($this->value, $this->currency->getCode());
        }

        return $amount;
    }

    /**
     * This method formats an amount as decimal value depending of options or locale.
     *
     * <code>
     * $options    = new Registry();
     * $options->set("intl", true);
     * $options->set("locale", "en_GB");
     * $options->set("format", "2/,/.");
     *
     * $amount   = 1500.25;
     *
     * $amount   = new Virtualcurrency\Amount($amount, $options);
     *
     * // Return 1,500.25
     * echo $amount->format();
     * </code>
     *
     * @return string
     */
    public function format()
    {
        $intl             = (bool)$this->options->get('intl', false);
        $fractionDigits   = abs($this->options->get('fraction_digits', 2));

        // Format the amount by function number_format.
        $format           = $this->options->get('format');
        if (!$intl and \JString::strlen($format) > 0) {
            return $this->formatNumber($this->value);
        }

        // Use PHP Intl library to format the amount.
        if ($intl and extension_loaded('intl')) { // Generate currency string using PHP NumberFormatter ( Internationalization Functions )
            $locale = $this->options->get('locale');

            // Get current locale code.
            if (!$locale) {
                $lang   = \JFactory::getLanguage();
                $locale = str_replace('-', '_', $lang->getTag());
            }

            $numberFormat = new \NumberFormatter($locale, \NumberFormatter::DECIMAL);
            $numberFormat->setAttribute(\NumberFormatter::FRACTION_DIGITS, $fractionDigits);

            return $numberFormat->format($this->value, \NumberFormatter::TYPE_DOUBLE);
        }

        return $this->value;
    }

    /**
     * Use this method to parse currency string.
     *
     * <code>
     * $amount   = 1,500.25;
     * $amount   = new Virtualcurrency\Amount($amount);
     *
     * // Will return 1500.25.
     * $goal = $currency->parse();
     * </code>
     *
     * @return float
     */
    public function parse()
    {
        $intl             = (bool)$this->getOption('intl', false);
        $fractionDigits   = abs($this->getOption('fraction_digits', 2));

        // Use PHP Intl library to format the amount.
        if ($intl and extension_loaded('intl')) { // Generate currency string using PHP NumberFormatter ( Internationalization Functions )
            $locale = $this->getOption('locale');

            // Get current locale code.
            if (!$locale) {
                $lang   = \JFactory::getLanguage();
                $locale = str_replace('-', '_', $lang->getTag());
            }

            $numberFormat = new \NumberFormatter($locale, \NumberFormatter::DECIMAL);
            $numberFormat->setAttribute(\NumberFormatter::FRACTION_DIGITS, $fractionDigits);

            $result = $numberFormat->parse($this->value, \NumberFormatter::TYPE_DOUBLE);

        } else {
            $result = $this->parseAmount($this->value);
        }

        return (float)$result;
    }

    /**
     * Format amount string to decimal value.
     *
     * @param $value
     *
     * @return float
     */
    protected function parseAmount($value)
    {
        // Parse a string like this 1.560,25. The result is 1560.25.
        if (1 === preg_match('/\.?[0-9]{3},[0-9]{1,3}$/i', $value)) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
            return (float)$value;
        }

        // Parse a string like this 45,00. The result is 45.00.
        if (1 === preg_match('/,[0-9]{1,3}$/i', $value)) {
            $value = str_replace(',', '.', $value);
            return (float)$value;
        }

        // Parse a string like this 1,560.25. The result is 1560.25.
        if (1 === preg_match('/^[0-9]+,[0-9]{3}\./i', $value)) {
            $value = str_replace(',', '', $value);
            return (float)$value;
        }

        return (float)$value;
    }

    /**
     * Set the amount value.
     *
     * <code>
     * $amount   = 1,500.25;
     *
     * $amount   = new Virtualcurrency\Amount();
     * $amount->setValue($amount);
     * </code>
     *
     * @param float $value
     *
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Format number.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function formatNumber($value)
    {
        if (!$value) {
            return 0;
        }

        $format = $this->options->get('format');
        $format = explode('/', $format);

        if ((false !== $format) and count($format) > 0) {

            $value = (false !== strpos($value, ',')) ? $this->parse() : $value;

            $count = count($format);

            switch ($count) {
                case 1:
                    $value = number_format($value, $format[0]);
                    break;

                case 2:
                    $value = number_format($value, $format[0], $format[1]);
                    break;

                case 3:
                    $value = number_format($value, $format[0], $format[1], $format[2]);
                    break;
            }
        }

        return $value;
    }

    /**
     * Use this method to set object options.
     *
     * <code>
     * $amount   = new Virtualcurrency\Amount;
     * $amount->setOption("intl", true);
     * $amount->setOption("locale", "en_GB");
     * </code>
     *
     * @param string $key Options like "intl", "locale",...
     * @param mixed $value
     */
    public function setOption($key, $value)
    {
        $this->options->set($key, $value);
    }

    /**
     * Return an option value.
     *
     * <code>
     * $amount   = new Virtualcurrency\Amount();
     *
     * if ($amount->getOption("intl")) {
     * ....
     * }
     * </code>
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getOption($key, $default = null)
    {
        return $this->options->get($key, $default);
    }
}
