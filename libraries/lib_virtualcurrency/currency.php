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
 * This class contains methods that are used for managing currency.
 *
 * @package      VirtualCurrency
 * @subpackage   Library
 */
class VirtualCurrencyCurrency
{
    protected $id;
    protected $title;
    protected $description;
    protected $code;
    protected $symbol;
    protected $params;
    protected $published;

    /**
     * @var JDatabaseDriver
     */
    protected $db;

    protected static $instances = array();

    /**
     * Initialize the object.
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency   = new VirtualCurrencyCurrency(JFactory::getDbo());
     * $currency->load($currencyId);
     * </code>
     *
     * @param JDatabaseDriver $db
     *
     */
    public function __construct(JDatabaseDriver $db = null)
    {
        $this->db = $db;
    }

    /**
     * Set a database object.
     *
     * <code>
     * $currency    = new VirtualCurrencyCurrency();
     * $currency->setDb(JFactory::getDbo());
     * </code>
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
     * Create a currency object, store it to the instances and return it.
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency   = VirtualCurrencyCurrency::getInstance(JFactory::getDbo(), $currencyId);
     * </code>
     *
     * @param  JDatabaseDriver $db
     * @param  integer $id
     *
     * @return null|self
     */
    public static function getInstance($db, $id)
    {
        if (!isset(self::$instances[$id])) {
            $currency             = new VirtualCurrencyCurrency($db);
            $currency->load($id);
            self::$instances[$id] = $currency;
        }

        return self::$instances[$id];
    }

