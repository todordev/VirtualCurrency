<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

jimport("virtualcurrency.currency");

/**
 * This class provides functionality that manage real currencies.
 *
 * @package      VirtualCurrency
 * @subpackage   Library
 */
class VirtualCurrencyRealCurrencies implements Iterator, Countable, ArrayAccess
{
    protected $items = array();

    protected $position = 0;

    /**
     * Database driver.
     *
     * @var JDatabaseDriver
     */
    protected $db;

    /**
     * Initialize the object.
     *
     * <code>
     * $currencies   = new VirtualCurrencyRealCurrencies(JFactory::getDbo());
     * </code>
     *
     * @param JDatabaseDriver $db
     */
    public function __construct(JDatabaseDriver $db)
    {
        $this->db = $db;
    }

    /**
     * Load currencies data by ID from database.
     *
     * <code>
     * $ids = array(1,2,3,4,5);
     *
     * $currencies   = new VirtualCurrencyRealCurrencies(JFactory::getDbo());
     * $currencies->load($ids);
     *
     * foreach($currencies as $currency) {
     *   echo $currency["title"];
     *   echo $currency["abbr"];
     * }
     *
     * </code>
     *
     * @param array $ids
     */
    public function load($ids = array())
    {
        // Load project data
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.title, a.abbr, a.symbol, a.position")
            ->from($this->db->quoteName("#__vc_realcurrencies", "a"));

        if (!empty($ids)) {
            JArrayHelper::toInteger($ids);
            $query->where("a.id IN ( " . implode(",", $ids) . " )");
        }

        $this->db->setQuery($query);
        $results = $this->db->loadAssocList();

        if (!$results) {
            $results = array();
        }

        $this->items = $results;
    }

    /**
     * Load currencies data by abbreviation from database.
     *
     * <code>
     * $ids = array("GBP", "EUR", "USD");
     *
     * $currencies   = new VirtualCurrencyRealCurrencies(JFactory::getDbo());
     * $currencies->loadByAbbr($ids);
     *
     * foreach($currencies as $currency) {
     *   echo $currency["title"];
     *   echo $currency["abbr"];
     * }
     * </code>
     *
     * @param array $ids
     */
    public function loadByAbbr($ids = array())
    {
        // Load project data
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.title, a.abbr, a.symbol, a.position")
            ->from($this->db->quoteName("#__vc_realcurrencies", "a"));

        if (!empty($ids)) {

            foreach ($ids as $key => $value) {
                $ids[$key] = $this->db->quote($value);
            }

            $query->where("a.abbr IN ( " . implode(",", $ids) . " )");
        }

        $this->db->setQuery($query);
        $results = $this->db->loadAssocList();

        if (!$results) {
            $results = array();
        }

        $this->items = $results;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return (!isset($this->items[$this->position])) ? null : $this->items[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->items[$this->position]);
    }

    public function count()
    {
        return (int)count($this->items);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    /**
     * Create a currency object by abbreviation and return it.
     *
     * <code>
     * $ids = array(1,2,3,4,5);
     *
     * $currencies   = new VirtualCurrencyRealCurrencies(JFactory::getDbo());
     * $currencies->load($ids);
     *
     * $currency = $currencies->getCurrencyByAbbr("EUR");
     * </code>
     *
     * @param string $abbr
     *
     * @throws UnexpectedValueException
     *
     * @return null|VirtualCurrencyRealCurrency
     */
    public function getCurrencyByAbbr($abbr)
    {
        $currency = null;

        foreach ($this->items as $item) {
            if (strcmp($abbr, $item["abbr"]) == 0) {

                $currency = new VirtualCurrencyRealCurrency();
                $currency->bind($item);

                break;
            }
        }

        return $currency;
    }

    /**
     * Create a currency object and return it.
     *
     * <code>
     * $ids = array(1,2,3,4,5);
     *
     * $currencies   = new VirtualCurrencyRealCurrencies(JFactory::getDbo());
     * $currencies->load($ids);
     *
     * $currency = $currencies->getCurrencyByAbbr(1);
     * </code>
     *
     * @param string $id
     *
     * @return null|VirtualCurrencyRealCurrency
     */
    public function getCurrency($id)
    {
        $currency = null;

        foreach ($this->items as $item) {

            if ($id == $item["id"]) {
                $currency = new VirtualCurrencyRealCurrency();
                $currency->bind($item);

                break;
            }
        }
        return $currency;
    }
}
