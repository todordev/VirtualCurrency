<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Payments
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Payment;

use Prism\Database;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing payment session.
 * In the temporary table are saved data,
 * which will be used during the process of making transactions.
 *
 * @package      Virtualcurrency
 * @subpackage   Payments
 */
class Session extends Database\Table
{
    protected $id;
    protected $user_id;
    protected $item_id;
    protected $item_type;
    protected $items_number;
    protected $unique_key;
    protected $gateway;
    protected $gateway_data;
    protected $session_id;

    protected $record_date;

    /**
     * Load account data from database.
     *
     * <code>
     * $id = 1;
     *
     * $paymentSession   = new Virtualcurrency\Payment\Session(JFactory::getDbo());
     * $paymentSession->load($id);
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
                'a.id, a.user_id, a.item_id, a.items_number, a.item_type, a.unique_key, ' .
                'a.gateway, a.gateway_data, a.session_id, a.record_date'
            )
            ->from($this->db->quoteName('#__vc_paymentsessions', 'a'));

        if (!is_array($keys)) {
            $query->where('a.id = ' . (int)$keys);
        } else {
            foreach ($keys as $key => $value) {
                $query->where($this->db->quoteName('a.'.$key) . '=' . $this->db->quote($value));
            }
        }

        $this->db->setQuery($query);
        $result = (array)$this->db->loadAssoc();

        // Decode gateway data.
        $this->gateway_data = (!empty($result['gateway_data'])) ? (array)json_decode($result['gateway_data'], true) : array();

        $this->bind($result, array('gateway_data'));
    }

    /**
     * Store the data in database.
     *
     * <code>
     * $data = (
     *  "user_id"    => 1,
     *  "currency_id"  => 2,
     *  "amount"  => 10,
     * );
     *
     * $paymentSession   = new Virtualcurrency\Payment\Session(JFactory::getDbo());
     * $paymentSession->bind($data);
     * $paymentSession->store();
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
        $recordDate   = (!$this->record_date) ? 'NULL' : $this->db->quote($this->record_date);

        // Encode the gateway data to JSON format.
        $gatewayData = $this->encodeDataToJson();

        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName('#__vc_paymentsessions'))
            ->set($this->db->quoteName('user_id') . '=' . $this->db->quote($this->user_id))
            ->set($this->db->quoteName('item_id') . '=' . $this->db->quote($this->item_id))
            ->set($this->db->quoteName('item_type') . '=' . $this->db->quote($this->item_type))
            ->set($this->db->quoteName('items_number') . '=' . $this->db->quote($this->items_number))
            ->set($this->db->quoteName('record_date') . '=' . $recordDate)
            ->set($this->db->quoteName('unique_key') . '=' . $this->db->quote($this->unique_key))
            ->set($this->db->quoteName('gateway') . '=' . $this->db->quote($this->gateway))
            ->set($this->db->quoteName('gateway_data') . '=' . $this->db->quote($gatewayData))
            ->set($this->db->quoteName('session_id') . '=' . $this->db->quote($this->session_id));

        $this->db->setQuery($query);
        $this->db->execute();

        $this->id = $this->db->insertid();
    }

    protected function updateObject()
    {
        // Encode the gateway data to JSON format.
        $gatewayData = $this->encodeDataToJson();

        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__vc_paymentsessions'))
            ->set($this->db->quoteName('user_id') . '=' . $this->db->quote($this->user_id))
            ->set($this->db->quoteName('item_id') . '=' . $this->db->quote($this->item_id))
            ->set($this->db->quoteName('item_type') . '=' . $this->db->quote($this->item_type))
            ->set($this->db->quoteName('items_number') . '=' . $this->db->quote($this->items_number))
            ->set($this->db->quoteName('record_date') . '=' . $this->db->quote($this->record_date))
            ->set($this->db->quoteName('unique_key') . '=' . $this->db->quote($this->unique_key))
            ->set($this->db->quoteName('gateway') . '=' . $this->db->quote($this->gateway))
            ->set($this->db->quoteName('gateway_data') . '=' . $this->db->quote($gatewayData))
            ->set($this->db->quoteName('session_id') . '=' . $this->db->quote($this->session_id))
            ->where($this->db->quoteName('id') . '=' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Remove old records.
     *
     * <code>
     * $paymentSession   = new Virtualcurrency\Payment\Session(JFactory::getDbo());
     * $paymentSession->cleanOld();
     * </code>
     */
    public function cleanOld()
    {
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName('#__vc_paymentsessions'))
            ->where($this->db->quoteName('record_date') .' < ( NOW() - INTERVAL 2 DAY )');

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Remove payment session.
     *
     * <code>
     * $sessionId = 1;
     *
     * $paymentSession   = Virtualcurrency\Payment\Session(JFactory::getDbo());
     * $paymentSession->setId($sessionId);
     *
     * $paymentSession->delete();
     * </code>
     */
    public function delete()
    {
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName('#__vc_paymentsessions'))
            ->where($this->db->quoteName('id') .' = ' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();

        $this->reset();
    }

