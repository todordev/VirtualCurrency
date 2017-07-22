<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Transaction
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Transaction;

use Prism\Domain\Entity;
use Prism\Domain\EntityId;
use Prism\Domain\EntityProperties;
use Prism\Domain\PropertiesMethods;

/**
 * This class contains methods that are used for managing transactions.
 *
 * @package      Virtualcurrency
 * @subpackage   Transaction
 */
class Transaction implements Entity, EntityProperties
{
    use EntityId, PropertiesMethods;

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
    protected $error_msg;

    protected $allowedStatuses = array('pending', 'completed', 'canceled', 'refunded', 'failed');

    /**
     * Set data to object properties.
     *
     * @param array $data
     * @param array $ignored
     */
    public function bind(array $data, array $ignored = array())
    {
        $properties = get_object_vars($this);

        // If there is a key extra_data, encode the data to JSON string.
        if (array_key_exists('extra_data', $data) and (is_array($data['extra_data']) or is_object($data['extra_data']))) {
            $this->extra_data = json_encode($data['extra_data'], true);
            unset($data['extra_data']);
        }

        // Encode extra data to JSON format.
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $properties) and !in_array($key, $ignored, true)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Check if transaction is completed.
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
     * @return bool
     */
    public function isPending()
    {
        return (bool)(strcmp('pending', $this->txn_status) === 0);
    }

    /**
     * Return unit title.
     *
     * @return string
     */
    public function getTitle()
    {
        return (string)$this->title;
    }

    /**
     * Return service provider.
     *
     * @return string
     */
    public function getServiceProvider()
    {
        return (string)$this->service_provider;
    }

    /**
     * Return service alias.
     *
     * @return string
     */
    public function getServiceAlias()
    {
        return (string)$this->service_alias;
    }

    /**
     * Return transaction date.
     *
     * @return string
     */
    public function getTransactionDate()
    {
        return (string)$this->txn_date;
    }

    /**
     * Return transaction status.
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
     * @return float
     */
    public function getAmount()
    {
        return (float)$this->txn_amount;
    }

    /**
     * Return the units number.
     *
     * @return float
     */
    public function getUnits()
    {
        return (float)$this->units;
    }

    /**
     * Return currency code of transaction.
     *
     * @return string
     */
    public function getTransaction()
    {
        return (string)$this->txn_currency;
    }

    /**
     * Return transaction ID that comes from payment gateway.
     *
     * @return string
     */
    public function getTransactionId()
    {
        return (string)$this->txn_id;
    }

    /**
     * Return ID of user who send an amount.
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
     * @return int
     */
    public function getReceiverId()
    {
        return (int)$this->receiver_id;
    }

    /**
     * Return item ID.
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
     * @return string
     */
    public function getItemType()
    {
        return (string)$this->item_type;
    }
    
    /**
     * Set transaction ID.
     *
     * @param string $id
     *
     * @return self
     */
    public function setTransactionId($id)
    {
        $this->txn_id = (string)$id;

        return $this;
    }

    /**
     * Return stored error message.
     *
     * @return string $message
     */
    public function getErrorMessage()
    {
        return (string)$this->error_msg;
    }

    /**
     * Set error message.
     *
     * @param string $message
     *
     * @return self
     */
    public function setErrorMessage($message)
    {
        $this->error_msg = (string)$message;

        return $this;
    }

    /**
     * Return extra data about transaction that comes from payment gateway.
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
     * @param array $data
     *
     * @return self
     */
    public function addExtraData(array $data)
    {
        $extraData = $this->getExtraData();

        foreach ($data as $key => $value) {
            $extraData[$key] = $value;
        }

        $this->extra_data = json_encode($extraData);

        return $this;
    }
}
