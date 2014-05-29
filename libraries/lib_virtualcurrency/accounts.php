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
 * This class provides functionality
 * for managing user accounts.
 *
 * @package      VirtualCurrency
 * @subpackage   Library
 */
class VirtualCurrencyAccounts implements Iterator, Countable, ArrayAccess
{
    /**
     * @var JDatabaseDriver
     */
    protected $db;

    protected $items = array();

    protected $position = 0;

    /**
     * Initialize the object.
     *
     * <code>
     * $userId    = 1;
     *
     * $accounts  = new VirtualCurrencyAccounts(JFactory::getDbo());
     * $accounts->load($userId);
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
     * $userId  = 1;
     *
     * $accounts    = new VirtualCurrencyAccounts();
     * $accounts->setDb(JFactory::getDbo());
     * $accounts->load($userId);
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
     * Load the data for all user accounts by userId
     *
     * <code>
     * $userId    = 1;
     *
     * $accounts  = new VirtualCurrencyAccounts(JFactory::getDbo());
     * $accounts->load($userId);
     * </code>
     *
     * @param integer $userId
     */
    public function load($userId)
    {
        $query = $this->db->getQuery(true);

        $query
            ->select(
                "a.id, a.amount, a.note, a.currency_id, a.user_id, " .
                "b.title, b.code, b.symbol, " .
                "c.name"
            )
            ->from($this->db->quoteName("#__vc_accounts", "a"))
            ->innerJoin($this->db->quoteName("#__vc_currencies", "b") . " ON a.currency_id = b.id")
            ->innerJoin($this->db->quoteName("#__users", "c") . " ON a.user_id = c.id")
            ->where("a.user_id = " . (int)$userId);

        $this->db->setQuery($query);
        $results = $this->db->loadAssocList();

        if (!empty($results)) {
            $this->items = $results;
        }
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