    /**
     * Set ID session.
     *
     * <code>
     * $sessionId = 1;
     *
     * $paymentSession   = new Virtualcurrency\Payment\Session();
     * $paymentSession->setId($sessionId);
     * </code>
     *
     * @param int $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = (int)$id;

        return $this;
    }

    /**
     * Return the ID of the payment session.
     *
     * <code>
     * $paymentSession   = new Virtualcurrency\Payment\Session();
     * $paymentSession->getId();
     * </code>
     *
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * Set number of virtual goods or currency.
     *
     * <code>
     * $paymentSessionId = 1;
     * 
     * $paymentSession   = new Virtualcurrency\Payment\Session(\JFactory::getDbo());
     * $paymentSession->load($paymentSessionId);
     *
     * $paymentSession->setItemsNumber("100.00");
     * </code>
     *
     * @param float $number
     *
     * @return self
     */
    public function setItemsNumber($number)
    {
        $this->items_number = $number;

        return $this;
    }
    
    /**
     * Return the number of units, that will be bought.
     *
     * <code>
     * $id = 1;
     *
     * $paymentSession   = Virtualcurrency\Payment\Session(JFactory::getDbo());
     * $paymentSession->load($id);
     *
     * echo $paymentSession->getItemsNumber();
     * </code>
     *
     * @return float
     */
    public function getItemsNumber()
    {
        return $this->items_number;
    }

    /**
     * Set user ID to the object.
     *
     * <code>
     * $paymentSessionId = 1;
     * $userId = 2;
     *
     * $paymentSession   = new Virtualcurrency\Payment\Session(\JFactory::getDbo());
     * $paymentSession->load($paymentSessionId);
     *
     * $paymentSession->setUserId($userId);
     * </code>
     *
     * @param int $userId
     *
     * @return self
     */
    public function setUserId($userId)
    {
        $this->user_id = (int)$userId;

        return $this;
    }

    /**
     * Return the ID of the user that is going to buy virtual currency.
     *
     * <code>
     * $id = 1;
     *
     * $paymentSession   = Virtualcurrency\Payment\Session(JFactory::getDbo());
     * $paymentSession->load($id);
     *
     * $userId = $paymentSession->getUserId();
     * </code>
     *
     * @return int
     */
    public function getUserId()
    {
        return (int)$this->user_id;
    }

    /**
     * Set item ID.
     *
     * <code>
     * $paymentSessionId = 1;
     * $itemId = 2;
     *
     * $paymentSession   = new Virtualcurrency\Payment\Session(\JFactory::getDbo());
     * $paymentSession->load($paymentSessionId);
     *
     * $paymentSession->setItemId($itemId);
     * </code>
     *
     * @param int $itemId
     *
     * @return self
     */
    public function setItemId($itemId)
    {
        $this->item_id = (int)$itemId;

        return $this;
    }