    /**
     * Set data to object properties.
     *
     * <code>
     * $data = (
     *  "title"    => "Coins",
     *  "description"  => "My description...",
     *  "code" => "C",
     *  "symbol" => "$"
     * );
     *
     * $currency   = new VirtualCurrencyCurrency(JFactory::getDbo());
     * $currency->bind($data);
     * </code>
     *
     * @param array $data
     * @param array $ignored This is a name of an index, that will be ignored and will not be set as object parameter.
     */
    public function bind($data, $ignored = array())
    {
        // Parse params
        if (!isset($data["params"])) {
            $this->params = array();
        } else {
            $this->setParams($data["params"]);
            unset($data["params"]);
        }

        foreach ($data as $key => $value) {
            if (!in_array($key, $ignored)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Load currency data from database.
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency   = new VirtualCurrencyCurrency(JFactory::getDbo());
     * $currency->load($currencyId);
     * </code>
     *
     * @param int $id
     */
    public function load($id)
    {
        $query = $this->db->getQuery(true);
        $query
            ->select("a.id, a.title, a.description, a.code, a.symbol, a.params, a.published")
            ->from($this->db->quoteName("#__vc_currencies", "a"))
            ->where("a.id = " . (int)$id);

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (!$result) {
            $result = array();
        }

        $this->bind($result);
    }

    /**
     * Store the data in database.
     *
     * <code>
     * $data = (
     *  "title"    => "Coins",
     *  "description"  => "My description...",
     *  "code" => "C",
     *  "symbol" => "$"
     * );
     *
     * $currency   = new VirtualCurrencyCurrency(JFactory::getDbo());
     * $currency->bind($data);
     * $currency->store();
     * </code>
     *
     */
    public function store()
    {
        if (!$this->id) { // Insert
            $this->insertObject();
        } else { // Update
            $this->updateObject();
        }
    }

    protected function insertObject()
    {
        $description   = (!$this->description) ? "NULL" : $this->db->quote($this->description);
        $params        = (!$this->params) ? "NULL" : $this->db->quote($this->params);

        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName("#__vc_currencies"))
            ->set($this->db->quoteName("title") . "=" . $this->db->quote($this->title))
            ->set($this->db->quoteName("description") . "=" . $description)
            ->set($this->db->quoteName("code") . "=" . (int)$this->code)
            ->set($this->db->quoteName("symbol") . "=" . (int)$this->symbol)
            ->set($this->db->quoteName("params") . "=" . $params);

        $this->db->setQuery($query);
        $this->db->execute();

        $this->id = $this->db->insertid();
    }

    protected function updateObject()
    {
        $description   = (!$this->description) ? "NULL" : $this->db->quote($this->description);
        $params        = (!$this->params) ? "NULL" : $this->db->quote($this->params);

        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName("#__vc_currencies"))
            ->set($this->db->quoteName("title") . "=" . $this->db->quote($this->title))
            ->set($this->db->quoteName("description") . "=" . $description)
            ->set($this->db->quoteName("code") . "=" . (int)$this->code)
            ->set($this->db->quoteName("symbol") . "=" . (int)$this->symbol)
            ->set($this->db->quoteName("params") . "=" . $params)
            ->where($this->db->quoteName("id") . "=" . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * This method set the parameters of the virtual currency.
     *
     * <code>
     *  $params = array(
     *      "amount" = 1,
     *      "minimum" = 5
     *  );
     *
     *  $currency    = new VirtualCurrencyCurrency();
     *  $currency->setParams($params);
     * </code>
     *
     * @param  string|array $params
     *
     * @return self
     */
    public function setParams($params)
    {
        if (is_string($params)) {
            $this->params = json_decode($params, true);
        } elseif (is_array($params)) {
            $this->params = $params;
        } else {
            $this->params = array();
        }

        if (!$this->params) {
            $this->params = array();
        }

        return $this;
    }

    /**
     * This method returns a parameter.
     *
     * <code>
     *  $currencyId  = 1;
     *
     *  $currency    = VirtualCurrencyCurrency::getInstance(JFactory::getDbo, $currencyId);
     *
     *  // Return minimum units that can be bought.
     *  $minimum = $this->getParam("minimum");
     * </code>
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function getParam($key)
    {
        if (is_array($this->params) and isset($this->params[$key])) {
            return $this->params[$key];
        }

        return null;
    }

    /**
     * This method set a parameter.
     *
     * <code>
     *  $currencyId  = 1;
     *
     *  $currency    = VirtualCurrencyCurrency::getInstance(JFactory::getDbo, $currencyId);
     *
     *  // Set the a value of minimum units that can be bought.
     *  $this->setParam("minimum", 10);
     * </code>
     *
     * @param  string $key
     * @param  mixed $value
     *
     * @return self
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;

        return $this;
    }

    /**
     * This method calculates the amount of the units.
     * You have to give the number of your units that you would like to calculate.
     * The method will calculate the price of those units.
     *
     * <code>
     *  $currencyId  = 1;
     *
     *  $currency    = VirtualCurrencyCurrency::getInstance(JFactory::getDbo, $currencyId);
     *
     *  // It is the number of units, that I would like to buy.
     *  $unitsNumber = 10;
     *  $amount      = $currency->calculate($unitsNumber);
     * </code>
     *
     * @param  integer $units
     *
     * @return float Amount
     */
    public function calculate($units)
    {
        $amount = 0;
        if (!empty($units)) {
            $amount = $this->getParam("amount");
            $amount = $amount * $units;
        }

        return $amount;
    }

    /**
     * This method generates string, using symbol or code of the currency.
     * That string represents an amount in the virtual currency.
     *
     * <code>
     *  $currencyId  = 1;
     *
     *  $currency    = VirtualCurrencyCurrency::getInstance(JFactory::getDbo, $currencyId);
     *
     *  // It is the amount that I would like to present.
     *  $amount      = 100;
     *  $string      = $currency->getAmountString($amount);
     * </code>
     *
     * @param mixed $value This is a value used in the amount string.
     *
     * @return string Amount
     */
    public function getAmountString($value)
    {
        if (!empty($this->symbol)) { // Symbol
            $amount = $this->symbol . $value;
        } else { // Currency Code
            $amount = $value . $this->code;
        }

        return $amount;
    }

    /**
     * Return the ID of the virtual currency.
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency    = new VirtualCurrencyCurrency(JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * if (!$currency->getId()) {
     * ...
     * }
     * </code>
     *
     * @return  int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return the description of the unit (virtual currency).
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency    = new VirtualCurrencyCurrency(JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * $description = $currency->getDescription();
     * </code>
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Return the code (abbreviation) of the unit (virtual currency).
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency    = new VirtualCurrencyCurrency(JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * $code = $currency->getCode();
     * </code>
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Return the symbol of the unit (virtual currency).
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency    = new VirtualCurrencyCurrency(JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * $symbol = $currency->getSymbol();
     * </code>
     *
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * Return the title of the virtual currency.
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency    = new VirtualCurrencyCurrency(JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * $title = $currency->getTitle();
     * </code>
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns an associative array of object properties.
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency    = new VirtualCurrencyCurrency(JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * $properties = $currency->getProperties();
     * </code>
     *
     * @return  array
     */
    public function getProperties()
    {
        $vars = get_object_vars($this);

        foreach ($vars as $key => $value) {
            if (strcmp("db", $key) == 0) {
                unset($vars[$key]);
            }
        }

        return $vars;
    }
}
