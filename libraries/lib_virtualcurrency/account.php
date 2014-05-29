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
 * This class contains methods,
 * which are used for managing virtual bank account.
 *
 * @package      VirtualCurrency
 * @subpackage   Library
 */
class VirtualCurrencyAccount
{
    protected $id;
    protected $amount;
    protected $note;
    protected $user_id;
    protected $currency_id;

    /**
     * @var JDatabaseDriver
     */
    protected $db;

    /**
     * This method initializes the object.
     *
     * <code>
     *  // Get user account by keys
     *  $keys = array(
     *      "user_id" => 1,
     *      "currency_id" => 10
     *  );
     *
     *  $account = new VirtualCurrencyAccount(JFactory::getDbo());
     *  $account->load($keys);
     *
     *  // Get user account by account ID
     *  $accountId = 1;
     *
     *  $account   = new VirtualCurrencyAccount();
     *  $account->load($accountId);
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
     * $accountId  = 1;
     *
     * $account    = new VirtualCurrencyAccount();
     * $account->setDb(JFactory::getDbo());
     * $account->load($accountId);
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
     * Increase the number of units ( virtual currency ).
     *
     * <code>
     *  // Get user account by account ID
     *  $accountId = 1;
     *
     *  $account   = new VirtualCurrencyAccount();
     *  $account->setDb(JFactory::getDbo());
     *  $account->load($accountId);
     *
     *  // Increase the amount and store the new value.
     *  $account->increaseAmount(50);
     *  $account->store();
     * </code>
     *
     * @param float $value
     *
     * @return self
     */
    public function increaseAmount($value)
    {
        if (is_numeric($value)) {
            $this->amount += $value;
        }

        return $this;
    }

    /**
     * Decrease the number of units ( virtual currency )
     *
     * <code>
     *  // Get user account by account ID
     *  $accountId = 1;
     *
     *  $account   = new VirtualCurrencyAccount();
     *  $account->setDb(JFactory::getDbo());
     *  $account->load($accountId);
     *
     *  // Decrease the amount and store the new value.
     *  $account->decreaseAmount(50);
     *  $account->store();
     * </code>
     *
     * @param float $value
     *
     * @return self
     */
    public function decreaseAmount($value)
    {
        if (is_numeric($value)) {
            $this->amount -= $value;
        }

        return $this;
    }

    /**
     * Load account data from database.
     *
     * <code>
     * $accountId = 1;
     *
     * $account   = new VirtualCurrencyAccount(JFactory::getDbo());
     * $account->load($accountId);
     * </code>
     *
     * @param array|int $keys
     */
    public function load($keys)
    {
        $query = $this->db->getQuery(true);
        $query
            ->select("a.id, a.amount, a.note, a.user_id, a.currency_id")
            ->from($this->db->quoteName("#__vc_accounts", "a"));

        if (!is_array($keys)) {
            $query->where("a.id = " . (int)$keys);
        } else {
            foreach ($keys as $key => $value) {
                $query->where($this->db->quoteName("a.".$key) . "=" . $this->db->quote($value));
            }
        }

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (!$result) {
            $result = array();
        }

        $this->bind($result);
    }

    /**
     * Set data to object properties.
     *
     * <code>
     * $data = (
     *  "amount"  => 100.00,
     *  "note"  => "note...",
     *  "user_id" => 1,
     *  "currency_id" => 2
     * );
     *
     * $account   = new VirtualCurrencyAccount(JFactory::getDbo());
     * $account->bind($data);
     * </code>
     *
     * @param array $data
     * @param array $ignored This is a name of an index, that will be ignored and will not be set as object parameter.
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
     * Store the data in database.
     *
     * <code>
     * $data = (
     *  "amount"  => 100.00,
     *  "note"  => "note...",
     *  "user_id" => 1,
     *  "currency_id" => 2
     * );
     *
     * $account   = new VirtualCurrencyAccount(JFactory::getDbo());
     * $account->bind($data);
     * $account->store();
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
        $note   = (!$this->note) ? "NULL" : $this->db->quote($this->note);

        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName("#__vc_accounts"))
            ->set($this->db->quoteName("amount") . "=" . $this->db->quote($this->amount))
            ->set($this->db->quoteName("note") . "=" . $note)
            ->set($this->db->quoteName("user_id") . "=" . (int)$this->user_id)
            ->set($this->db->quoteName("currency_id") . "=" . (int)$this->currency_id);

        $this->db->setQuery($query);
        $this->db->execute();

        $this->id = $this->db->insertid();
    }

    protected function updateObject()
    {
        $note   = (!$this->note) ? "NULL" : $this->db->quote($this->note);

        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName("#__vc_accounts"))
            ->set($this->db->quoteName("amount") . "=" . $this->db->quote($this->amount))
            ->set($this->db->quoteName("note") . "=" . $note)
            ->set($this->db->quoteName("user_id") . "=" . (int)$this->user_id)
            ->set($this->db->quoteName("currency_id") . "=" . (int)$this->currency_id)
            ->where($this->db->quoteName("id") . "=" . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Update the amount of the account.
     *
     * <code>
     *  // Get user account by account ID
     *  $accountId = 1;
     *
     *  $account   = new VirtualCurrencyAccount();
     *  $account->setDb(JFactory::getDbo());
     *  $account->load($accountId);
     *
     *  // Increase the amount and store the new value.
     *  $account->increaseAmount(50);
     *  $account->updateAmount();
     * </code>
     */
    public function updateAmount()
    {
        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName("#__vc_accounts"))
            ->set($this->db->quoteName("amount") . "=" . $this->db->quote($this->amount))
            ->where($this->db->quoteName("id") . "=" . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }
}