    /**
     * Return the ID of the item that is going to ge bought by a user.
     *
     * <code>
     * $id = 1;
     *
     * $paymentSession   = Virtualcurrency\Payment\Session(JFactory::getDbo());
     * $paymentSession->load($id);
     *
     * $itemId = $paymentSession->getItemId();
     * </code>
     *
     * @return int
     */
    public function getItemId()
    {
        return (int)$this->item_id;
    }

    protected function encodeDataToJson()
    {
        if ($this->gateway_data === null or !is_array($this->gateway_data)) {
            $this->gateway_data = array();
        }
        return json_encode($this->gateway_data);
    }

    /**
     * Return gateway data.
     *
     * <code>
     * $paymentSessionId  = 1;
     *
     * $paymentSession    = new Virtualcurrency\Payment\Session(\JFactory::getDbo());
     * $paymentSession->load($paymentSessionId);
     *
     * $gatewayData = $paymentSession->getGatewayData();
     * </code>
     *
     * @return string
     */
    public function getGatewayData()
    {
        return $this->gateway_data;
    }

    /**
     * Set a gateway data.
     *
     * <code>
     * $paymentSessionId  = 1;
     * $data        = array(
     *    "token" => "TOKEN1234"
     * );
     *
     * $paymentSession    = new Virtualcurrency\Payment\Session(\JFactory::getDbo());
     * $paymentSession->load($paymentSessionId);
     *
     * $paymentSession->setGatewayData($data);
     * </code>
     *
     * @param array $data
     *
     * @return self
     */
    public function setGatewayData(array $data)
    {
        $this->gateway_data = $data;

        return $this;
    }

    /**
     * Return a value of a gateway data.
     *
     * <code>
     * $paymentSessionId  = 1;
     *
     * $paymentSession    = new Virtualcurrency\Payment\Session(\JFactory::getDbo());
     * $paymentSession->load($paymentSessionId);
     *
     * $gateway = $paymentSession->getData("token");
     * </code>
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getData($key, $default = null)
    {
        return (!array_key_exists($key, $this->gateway_data)) ? $default : $this->gateway_data[$key];
    }

    /**
     * Set a gateway data value.
     *
     * <code>
     * $paymentSessionId  = 1;
     * $token        = "TOKEN1234";
     *
     * $paymentSession    = new Virtualcurrency\Payment\Session(\JFactory::getDbo());
     * $paymentSession->load($paymentSessionId);
     *
     * $paymentSession->setData("token", $token);
     * </code>
     *
     * @param string $key
     * @param mixed $value
     *
     * @return self
     */
    public function setData($key, $value)
    {
        $this->gateway_data[$key] = $value;

        return $this;
    }

    /**
     * Return a unique key that comes from a payment gateway.
     * That can be transaction ID, token,...
     *
     * <code>
     * $paymentSessionId  = 1;
     *
     * $paymentSession    = new Virtualcurrency\Payment\Session(\JFactory::getDbo());
     * $paymentSession->load($paymentSessionId);
     *
     * $uniqueKey = $intention->getUniqueKey();
     * </code>
     *
     * @return string
     */
    public function getUniqueKey()
    {
        return $this->unique_key;
    }

    /**
     * Set unique key that comes from a payment gateway.
     * That can be transaction ID, token,...
     *
     * <code>
     * $paymentSessionId  = 1;
     * $token        = "TOKEN1234";
     *
     * $paymentSession    = new Virtualcurrency\Payment\Session(\JFactory::getDbo());
     * $paymentSession->load($paymentSessionId);
     *
     * $paymentSession->setUniqueKey($token);
     * </code>
     *
     * @param string $key
     * @return self
     */
    public function setUniqueKey($key)
    {
        $this->unique_key = $key;

        return $this;
    }

    /**
     * Set unique key that comes from a payment gateway.
     * That can be transaction ID, token,...
     *
     * <code>
     * $paymentSessionId  = 1;
     * $token        = "TOKEN1234";
     *
     * $paymentSession    = new Virtualcurrency\Payment\Session(\JFactory::getDbo());
     * $paymentSession->load($paymentSessionId);
     *
     * $paymentSession->setUniqueKey($token);
     * $paymentSession->storeUniqueKey();
     * </code>
     *
     * @return self
     */
    public function storeUniqueKey()
    {
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName('#__vc_paymentsessions'))
            ->set($this->db->quoteName('unique_key') . '=' . $this->db->quote($this->unique_key))
            ->where($this->db->quoteName('id') . '=' . $this->db->quote($this->id));

