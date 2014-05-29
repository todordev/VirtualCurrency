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
 * This class contains methods used for managing a set of currencies.
 *
 * @package      VirtualCurrency
 * @subpackage   Library
 */
class VirtualCurrencyCurrencies implements Iterator, Countable, ArrayAccess
{
    /**
     * @var JDatabaseDriver
     */
    protected $db;

    protected $items = array();

    protected $position = 0;

    /**
     * Initialize the object and load currencies data.
     *
     * <code>
     * // The state could be 1 = published, 0 = unpublished, null = all
     * $options = array(
     *     "state" => 1
     * );
     *
     * $currencies = new VirtualCurrencyCurrencies(JFactory::getDbo());
     * $currencies->load($options);
     * </code>
     *
     * @param JDatabaseDriver $db
     */
    public function __construct(JDatabaseDriver $db = null)
    {
        $this->db = $db;
    }

    /**
     * Set a database object.
     *
     * <code>
     * $currencies    = new VirtualCurrencyCurrencies();
     * $currencies->setDb(JFactory::getDbo());
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
     * Load currencies data.
     *
     * <code>
     *  // The state could be 1 = published, 0 = unpublished, null = all
     *  $options = array(
     *      "state" => 1
     *  );
     *
     *  $currencies = new VirtualCurrencyCurrencies();
     *  $currencies->setDb(JFactory::getDbo());
     *  $currencies->load($options);
     * </code>
     *
     * @param array $options
     *
     */
    public function load($options = array())
    {
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.title, a.code, a.symbol, a.params, a.published")
            ->from($this->db->quoteName("#__vc_currencies", "a"));

        $state = JArrayHelper::getValue($options, "state");
        if (!is_null($state)) {
            $state = (!$state) ? 0 : 1;
            $query->where("a.published = " . (int)$state);
        }

        $this->db->setQuery($query);
        $results = $this->db->loadAssocList();

        if (!empty($results)) {

            foreach ($results as $key => $value) {

                if (!empty($value["params"])) {
                    $results[$key]["params"] = json_decode($value["params"], true);
                }
            }

            $this->items = $results;
        }

    }

    /**
     * Return a currency data, getting it by currency ID.
     *
     * <code>
     * $currencyId = 1;
     *
     * $currencies = new VirtualCurrencyCurrencies(JFactory::getDbo());
     * $currencies->load();
     *
     * $currency   = $currencies->getCurrency($currencyId);
     * </code>
     *
     * @param integer $id
     *
     * @return VirtualCurrencyCurrency|null
     */
    public function getCurrency($id)
    {
        foreach ($this->items as $item) {
            if ($id == $item["id"]) {
                $currency = new VirtualCurrencyCurrency();
                $currency->bind($item);
                return $currency;
            }
        }

        return null;
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
}
