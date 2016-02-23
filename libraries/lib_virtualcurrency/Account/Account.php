<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Account;

use Prism\Database;
use Joomla\Registry\Registry;
use Virtualcurrency\Currency\Currency;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods,
 * which are used for managing virtual bank account.
 *
 * @package      Virtualcurrency
 * @subpackage   Library
 */
class Account extends Database\Table
{
    protected $id;
    protected $amount;
    protected $note;
    protected $user_id;
    protected $currency_id;

    protected $name;
    protected $currency;

    /**
     * Increase the number of units ( virtual currency ).
     *
     * <code>
     *  // Get user account by account ID
     *  $accountId = 1;
     *
     *  $account   = new Virtualcurrency\Account\Account();
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
     *  $account   = new Virtualcurrency\Account\Account();
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
     * $account   = new Virtualcurrency\Account\Account(JFactory::getDbo());
     * $account->load($accountId);
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
                'a.id, a.amount, a.note, a.user_id, a.currency_id, ' .
                'b.title, b.code, b.symbol, b.image, b.image_icon, ' .
                'c.name'
            )
            ->from($this->db->quoteName('#__vc_accounts', 'a'))
            ->innerJoin($this->db->quoteName('#__vc_currencies', 'b') . ' ON a.currency_id = b.id')
            ->innerJoin($this->db->quoteName('#__users', 'c') . ' ON a.user_id = c.id');

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
     * $data = (
     *  "amount"  => 100.00,
     *  "note"  => "note...",
     *  "user_id" => 1,
     *  "currency_id" => 2
     * );
     *
     * $account   = new Virtualcurrency\Account\Account(JFactory::getDbo());
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
        $note   = (!$this->note) ? 'NULL' : $this->db->quote($this->note);

        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName('#__vc_accounts'))
            ->set($this->db->quoteName('amount') . '=' . $this->db->quote($this->amount))
            ->set($this->db->quoteName('note') . '=' . $note)
            ->set($this->db->quoteName('user_id') . '=' . (int)$this->user_id)
            ->set($this->db->quoteName('currency_id') . '=' . (int)$this->currency_id);

        $this->db->setQuery($query);
        $this->db->execute();

        $this->id = $this->db->insertid();
    }

    protected function updateObject()
    {
        $note   = (!$this->note) ? 'NULL' : $this->db->quote($this->note);

        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__vc_accounts'))
            ->set($this->db->quoteName('amount') . '=' . $this->db->quote($this->amount))
            ->set($this->db->quoteName('note') . '=' . $note)
            ->set($this->db->quoteName('user_id') . '=' . (int)$this->user_id)
            ->set($this->db->quoteName('currency_id') . '=' . (int)$this->currency_id)
            ->where($this->db->quoteName('id') . '=' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Set notification data to object parameters.
     *
     * <code>
     * $data = array(
     *      "amount"          => 100,
     *      "note"            => "...",
     *      "currency_id"     => 1
     *      "user_id"         => 2
     * );
     *
     * $account   = new Virtualcurrency\Account\Account(\JFactory::getDbo());
     * $account->bind($data);
     * </code>
     *
     * @param array $data
     * @param array $ignored
     */
    public function bind($data, array $ignored = array())
    {
        // Create Currency object.
        $currencyColumns = array('title', 'code', 'symbol', 'image', 'image_icon');
        $currencyData    = array();

        foreach ($currencyColumns as $columnName) {
            if (array_key_exists($columnName, $data)) {
                $currencyData[$columnName] = $data[$columnName];
                unset($data[$columnName]);
            }
        }

        $this->currency = new Currency($this->db);
        $this->currency->bind($currencyData);
        unset($currencyData);

        // Parse parameters of the object if they exists.
        if (array_key_exists('params', $data) and !in_array('params', $ignored, true)) {
            $this->params = new Registry($data['params']);
            unset($data['params']);
        }

        foreach ($data as $key => $value) {
            if (!in_array($key, $ignored, true)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Store the amount of the account in database.
     *
     * <code>
     *  // Get user account by account ID
     *  $accountId = 1;
     *
     *  $account   = new Virtualcurrency\Account\Account();
     *  $account->setDb(JFactory::getDbo());
     *  $account->load($accountId);
     *
     *  // Increase the amount and store the new value.
     *  $account->increaseAmount(50);
     *  $account->storeAmount();
     * </code>
     */
    public function storeAmount()
    {
        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__vc_accounts'))
            ->set($this->db->quoteName('amount') . '=' . $this->db->quote($this->amount))
            ->where($this->db->quoteName('id') . '=' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Return the ID of the account.
     *
     * <code>
     *  // Get user account by account ID
     *  $keys = array(
     *     'user_id' => 1,
     *     'currency_id' => 2,
     *  );
     *
     *  $account   = new Virtualcurrency\Account\Account(JFactory::getDbo());
     *  $account->load($keys);
     *
     *  if (!$account->getId()) {
     *  ....
     * }
     * </code>
     *
     * @return string
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * Return the name of the holder.
     *
     * <code>
     *  // Get user account by account ID
     *  $accountId = 1;
     *
     *  $account   = new Virtualcurrency\Account\Account(JFactory::getDbo());
     *  $account->load($accountId);
     *
     *  echo $account->getName();
     * </code>
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return the amount collected in the account.
     *
     * <code>
     *  // Get user account by account ID
     *  $accountId = 1;
     *
     *  $account   = new Virtualcurrency\Account\Account(JFactory::getDbo());
     *  $account->load($accountId);
     *
     *  echo $account->getAmount();
     * </code>
     *
     * @return float
     */
    public function getAmount()
    {
        return (float)$this->amount;
    }

    /**
     * Return the price in real currency.
     *
     * <code>
     *  $accountId = 1;
     *  $numberOfUnits = 10;
     *
     *  $account   = new Virtualcurrency\Account\Account(JFactory::getDbo());
     *  $account->load($accountId);
     *
     *  echo $account->getRealPrice($numberOfUnits);
     * </code>
     *
     * @param string $type The type of the currency - real or virtual.
     * @param int $numberOfUnits
     *
     * @return float
     */
    public function getPrice($type, $numberOfUnits = 1)
    {
        if (strcmp('real', $type) === 0) {
            $price = $this->getParam('price', 0.00);
            if ($price > 0 and $numberOfUnits > 0) {
                return round($price * $numberOfUnits, 2);
            }
        }

        if (strcmp('virtual', $type) === 0) {
            $price = $this->getParam('price-virtual', 0.00);
            if ($price > 0 and $numberOfUnits > 0) {
                return round($price * $numberOfUnits, 2);
            }
        }

        return 0.00;
    }

    /**
     * Return currency object on which is based current account.
     *
     * <code>
     *  // Get user account by account ID
     *  $accountId = 1;
     *
     *  $account   = new Virtualcurrency\Account\Account(JFactory::getDbo());
     *  $account->load($accountId);
     *
     *  echo $account->getCurrency()->getIcon();
     * </code>
     *
     * @return Currency
     */
    public function getCurrency()
    {
        if ($this->currency === null) {
            $this->currency = new Currency($this->db);

            if ($this->currency_id > 0) {
                $this->currency->load($this->currency_id);
            }
        }

        return $this->currency;
    }

    /**
     * Create new user account.
     *
     * <code>
     *  $data = array(
     *      'user_id' => 1,
     *      'currency_id' => 2,
     *      'note' => '...'
     *  )
     *
     *  $account   = new Virtualcurrency\Account\Account(JFactory::getDbo());
     *  $account->open($data['user_id'], $data['currency_id']);
     * </code>
     *
     * @param array $data
     * @param bool $force // Force creation of account. If it is true, the system does not check for existing account.
     *
     * @throws /InvalidArgumentException
     *
     * @return Currency
     */
    public function open(array $data, $force = false)
    {
        if (!array_key_exists('user_id', $data)) {
            throw new \InvalidArgumentException('It is missing user ID');
        }

        if (!array_key_exists('currency_id', $data)) {
            throw new \InvalidArgumentException('It is missing currency ID');
        }

        $this->user_id      = $data['user_id'];
        $this->currency_id  = $data['currency_id'];
        $this->note         = (!array_key_exists('note', $data)) ? '' : $data['note'];

        // Check for existing account.
        if (!$force) {
            $query = $this->db->getQuery(true);
            $query
                ->select('COUNT(*)')
                ->from($this->db->quoteName('#__vc_accounts', 'a'))
                ->where('a.user_id = ' . (int)$this->user_id)
                ->where('a.currency_id = ' . (int)$this->currency_id);

            $this->db->setQuery($query);
            $result = (bool)$this->db->loadResult();

            if ($result) {
                throw new \InvalidArgumentException('The account already exists.');
            }
        }

        $this->store();
    }
}
