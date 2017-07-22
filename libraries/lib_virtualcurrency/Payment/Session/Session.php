<?php
/**
 * @package      Virtualcurrency\Payment
 * @subpackage   Session
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Payment\Session;

use Prism\Domain\Reset;
use Prism\Domain\Entity;
use Prism\Domain\EntityId;
use Prism\Domain\Resetting;
use Prism\Domain\EntityProperties;
use Prism\Domain\PropertiesMethods;

/**
 * This class provides functionality that manage payment session.
 * The session is used for storing data in the process of requests between application and payment services.
 *
 * @package      Virtualcurrency\Payment
 * @subpackage   Session
 */
class Session implements Entity, Resetting, EntityProperties
{
    use EntityId, Reset, PropertiesMethods;

    protected $user_id;
    protected $item_id;
    protected $item_type;
    protected $items_number;
    protected $gateway;
    protected $session_id;
    protected $record_date;

    protected $services;

    /**
     * Session constructor.
     *
     * <code>
     * $paymentSessionId = 1;
     *
     * $gateway       = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $mapper        = new Virtualcurrency\Payment\Session\Mapper($gateway);
     * $repository    = new Virtualcurrency\Payment\Session\Repository($mapper);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
     * </code>
     */
    public function __construct()
    {
        $this->services = new Services();
    }

    /**
     * Set user ID to the object.
     *
     * <code>
     * $paymentSessionId = 1;
     * $userId = 2;
     *
     * $gateway       = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $mapper        = new Virtualcurrency\Payment\Session\Mapper($gateway);
     * $repository    = new Virtualcurrency\Payment\Session\Repository($mapper);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
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
     * Return user ID which is part of current payment session.
     *
     * <code>
     * $paymentSessionId = 1;
     *
     * $gateway       = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $mapper        = new Virtualcurrency\Payment\Session\Mapper($gateway);
     * $repository    = new Virtualcurrency\Payment\Session\Repository($mapper);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
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
     * Set number of virtual goods or currency.
     *
     * <code>
     * $paymentSessionId = 1;
     *
     * $gateway       = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $mapper        = new Virtualcurrency\Payment\Session\Mapper($gateway);
     * $repository    = new Virtualcurrency\Payment\Session\Repository($mapper);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
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
     * $gateway       = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $mapper        = new Virtualcurrency\Payment\Session\Mapper($gateway);
     * $repository    = new Virtualcurrency\Payment\Session\Repository($mapper);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
     * echo $paymentSession->getItemsNumber();
     * </code>
     *
     * @return float
     */
    public function getItemsNumber()
    {
        return (float)$this->items_number;
    }

    /**
     * Set item ID.
     *
     * <code>
     * $paymentSessionId = 1;
     * $itemId = 2;
     *
     * $gateway       = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $mapper        = new Virtualcurrency\Payment\Session\Mapper($gateway);
     * $repository    = new Virtualcurrency\Payment\Session\Repository($mapper);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
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
     * $gateway       = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $mapper        = new Virtualcurrency\Payment\Session\Mapper($gateway);
     * $repository    = new Virtualcurrency\Payment\Session\Repository($mapper);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
     * $itemId = $paymentSession->getItemId();
     * </code>
     *
     * @return int
     */
    public function getItemId()
    {
        return (int)$this->item_id;
    }

    /**
     * Set the name of the payment gateway.
     *
     * <code>
     * $paymentSessionId = 1;
     * $name = "PayPal";
     *
     * $gateway       = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $mapper        = new Virtualcurrency\Payment\Session\Mapper($gateway);
     * $repository    = new Virtualcurrency\Payment\Session\Repository($mapper);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
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
     * $gateway       = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $mapper        = new Virtualcurrency\Payment\Session\Mapper($gateway);
     * $repository    = new Virtualcurrency\Payment\Session\Repository($mapper);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
     * $name = $paymentSession->getGateway();
     * </code>
     *
     * @return string
     */
    public function getGateway()
    {
        return (string)$this->gateway;
    }

    /**
     * Set the date of the database record.
     *
     * <code>
     * $paymentSessionId = 1;
     * $date = "01-01-2014";
     *
     * $gateway       = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $mapper        = new Virtualcurrency\Payment\Session\Mapper($gateway);
     * $repository    = new Virtualcurrency\Payment\Session\Repository($mapper);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
     *
     * $paymentSession->setRecordDateId($date);
     * </code>
     *
     * @param string $recordDate
     *
     * @return self
     */
    public function setRecordDate($recordDate)
    {
        $this->record_date = $recordDate;

        return $this;
    }