        $this->db->setQuery($query);
        $this->db->execute();

        return $this;
    }

    /**
     * Set the name of the payment gateway.
     *
     * <code>
     * $paymentSessionId = 1;
     * $name = "PayPal";
     *
     * $paymentSession   = new Virtualcurrency\Payment\Session(\JFactory::getDbo());
     * $paymentSession->load($paymentSessionId);
     *
     * $paymentSession->setGateway($name);
     * </code>
     *
     * @param string $gateway
     *
     * @return self
     */
    public function setGateway($gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    /**
     * Return the name of payment service.
     *
     * <code>
     * $paymentSessionId = 1;
     *
     * $paymentSession   = new Virtualcurrency\Payment\Session(\JFactory::getDbo());
     * $paymentSession->load($paymentSessionId);
     *
     * $name = $paymentSession->getGateway();
     * </code>
     *
     * @return string
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * Return session ID.
     *
     * <code>
     * $paymentSessionId = 1;
     *
     * $paymentSession    = new Virtualcurrency\Payment\Session(\JFactory::getDbo());
     * $paymentSession->load($intentionId);
     *
     * $sessionId = $paymentSession->getSessionId();
     * </code>
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->session_id;
    }

    /**
     * Set session ID.
     *
     * <code>
     * $paymentSessionId = 1;
     * $sessionId        = "SESSION_ID_1234";
     *
     * $paymentSession    = new Virtualcurrency\Payment\Session(\JFactory::getDbo());
     * $paymentSession->load($paymentSessionId);
     *
     * $paymentSession->setSessionId($sessionId);
     * </code>
     *
     * @param string $sessionId
     * @return self
     */
    public function setSessionId($sessionId)
    {
        $this->session_id = $sessionId;

        return $this;
    }

    /**
     * Return the type of the item.
     *
     * <code>
     * $paymentSessionId = 1;
     *
     * $paymentSession    = new Virtualcurrency\Payment\Session(\JFactory::getDbo());
     * $paymentSession->load($intentionId);
     *
     * echo $paymentSession->getItemType();
     * </code>
     *
     * @return string
     */
    public function getItemType()
    {
        return $this->item_type;
    }

    /**
     * Set the type of the item.
     *
     * <code>
     * $paymentSessionId = 1;
     *
     * $paymentSession    = new Virtualcurrency\Payment\Session(\JFactory::getDbo());
     * $paymentSession->load($paymentSessionId);
     *
     * $paymentSession->setItemType('currency');
     * </code>
     *
     * @param string $type
     * @return self
     */
    public function setItemType($type)
    {
        $this->item_type = in_array($type, array('currency', 'commodity'), true) ? $type : null;

        return $this;
    }
    
    /**
     * Check if the item is virtual currency.
     *
     * <code>
     * $paymentSessionId = 1;
     *
     * $paymentSession   = new Virtualcurrency\Payment\Session(\JFactory::getDbo());
     * $paymentSession->load($paymentSessionId);
     *
     * if (!$paymentSession->isCurrency()) {
     * ...
     * }
     * </code>
     *
     * @return bool
     */
    public function isCurrency()
    {
        return (bool)(strcmp('currency', $this->item_type) === 0);
    }

    /**
     * Check if the item is virtual commodity.
     *
     * <code>
     * $paymentSessionId = 1;
     *
     * $paymentSession   = new Virtualcurrency\Payment\Session(\JFactory::getDbo());
     * $paymentSession->load($paymentSessionId);
     *
     * if (!$paymentSession->isCommodity()) {
     * ...
     * }
     * </code>
     *
     * @return bool
     */
    public function isCommodity()
    {
        return (bool)(strcmp('commodity', $this->item_type) === 0);
    }
}
