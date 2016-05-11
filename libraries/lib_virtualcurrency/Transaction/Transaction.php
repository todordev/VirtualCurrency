<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Transactions
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Transaction;

use Prism\Database;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing transactions.
 *
 * @package      Virtualcurrency
 * @subpackage   Transactions
 */
class Transaction extends Database\Table
{
    protected $id;
    protected $title;
    protected $units;
    protected $txn_id;
    protected $txn_amount;
    protected $txn_currency;
    protected $txn_status;
    protected $txn_date;
    protected $extra_data;
    protected $service_provider;
    protected $service_alias;
    protected $item_id;
    protected $item_type;
    protected $sender_id;
    protected $receiver_id;

    protected $allowedStatuses = array('pending', 'completed', 'canceled', 'refunded', 'failed');

    /**
     * Load account data from database.
     *
     * <code>
     * $txnId = 1;
     *
     * $transaction   = new Virtualcurrency\Transaction\Transaction(JFactory::getDbo());
     * $transaction->load($txnId);
     * </code>
     *
     * @param array|int $keys
     * @param array $options
     */
    public function load($keys, array $options = array())
    {
        $query = $this->db->getQuery(true);
        $query
            ->select(
                'a.id, a.title, a.units, a.txn_id, a.txn_amount, a.txn_currency, a.txn_status, a.txn_date, ' .
                'a.service_provider, a.service_alias, a.extra_data, a.item_id, a.item_type, a.sender_id, a.receiver_id'
            )
            ->from($this->db->quoteName('#__vc_transactions', 'a'));

        if (!is_array($keys)) {
            $query->where('a.id = ' . (int)$keys);
        } else {
            foreach ($keys as $key => $value) {
                $query->where($this->db->quoteName('a.'.$key) . '=' . $this->db->quote($value));
            }
        }

        $this->db->setQuery($query);
        $result = (array)$this->db->loadAssoc();

        $this->bind($result);
    }