    /**
     * Return the date of current record.
     *
     * <code>
     * $paymentSessionId = 1;
     *
     * $gateway       = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $mapper        = new Virtualcurrency\Payment\Session\Mapper($gateway);
     * $repository    = new Virtualcurrency\Payment\Session\Repository($mapper);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
     *
     * $date = $paymentSession->getRecordDate();
     * </code>
     *
     * @return string
     */
    public function getRecordDate()
    {
        return (string)$this->record_date;
    }

    /**
     * Return session ID.
     *
     * <code>
     * $paymentSessionId = 1;
     *
     * $gateway       = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $mapper        = new Virtualcurrency\Payment\Session\Mapper($gateway);
     * $repository    = new Virtualcurrency\Payment\Session\Repository($mapper);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
     *
     * $sessionId = $paymentSession->getSessionId();
     * </code>
     *
     * @return string
     */
    public function getSessionId()
    {
        return (string)$this->session_id;
    }

    /**
     * Set session ID.
     *
     * <code>
     * $paymentSessionId = 1;
     * $sessionId        = "SESSION_ID_1234";
     *
     * $gateway       = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $mapper        = new Virtualcurrency\Payment\Session\Mapper($gateway);
     * $repository    = new Virtualcurrency\Payment\Session\Repository($mapper);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
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
     * $gateway       = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $mapper        = new Virtualcurrency\Payment\Session\Mapper($gateway);
     * $repository    = new Virtualcurrency\Payment\Session\Repository($mapper);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
     * echo $paymentSession->getItemType();
     * </code>
     *
     * @return string
     */
    public function getItemType()
    {
        return (string)$this->item_type;
    }

    /**
     * Set the type of the item.
     *
     * <code>
     * $paymentSessionId = 1;
     *
     * $gateway       = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $mapper        = new Virtualcurrency\Payment\Session\Mapper($gateway);
     * $repository    = new Virtualcurrency\Payment\Session\Repository($mapper);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
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
     * $gateway       = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $mapper        = new Virtualcurrency\Payment\Session\Mapper($gateway);
     * $repository    = new Virtualcurrency\Payment\Session\Repository($mapper);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
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
     * $gateway       = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $mapper        = new Virtualcurrency\Payment\Session\Mapper($gateway);
     * $repository    = new Virtualcurrency\Payment\Session\Repository($mapper);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
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

    /**
     * Set notification data to object parameters.
     *
     * <code>
     * $data = array(
     *     //...
     * );
     *
     * $paymentSession   = new Virtualcurrency\Payment\Session\Session();
     * $paymentSession->bind($data);
     * </code>
     *
     * @param array $data
     * @param array $ignored
     */
    public function bind(array $data, array $ignored = array())
    {
        $properties = get_object_vars($this);

        // Parse parameters of the object if they exists.
        if (array_key_exists('services', $data) and !in_array('services', $ignored, true) and ($data['services'] instanceof Services)) {
            $this->services = $data['services'];
            unset($data['services']);
        }

        foreach ($data as $key => $value) {
            if (array_key_exists($key, $properties) and !in_array($key, $ignored, true)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Return the services as Registry object.
     *
     * <code>
     * $paymentSessionId = 1;
     *
     * $gateway       = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $mapper        = new Virtualcurrency\Payment\Session\Mapper($gateway);
     * $repository    = new Virtualcurrency\Payment\Session\Repository($mapper);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
     *
     * $services       = $paymentSession->getServices();
     * </code>
     *
     * @return Services
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Return an object that keeps the service data.
     *
     * <code>
     * $paymentSessionId = 1;
     *
     * $gateway       = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $mapper        = new Virtualcurrency\Payment\Session\Mapper($gateway);
     * $repository    = new Virtualcurrency\Payment\Session\Repository($mapper);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
     *
     * $serviceData = $paymentSession->service('paypal');
     * </code>
     *
     * @param string $gateway
     *
     * @return Service
     * @throws \InvalidArgumentException
     */
    public function service($gateway)
    {
        if (!$gateway) {
            throw new \InvalidArgumentException('Invalid gateway name (alias).');
        }

        if (!$this->services->keyExists($gateway)) {
            $this->services[$gateway] = new Service;
            $this->services[$gateway]->setAlias($gateway);
        }

        return $this->services[$gateway];
    }
}
