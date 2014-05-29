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
 * This class contains methods that are used for managing payment session.
 * In the temporary table are saved data,
 * which will be used during the process of making transactions.
 *
 * @package      VirtualCurrency
 * @subpackage   Library
 */
class VirtualCurrencyPaymentSession
{
    protected $id;
    protected $user_id;
    protected $currency_id;
    protected $amount;

    protected $record_date;

    /**
     * @var JDatabaseDriver
     */
    protected $db;

    /**
     * Initialize the object.
     *
     * <code>
     * $id = 1;
     *
     * $paymentSession   = new VirtualCurrencyPaymentSession(JFactory::getDbo());
     * $paymentSession->load($id);
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
     * $paymentSession    = new VirtualCurrencyPaymentSession();
     * $paymentSession->setDb(JFactory::getDbo());
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
     * $id = 1;
     *
     * $paymentSession   = new VirtualCurrencyPaymentSession(JFactory::getDbo());
     * $paymentSession->load($id);
     * </code>
     *
     * @param int $id
     */
    public function load($id)
    {
        $query = $this->db->getQuery(true);
        $query
            ->select("a.id, a.user_id, a.currency_id, a.amount, a.record_date")
            ->from($this->db->quoteName("#__vc_paymentsessions", "a"))
            ->where("a.id = " . (int)$id);

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
     *  "user_id"    => 1,
     *  "currency_id"  => 2,
     *  "amount"  => 10,
     * );
     *
     * $paymentSession   = new VirtualCurrencyPaymentSession(JFactory::getDbo());
     * $paymentSession->bind($data);
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
     *  "user_id"    => 1,
     *  "currency_id"  => 2,
     *  "amount"  => 10,
     * );
     *
     * $paymentSession   = new VirtualCurrencyPaymentSession(JFactory::getDbo());
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
        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName("#__vc_paymentsessions"))
            ->set($this->db->quoteName("user_id") . "=" . (int)$this->user_id)
            ->set($this->db->quoteName("currency_id") . "=" . (int)$this->currency_id)
            ->set($this->db->quoteName("amount") . "=" . $this->db->quote($this->amount));

        $this->db->setQuery($query);
        $this->db->execute();

        $this->id = $this->db->insertid();
    }

    protected function updateObject()
    {
        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName("#__vc_paymentsessions"))
            ->set($this->db->quoteName("user_id") . "=" . (int)$this->user_id)
            ->set($this->db->quoteName("currency_id") . "=" . (int)$this->currency_id)
            ->set($this->db->quoteName("amount") . "=" . $this->db->quote($this->amount))
            ->where($this->db->quoteName("id") . "=" . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Remove old records.
     *
     * <code>
     * $paymentSession   = new VirtualCurrencyPaymentSession(JFactory::getDbo());
     * $paymentSession->cleanOld();
     * </code>
     */
    public function cleanOld()
    {
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName("#__vc_paymentsessions"))
            ->where($this->db->quoteName("record_date") ." < ( NOW() - INTERVAL 2 DAY )");

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Remove payment session.
     *
     * <code>
     * $id = 1;
     *
     * $paymentSession   = VirtualCurrencyPaymentSession(JFactory::getDbo());
     * $paymentSession->load($id);
     *
     * $paymentSession->delete();
     * </code>
     */
    public function delete()
    {
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName("#__vc_paymentsessions"))
            ->where($this->db->quoteName("id") ." = " . $this->id);

        $this->db->setQuery($query);
        $this->db->execute();

        $this->reset();

    }

    protected function reset()
    {
        $this->id = 0;
        $this->user_id = 0;
        $this->currency_id = 0;
        $this->amount = 0;
        $this->record_date = null;
    }

    /**
     * Return the ID of the payment session.
     *
     * <code>
     * $paymentSession   = new VirtualCurrencyPaymentSession();
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
     * Return the number of units, that will be bought.
     *
     * <code>
     * $id = 1;
     *
     * $paymentSession   = VirtualCurrencyPaymentSession(JFactory::getDbo());
     * $paymentSession->load($id);
     *
     * $amount = $paymentSession->getAmount();
     * </code>
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Return the ID of the user that is going to buy virtual currency.
     *
     * <code>
     * $id = 1;
     *
     * $paymentSession   = VirtualCurrencyPaymentSession(JFactory::getDbo());
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
     * Return the ID of the currency that is going to bought by a user.
     *
     * <code>
     * $id = 1;
     *
     * $paymentSession   = VirtualCurrencyPaymentSession(JFactory::getDbo());
     * $paymentSession->load($id);
     *
     * $currencyId = $paymentSession->getCurrencyId();
     * </code>
     *
     * @return int
     */
    public function getCurrencyId()
    {
        return (int)$this->currency_id;
    }

    /**
     * Returns an associative array of object properties.
     *
     * <code>
     * $id = 1;
     *
     * $paymentSession    = new VirtualCurrencyPaymentSession(JFactory::getDbo());
     * $paymentSession->load($id);
     *
     * $properties = $paymentSession->getProperties();
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