    /**
     * Store the data in database.
     *
     * <code>
     * $data = array(
     *      "title"         => "Gold",
     *      "units"         => 100,
     *      "txn_id"        => TXN0J09290U2,
     *      "txn_amount"    => "10.0",
     *      "txn_currency"  => "USD",
     *      "txn_status"    => "completed",
     *      "txn_date"      => "2013-08-18 20:46:16",
     *      "item_id"       => 1,
     *      "item_type"     => 'currency',
     *      "sender_id"     => 200,
     *      "receiver_id"   => 300,
     *      "service_provider"      => "PayPal"
     *  );
     *
     * // Create an object and store transaction data.
     * $temporary    = new Virtualcurrency\Transaction\Transaction();
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
        // Prepare extra data value.
        $extraData = (!$this->extra_data) ? 'NULL' : $this->db->quote($this->extra_data);
        $txnDate = (!$this->txn_date) ? 'NULL' : $this->db->quote($this->txn_date);

        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName('#__vc_transactions'))
            ->set($this->db->quoteName('title') . '=' . $this->db->quote($this->title))
            ->set($this->db->quoteName('units') . '=' . $this->db->quote($this->units))
            ->set($this->db->quoteName('txn_id') . '=' . $this->db->quote($this->txn_id))
            ->set($this->db->quoteName('txn_amount') . '=' . $this->db->quote($this->txn_amount))
            ->set($this->db->quoteName('txn_currency') . '=' . $this->db->quote($this->txn_currency))
            ->set($this->db->quoteName('txn_status') . '=' . $this->db->quote($this->txn_status))
            ->set($this->db->quoteName('txn_date') . '=' . $txnDate)
            ->set($this->db->quoteName('extra_data') . '=' . $extraData)
            ->set($this->db->quoteName('service_provider') . '=' . $this->db->quote($this->service_provider))
            ->set($this->db->quoteName('service_alias') . '=' . $this->db->quote($this->service_alias))
            ->set($this->db->quoteName('item_id') . '=' . $this->db->quote($this->item_id))
            ->set($this->db->quoteName('item_type') . '=' . $this->db->quote($this->item_type))
            ->set($this->db->quoteName('sender_id') . '=' . $this->db->quote($this->sender_id))
            ->set($this->db->quoteName('receiver_id') . '=' . $this->db->quote($this->receiver_id));

        $this->db->setQuery($query);
        $this->db->execute();

        $this->id = $this->db->insertid();
    }

    protected function updateObject()
    {
        // Prepare extra data value.
        $extraData = (!$this->extra_data) ? 'NULL' : $this->db->quote($this->extra_data);

        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__vc_transactions'))
            ->set($this->db->quoteName('title') . '=' . $this->db->quote($this->title))
            ->set($this->db->quoteName('units') . '=' . $this->db->quote($this->units))
            ->set($this->db->quoteName('txn_id') . '=' . $this->db->quote($this->txn_id))
            ->set($this->db->quoteName('txn_amount') . '=' . $this->db->quote($this->txn_amount))
            ->set($this->db->quoteName('txn_currency') . '=' . $this->db->quote($this->txn_currency))
            ->set($this->db->quoteName('txn_status') . '=' . $this->db->quote($this->txn_status))
            ->set($this->db->quoteName('txn_date') . '=' . $this->db->quote($this->txn_date))
            ->set($this->db->quoteName('extra_data') . '=' . $extraData)
            ->set($this->db->quoteName('service_provider') . '=' . $this->db->quote($this->service_provider))
            ->set($this->db->quoteName('service_alias') . '=' . $this->db->quote($this->service_alias))
            ->set($this->db->quoteName('item_id') . '=' . $this->db->quote($this->item_id))
            ->set($this->db->quoteName('item_type') . '=' . $this->db->quote($this->item_type))
            ->set($this->db->quoteName('sender_id') . '=' . $this->db->quote($this->sender_id))
            ->set($this->db->quoteName('receiver_id') . '=' . $this->db->quote($this->receiver_id))
            ->where($this->db->quoteName('id') . '=' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Set data to object properties.
     *
     * <code>
     * $data = array(
     *  "txn_amount" => "10.00",
     *  "txn_currency" => "GBP"
     * );
     *
     * $transaction    = new Virtualcurrency\Transaction\Transaction(\JFactory::getDbo());
     * $transaction->bind($data);
     * </code>
     *
     * @param array $data
     * @param array $ignored
     */
    public function bind($data, array $ignored = array())
    {
        // Encode extra data to JSON format.
        foreach ($data as $key => $value) {
            if (!in_array($key, $ignored, true)) {
                $this->$key = $value;

                // If it is extra data ( array or object ), encode the data to JSON string.
                if ((strcmp('extra_data', $key) === 0) and (is_array($value) or is_object($value))) {
                    $this->$key = json_encode($value);
                }
            }
        }
    }

    /**
     * Return transaction ID.
     *
     * <code>
     * $transactionId  = 1;
     *
     * $transaction    = new Virtualcurrency\Transaction\Transaction(\JFactory::getDbo());
     * $transaction->load($transactionId);
     *
     * if (!$transaction->getId()) {
     * ....
     * }
     * </code>
     *
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * Check if transaction is completed.
     *
     * <code>
     * $transactionId  = 1;
     *
     * $transaction    = new Virtualcurrency\Transaction\Transaction(\JFactory::getDbo());
     * $transaction->load($transactionId);
     *
     * if (!$transaction->isCompleted()) {
     * ....
     * }
     * </code>
     *
     * @return bool
     */
    public function isCompleted()
    {
        return (bool)(strcmp('completed', $this->txn_status) === 0);
    }

    /**
     * Check if transaction is pending.
     *
     * <code>
     * $transactionId  = 1;
     *
     * $transaction    = new Virtualcurrency\Transaction\Transaction(\JFactory::getDbo());
     * $transaction->load($transactionId);
     *
     * if (!$transaction->isPending()) {
     * ....
     * }
     * </code>
     *
     * @return bool
     */
    public function isPending()
    {
        return (bool)(strcmp('pending', $this->txn_status) === 0);
    }

