<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing real currency.
 *
 * @package      VirtualCurrency
 * @subpackage   Library
 */
class VirtualCurrencyRealCurrency
{
    protected $id;
    protected $title;
    protected $abbr;
    protected $symbol;
    protected $position;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $options;

    /**
     * Database driver.
     *
     * @var JDatabaseDriver
     */
    protected $db;

    protected static $instances = array();

    /**
     * Initialize the object.
     *
     * <code>
     * $currencyId = 1;
     * $currency   = new VirtualCurrencyRealCurrency(JFactory::getDbo());
     * $currency->load($currencyId);
     * </code>
     *
     * @param JDatabaseDriver $db
     */
    public function __construct(JDatabaseDriver $db = null)
    {
        $this->db = $db;
        $this->options = new JRegistry;
    }

    /**
     * Create an object or return existing one.
     *
     * <code>
     * $currencyId = 1;
     *
     * $options    = new JRegistry();
     * $options->set("intl", true);
     * $options->set("format", "2/./,");
     *
     * $currency   = VirtualCurrencyRealCurrency::getInstance(JFactory::getDbo(), $currencyId, $options);
     * </code>
     *
     * @param JDatabaseDriver $db
     * @param int             $id
     * @param Joomla\Registry\Registry             $options
     *
     * @return null|VirtualCurrencyRealCurrency
     */
    public static function getInstance(JDatabaseDriver $db, $id, $options = null)
    {
        if (!isset(self::$instances[$id])) {
            $item = new VirtualCurrencyRealCurrency($db);
            $item->load($id);

            if (!is_null($options) and ($options instanceof JRegistry)) {
                $item->setOption("intl", $options->get("locale_intl", false));
                $item->setOption("format", $options->get("amount_format", false));
            }

            self::$instances[$id] = $item;
        }

        return self::$instances[$id];
    }

    /**
     * Set database object.
     *
     * @param JDatabaseDriver $db
     *
     * @return self
     */
    public function setDb(JDatabaseDriver $db)
    {
        $this->db = $db;
        return $this;
    }

    /**
     * Load currency data from database.
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency   = new VirtualCurrencyRealCurrency();
     * $currency->setDb(JFactory::getDbo());
     * $currency->load($currencyId);
     * </code>
     *
     * @param int $id
     */
    public function load($id)
    {
        $query = $this->db->getQuery(true);
        $query
            ->select("a.id, a.title, a.abbr, a.symbol, a.position")
            ->from($this->db->quoteName("#__vc_realcurrencies", "a"))
            ->where("a.id = " . (int)$id);

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (!$result) {
            $result = array();
        }

        $this->bind($result);
    }

    /**
     * Set data about currency to object parameters.
     *
     * <code>
     * $data = array(
     *  "title"  => "Pound sterling",
     *  "symbol" => "£"
     * );
     *
     * $currency   = new VirtualCurrencyREalCurrency();
     * $currency->bind($data);
     * </code>
     *
     * @param       $data
     * @param array $ignored
     *
     */
    public function bind($data, $ignored = array())
    {
        foreach ($data as $key => $value) {
            if (!in_array($key, $ignored)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * This method generates an amount using symbol or code of the currency.
     *
     * @param mixed  $value This is a value used in the amount string. This can be float, integer,...
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency   = VirtualCurrencyRealCurrency::getInstance(JFactory::getDbo(), $currencyId);
     * echo $currency->getAmountString(100);
     * </code>
     *
     * @return string
     */
    public function getAmountString($value)
    {
        $intl   = (bool)$this->options->get("intl", false);
        $format = $this->options->get("format");

        if (!$intl and !empty($format)) {
            $value = $this->formatAmount($value);
        }

        // Use PHP Intl library.
        if ($intl and extension_loaded('intl')) { // Generate currency string using PHP NumberFormatter ( Internationalization Functions )

            $locale = $this->options->get("locale");

            // Get current locale code.
            if (!$locale) {
                $lang   = JFactory::getLanguage();
                $locale = str_replace("-", "_", $lang->getTag());
            }

            $numberFormat = new NumberFormatter($locale, NumberFormatter::CURRENCY);
            $amount       = $numberFormat->formatCurrency($value, $this->abbr);

        } else { // Generate a custom currency string.

            if (!empty($this->symbol)) { // Symbol

                if (0 == $this->position) { // Symbol at beginning.
                    $amount = $this->symbol . $value;
                } else { // Symbol at end.
                    $amount = $value . $this->symbol;
                }

            } else { // Code
                $amount = $value . $this->abbr;
            }

        }

        return $amount;
    }

    /**
     * Return the ID of a real currency.
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency   = VirtualCurrencyRealCurrency::getInstance(JFactory::getDbo(), $currencyId);
     *
     * if (!$currency->getId()) {
     * ....
     * }
     * </code>
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return the code (abbreviation) of a real currency.
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency   = new VirtualCurrencyRealCurrency(JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * $code = $currency->getAbbr();
     *
     * @return string
     */
    public function getAbbr()
    {
        return $this->abbr;
    }

    /**
     * Return the symbol of a real currency.
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency   = new VirtualCurrencyRealCurrency(JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * $symbol = $currency->getSymbol();
     *
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * Use this method to set object options.
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency   = VirtualCurrencyRealCurrency::getInstance(JFactory::getDbo(), $currencyId);
     * $currency->setOption("intl", true);
     * $currency->setOption("locale", "en_GB");
     * </code>
     *
     * @param string $key Options like "intl", "locale",...
     * @param mixed $value
     */
    public function setOption($key, $value)
    {
        $this->options->set($key, $value);
    }

    protected function formatAmount($value)
    {
        $format = $this->options->get("format");
        $format = explode("/", $format);

        if (!empty($format)) {
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
}
