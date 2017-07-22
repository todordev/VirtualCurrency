<?php
/**
 * @package      Virtualcurrency\Payment
 * @subpackage   Session
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Payment\Session;

use Prism\Domain\Entity;
use Prism\Domain\EntityId;
use Joomla\Registry\Registry;

/**
 * This class provides functionality that manage data for specific payment gateway.
 *
 * @package      Virtualcurrency\Payment
 * @subpackage   Session
 */
class Service implements Entity
{
    use EntityId;

    protected $alias;
    protected $data;
    protected $order_id;
    protected $token;

    /**
     * Initialize the object.
     */
    public function __construct()
    {
        $this->data = new Registry;
    }

    /**
     * Return gateway name (alias).
     *
     * <code>
     * $paymentSessionId = 1;
     *
     * $gateway        = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $repository     = new Virtualcurrency\Payment\Session\Repository;
     * $repository->setGateway($gateway);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
     *
     * echo $paymentSession->service('paypal)->getAlias();
     * </code>
     *
     * @return string
     */
    public function getAlias()
    {
        return (string)$this->alias;
    }

    /**
     * Set gateway name (alias).
     *
     * <code>
     * $paymentSessionId = 1;
     *
     * $gateway        = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $repository     = new Virtualcurrency\Payment\Session\Repository;
     * $repository->setGateway($gateway);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
     *
     * $paymentSession->service('paypal)->setAlias('paypal');
     * </code>
     *
     * @param string $alias
     *
     * @return self
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Return order ID.
     *
     * <code>
     * $paymentSessionId = 1;
     *
     * $gateway        = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $repository     = new Virtualcurrency\Payment\Session\Repository;
     * $repository->setGateway($gateway);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
     * $orderId        = $paymentSession->service('paypal)->getOrderId('12345');
     * </code>
     *
     * @return string
     */
    public function getOrderId()
    {
        return (string)$this->order_id;
    }

    /**
     * Set order ID to payment gateway data.
     *
     * <code>
     * $paymentSessionId = 1;
     *
     * $gateway        = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $repository     = new Virtualcurrency\Payment\Session\Repository;
     * $repository->setGateway($gateway);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
     *
     * $paymentSession->service('paypal')->setOrderId('OID_1234');
     * </code>
     *
     * @param mixed $orderId
     *
     * @throws \InvalidArgumentException
     */
    public function setOrderId($orderId)
    {
        if (!$orderId) {
            throw new \InvalidArgumentException('Invalid order ID.');
        }

        $this->order_id = $orderId;
    }

    /**
     * Return unique key assigned to payment gateway.
     *
     * <code>
     * $paymentSessionId = 1;
     *
     * $gateway        = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $repository     = new Virtualcurrency\Payment\Session\Repository;
     * $repository->setGateway($gateway);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
     *
     * $uniqueKey      = $paymentSession->service('paypal')->getToken();
     * </code>
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getToken()
    {
        return (string)$this->token;
    }

    /**
     * Assign unique key to the payment gateway.
     *
     * <code>
     * $paymentSessionId = 1;
     *
     * $gateway        = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $repository     = new Virtualcurrency\Payment\Session\Repository;
     * $repository->setGateway($gateway);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
     *
     * $paymentSession->service('paypal')->setToken('T123456');
     * </code>
     *
     * @param string $token
     *
     * @throws \InvalidArgumentException
     */
    public function setToken($token)
    {
        if (!$token) {
            throw new \InvalidArgumentException('Invalid unique key (token).');
        }

        $this->token = $token;
    }

    /**
     * Return an array with additional data assigned to payment gateway.
     *
     * <code>
     * $paymentSessionId = 1;
     *
     * $gateway        = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $repository     = new Virtualcurrency\Payment\Session\Repository;
     * $repository->setGateway($gateway);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
     *
     * $serviceData    = $paymentSession->service('paypal')->getData(');
     * </code>
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getData()
    {
        return (array)$this->data->toArray();
    }

    /**
     * Set service data.
     *
     * <code>
     * $paymentSessionId = 1;
     *
     * $gateway        = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(JFactory::getDbo());
     * $repository     = new Virtualcurrency\Payment\Session\Repository;
     * $repository->setGateway($gateway);
     *
     * $paymentSession = $repository->fetchById($paymentSessionId);
     *
     * $serviceData = array(
     *     'paypal' => array(),
     *     'stripe' => array()
     * );
     *
     * $paymentSession->service('paypal')->setData($serviceData);
     * </code>
     *
     * @param array $data
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function setData(array $data)
    {
        if (!$data) {
            throw new \InvalidArgumentException('Invalid gateway data.');
        }

        $this->data->loadArray($data);

        return $this;
    }

    /**
     * Set additional data to the service data.
     *
     * <code>
     * $serviceData = new Virtualcurrency\Payment\Session\ServiceData;
     *
     * $serviceData->data('token', 'TOK123456');
     * // or
     * echo $serviceData->data('token');
     * </code>
     *
     * @param string $key
     * @param string $value
     *
     * @return mixed
     */
    public function data($key, $value = null)
    {
        if ($value === null) {
            return $this->data->get($key);
        }

        return $this->data->set($key, $value);
    }

    /**
     * Populate persistence data to the object properties.
     *
     * <code>
     * $data = array(
     *     //...
     * );
     *
     * $serviceData   = new Virtualcurrency\Payment\Session\ServiceData;
     * $serviceData->bind($data);
     * </code>
     *
     * @param array $data
     * @param array $ignored
     */
    public function bind(array $data, array $ignored = array())
    {
        $properties = get_object_vars($this);

        // Parse parameters of the object if they exists.
        if (array_key_exists('data', $data) and array_key_exists('data', $properties) and !in_array('data', $ignored, true)) {
            if (is_array($data['data'])) {
                $this->data->loadArray($data['data']);
            }
            unset($data['data']);
        }

        foreach ($data as $key => $value) {
            if (array_key_exists($key, $properties) and !in_array($key, $ignored, true)) {
                $this->$key = $value;
            }
        }
    }
}