    /**
     * Return transaction status.
     *
     * <code>
     * $transactionId  = 1;
     *
     * $transaction    = new Virtualcurrency\Transaction\Transaction(\JFactory::getDbo());
     * $transaction->load($transactionId);
     *
     * $status = $transaction->getStatus();
     * </code>
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->txn_status;
    }

    /**
     * Set a transaction status.
     *
     * <code>
     * $transactionId  = 1;
     * $status  = "completed";
     *
     * $transaction    = new Virtualcurrency\Transaction\Transaction(\JFactory::getDbo());
     * $transaction->load($transactionId);
     *
     * $transaction->setStatus($status);
     * </code>
     *
     * @param string $status A transaction status - 'pending', 'completed', 'canceled', 'refunded', 'failed'.
     *
     * @return self
     */
    public function setStatus($status)
    {
        if (in_array($status, $this->allowedStatuses, true)) {
            $this->txn_status = $status;
        }

        return $this;
    }

    /**
     * Return transaction amount.
     *
     * <code>
     * $transactionId  = 1;
     *
     * $transaction    = new Virtualcurrency\Transaction\Transaction(\JFactory::getDbo());
     * $transaction->load($transactionId);
     *
     * $amount = $transaction->getAmount();
     * </code>
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->txn_amount;
    }

    /**
     * Return the units number.
     *
     * <code>
     * $transactionId  = 1;
     *
     * $transaction    = new Virtualcurrency\Transaction\Transaction(\JFactory::getDbo());
     * $transaction->load($transactionId);
     *
     * $units = $transaction->getUnits();
     * </code>
     *
     * @return float
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * Return currency code of transaction.
     *
     * <code>
     * $transactionId  = 1;
     *
     * $transaction    = new Virtualcurrency\Transaction\Transaction(\JFactory::getDbo());
     * $transaction->load($transactionId);
     *
     * $string = $transaction->getCurrency();
     * </code>
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->txn_currency;
    }

    /**
     * Return transaction ID that comes from payment gataway.
     *
     * <code>
     * $transactionId  = 1;
     *
     * $transaction    = new Virtualcurrency\Transaction\Transaction(\JFactory::getDbo());
     * $transaction->load($transactionId);
     *
     * $txnId = $transaction->getTransactionId();
     * </code>
     *
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->txn_id;
    }

    /**
     * Return ID of user who send an amount.
     *
     * <code>
     * $transactionId  = 1;
     *
     * $transaction    = new Virtualcurrency\Transaction\Transaction(\JFactory::getDbo());
     * $transaction->load($transactionId);
     *
     * $investorId = $transaction->getInvestorId();
     * </code>
     *
     * @return int
     */
    public function getSenderId()
    {
        return (int)$this->sender_id;
    }

    /**
     * Return ID of user who receive the amount.
     *
     * <code>
     * $transactionId  = 1;
     *
     * $transaction    = new Virtualcurrency\Transaction\Transaction(\JFactory::getDbo());
     * $transaction->load($transactionId);
     *
     * $receiverId = $transaction->getReceiverId();
     * </code>
     *
     * @return int
     */
    public function getReceiverId()
    {
        return (int)$this->receiver_id;
    }

    /**
     * Return item ID.
     *
     * <code>
     * $transactionId  = 1;
     *
     * $transaction    = new Virtualcurrency\Transaction\Transaction(\JFactory::getDbo());
     * $transaction->load($transactionId);
     *
     * $itemId = $transaction->getItemId();
     * </code>
     *
     * @return int
     */
    public function getItemId()
    {
        return (int)$this->item_id;
    }

    /**
     * Return item type.
     *
     * <code>
     * $transactionId  = 1;
     *
     * $transaction    = new Virtualcurrency\Transaction\Transaction(\JFactory::getDbo());
     * $transaction->load($transactionId);
     *
     * if ('currency' === $transaction->getItemType()) {
     * ...
     * }
     * </code>
     *
     * @return string
     */
    public function getItemType()
    {
        return (string)$this->item_type;
    }
    
