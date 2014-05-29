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
 * This class contains methods that are used for managing transactions.
 *
 * @package      VirtualCurrency
 * @subpackage   Library
 */
class VirtualCurrencyTransaction
{
    protected $id;
    protected $units;
    protected $txn_id;
    protected $txn_amount;
    protected $txn_currency;
    protected $txn_status;

    protected $txn_date;

    protected $service_provider;
    protected $currency_id;
    protected $sender_id;
    protected $receiver_id;

    /**
     * @var JDatabaseDriver
     */
    protected $db;

    /**
     * Initialize the object.
     *
     * <code>
     * $txnId = 1;
     *
     * $transaction   = new VirtualCurrencyTransaction(JFactory::getDbo());
     * $transaction->load($txnId);
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
     * $transaction    = new VirtualCurrencyTransaction();
     * $transaction->setDb(JFactory::getDbo());
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
     * Load account data from database.
     *
     * <code>
     * $txnId = 1;
     *
     * $transaction   = new VirtualCurrencyTransaction(JFactory::getDbo());
     * $transaction->load($txnId);
     * </code>
     *
     * @param array|int $keys
     */
    public function load($keys)
    {
        $query = $this->db->getQuery(true);
        $query
            ->select(
                "a.id, a.units, a.txn_id, a.txn_amount, a.txn_currency, a.txn_status, a.txn_date, " .
                "a.service_provider, a.currency_id, a.sender_id, a.receiver_id"
            )
            ->from($this->db->quoteName("#__vc_transactions", "a"));

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
     * $data = array(
     *      "units"         => 100,
     *      "txn_id"        => TXN0J09290U2,
     *      "txn_amount"    => "10.0",
     *      "txn_currency"  => "USD",
     *      "txn_status"    => "completed",
     *      "txn_date"      => "2013-08-18 20:46:16",
     *      "currency_id"   => 1,
     *      "sender_id"     => 200,
     *      "receiver_id"   => 300,
     *      "service_provider"      => "PayPal"
     *  );
     *
     * $transaction   = new VirtualCurrencyTransaction(JFactory::getDbo());
     * $transaction->bind($data);
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
     * $data = array(
     *      "units"         => 100,
     *      "txn_id"        => TXN0J09290U2,
     *      "txn_amount"    => "10.0",
     *      "txn_currency"  => "USD",
     *      "txn_status"    => "completed",
     *      "txn_date"      => "2013-08-18 20:46:16",
     *      "currency_id"   => 1,
     *      "sender_id"     => 200,
     *      "receiver_id"   => 300,
     *      "service_provider"      => "PayPal"
     *  );
     *
     * // Create an object and store transaction data.
     * $temporary    = new VirtualCurrencyTransaction();
     * $temporary->bind($data);
     * $temporary->store();
     * </code>
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
        $txnDate = (!$this->txn_date) ? "NULL" : $this->db->quote($this->txn_date);

        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName("#__vc_transactions"))
            ->set($this->db->quoteName("units") . "=" . $this->db->quote($this->units))
            ->set($this->db->quoteName("txn_id") . "=" . $this->db->quote($this->txn_id))
            ->set($this->db->quoteName("txn_amount") . "=" . $this->db->quote($this->txn_amount))
            ->set($this->db->quoteName("txn_currency") . "=" . $this->db->quote($this->txn_currency))
            ->set($this->db->quoteName("txn_status") . "=" . $this->db->quote($this->txn_status))
            ->set($this->db->quoteName("txn_date") . "=" . $txnDate)
            ->set($this->db->quoteName("service_provider") . "=" . $this->db->quote($this->service_provider))
            ->set($this->db->quoteName("currency_id") . "=" . $this->db->quote($this->currency_id))
            ->set($this->db->quoteName("sender_id") . "=" . $this->db->quote($this->sender_id))
            ->set($this->db->quoteName("receiver_id") . "=" . $this->db->quote($this->receiver_id));

        $this->db->setQuery($query);
        $this->db->execute();

        $this->id = $this->db->insertid();
    }

    protected function updateObject()
    {
        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName("#__vc_transactions"))
            ->set($this->db->quoteName("units") . "=" . $this->db->quote($this->units))
            ->set($this->db->quoteName("txn_id") . "=" . $this->db->quote($this->txn_id))
            ->set($this->db->quoteName("txn_amount") . "=" . $this->db->quote($this->txn_amount))
            ->set($this->db->quoteName("txn_currency") . "=" . $this->db->quote($this->txn_currency))
            ->set($this->db->quoteName("txn_status") . "=" . $this->db->quote($this->txn_status))
            ->set($this->db->quoteName("txn_date") . "=" . $this->db->quote($this->txn_date))
            ->set($this->db->quoteName("service_provider") . "=" . $this->db->quote($this->service_provider))
            ->set($this->db->quoteName("currency_id") . "=" . $this->db->quote($this->currency_id))
            ->set($this->db->quoteName("sender_id") . "=" . $this->db->quote($this->sender_id))
            ->set($this->db->quoteName("receiver_id") . "=" . $this->db->quote($this->receiver_id))
            ->where($this->db->quoteName("id") . "=" . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function isCompleted()
    {
        $result = (strcmp("completed", $this->txn_status) == 0);

        return (bool)$result;
    }

    public function isPending()
    {
        $result = (strcmp("pending", $this->txn_status) == 0);

        return (bool)$result;
    }

    /**
     * @return mixed
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * @return mixed
     */
    public function getReceiverId()
    {
        return $this->receiver_id;
    }

    /**
     * @return mixed
     */
    public function getCurrencyId()
    {
        return $this->currency_id;
    }

    /**
     * Returns an associative array of object properties.
     *
     * <code>
     * $txnId = 1;
     *
     * $transaction    = new VirtualCurrencyTransaction(JFactory::getDbo());
     * $transaction->load($txnId);
     *
     * $properties = $transaction->getProperties();
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