    /**
     * Set transaction ID.
     *
     * <code>
     * $transactionId  = 1;
     * $txnId  = "txn_asdf1234";
     *
     * $transaction    = new Virtualcurrency\Transaction\Transaction(\JFactory::getDbo());
     * $transaction->load($transactionId);
     *
     * $transaction->setTransactionId($txnId);
     * $transaction->store();
     * </code>
     *
     * @param string $id
     *
     * @return self
     */
    public function setTransactionId($id)
    {
        $this->txn_id = $id;

        return $this;
    }

    /**
     * Return extra data about transaction that comes from payment gateway.
     *
     * <code>
     * $transactionId  = 1;
     *
     * $transaction    = new Virtualcurrency\Transaction\Transaction(\JFactory::getDbo());
     * $transaction->load($transactionId);
     *
     * $extraData = $transaction->getExtraData();
     * </code>
     *
     * @return array
     */
    public function getExtraData()
    {
        $extraData = array();

        if (is_string($this->extra_data)) {
            $extraData = json_decode($this->extra_data, true);
        }

        if ($extraData === null or !is_array($extraData)) {
            $extraData = array();
        }

        return $extraData;
    }

    /**
     * Include some extra data to existing one.
     *
     * <code>
     * $date = new JDate();
     * $trackingKey = $date->toUnix();
     *
     * $extraData = array(
     *    $trackingKey => array(
     *        "Acknowledgement Status" => "....",
     *        "Timestamp" => "....",
     *        "Correlation ID" => "....",
     *        "NOTE" => "...."
     *     )
     * );
     *
     * $transactionId  = 1;
     *
     * $transaction    = new Virtualcurrency\Transaction\Transaction(\JFactory::getDbo());
     * $transaction->load($transactionId);
     *
     * $extraData = $transaction->addExtraData($extraData);
     * </code>
     *
     * @param array $data
     *
     * @return array
     */
    public function addExtraData($data)
    {
        if (is_array($data)) {
            $extraData = $this->getExtraData();

            foreach ($data as $key => $value) {
                $extraData[$key] = $value;
            }

            $this->extra_data = json_encode($extraData);
        }
    }

    /**
     * Update an extra data record in the database.
     *
     * <code>
     * $date = new JDate();
     * $trackingKey = $date->toUnix();
     *
     * $extraData = array(
     *    $trackingKey => array(
     *        "Acknowledgement Status" => "....",
     *        "Timestamp" => "....",
     *        "Correlation ID" => "....",
     *        "NOTE" => "...."
     *     )
     * );
     *
     * $transactionId  = 1;
     *
     * $transaction    = new Virtualcurrency\Transaction\Transaction(\JFactory::getDbo());
     * $transaction->load($transactionId);
     *
     * $transaction->addExtraData($extraData);
     * $transaction->updateExtraData();
     * </code>
     */
    public function updateExtraData()
    {
        // Prepare extra data value.
        $extraData = (!$this->extra_data) ? 'NULL' : $this->db->quote($this->extra_data);

        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName('#__crowdf_transactions'))
            ->set($this->db->quoteName('extra_data') . ' = ' . $extraData)
            ->where($this->db->quoteName('id') . ' = ' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Update a transaction status in the database record.
     *
     * <code>
     * $transactionId  = 1;
     *
     * $transaction    = new Virtualcurrency\Transaction\Transaction(\JFactory::getDbo());
     * $transaction->load($transactionId);
     *
     * $transaction->setStatus("completed");
     * $transaction->updateStatus();
     * </code>
     */
    public function updateStatus()
    {
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName('#__crowdf_transactions'))
            ->set($this->db->quoteName('txn_status') . ' = ' . $this->db->quote($this->txn_status))
            ->where($this->db->quoteName('id') . ' = ' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }
}
