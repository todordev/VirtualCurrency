<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Account
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Account;

use Prism\Domain\Entity;
use Prism\Domain\EntityId;
use Prism\Domain\EntityProperties;
use Prism\Domain\Populator;
use Prism\Domain\PropertiesMethods;
use Virtualcurrency\Currency\Currency;

/**
 * This class contains methods,
 * which are used for managing virtual bank account.
 *
 * @package      Virtualcurrency
 * @subpackage   Account
 */
class Account implements Entity, EntityProperties
{
    use EntityId, Populator, PropertiesMethods;

    protected $amount;
    protected $note;
    protected $published = 0;
    protected $created_at;
    protected $user_id;
    protected $currency_id;

    protected $name;

    /**
     * @var Currency
     */
    protected $currency;

    /**
     * Increase the number of units ( virtual currency ).
     *
     * <code>
     *  $accountId = 1;
     *
     *  $gateway     = new Virtualcurrency\Account\Gateway\JoomlaGateway(\JFactory::getDbo());
     *  $mapper      = new Virtualcurrency\Account\Mapper($gateway);
     *  $repository  = new Virtualcurrency\Account\Repository($mapper);
     *
     *  $account = $repository->fetchById($accountId);
     *  $account->increaseAmount(50);
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
     *  $accountId = 1;
     *
     *  $gateway     = new Virtualcurrency\Account\Gateway\JoomlaGateway(\JFactory::getDbo());
     *  $mapper      = new Virtualcurrency\Account\Mapper($gateway);
     *  $repository  = new Virtualcurrency\Account\Repository($mapper);
     *
     *  $account = $repository->fetchById($accountId);
     *  $account->decreaseAmount(50);
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
     * $account   = new Virtualcurrency\Account\Account;
     * $account->bind($data);
     * </code>
     *
     * @param array $data
     * @param array $ignored
     */
    public function bind(array $data, array $ignored = array())
    {
        // Create Currency object.
        if (array_key_exists('currency', $data)) {
            $this->currency = new Currency();
            $this->currency->bind($data['currency']);
            unset($data['currency']);
        }

        foreach ($data as $key => $value) {
            if (!in_array($key, $ignored, true)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Return the name of the holder.
     *
     * <code>
     *  $accountId = 1;
     *
     *  $gateway     = new Virtualcurrency\Account\Gateway\JoomlaGateway(\JFactory::getDbo());
     *  $mapper      = new Virtualcurrency\Account\Mapper($gateway);
     *  $repository  = new Virtualcurrency\Account\Repository($mapper);
     *
     *  $account = $repository->fetchById($accountId);
     *
     *  echo $account->getName();
     * </code>
     *
     * @return string
     */
    public function getName()
    {
        return (string)$this->name;
    }

    /**
     * Return a note about this account.
     *
     * <code>
     *  $accountId = 1;
     *
     *  $gateway     = new Virtualcurrency\Account\Gateway\JoomlaGateway(\JFactory::getDbo());
     *  $mapper      = new Virtualcurrency\Account\Mapper($gateway);
     *  $repository  = new Virtualcurrency\Account\Repository($mapper);
     *
     *  $account = $repository->fetchById($accountId);
     *
     *  echo $account->getNote();
     * </code>
     *
     * @return string
     */
    public function getNote()
    {
        return (string)$this->note;
    }

    /**
     * Return the status of the account.
     *
     * <code>
     *  $accountId = 1;
     *
     *  $gateway     = new Virtualcurrency\Account\Gateway\JoomlaGateway(\JFactory::getDbo());
     *  $mapper      = new Virtualcurrency\Account\Mapper($gateway);
     *  $repository  = new Virtualcurrency\Account\Repository($mapper);
     *
     *  $account = $repository->fetchById($accountId);
     *
     *  echo $account->getPublished();
     * </code>
     *
     * @return int
     */
    public function getPublished()
    {
        return (int)$this->published;
    }

    /**
     * Return the user ID of the account.
     *
     * <code>
     *  $accountId = 1;
     *
     *  $gateway     = new Virtualcurrency\Account\Gateway\JoomlaGateway(\JFactory::getDbo());
     *  $mapper      = new Virtualcurrency\Account\Mapper($gateway);
     *  $repository  = new Virtualcurrency\Account\Repository($mapper);
     *
     *  $account = $repository->fetchById($accountId);
     *
     *  echo $account->getUserId();
     * </code>
     *
     * @return int
     */
    public function getUserId()
    {
        return (int)$this->user_id;
    }

    /**
     * Return the currency ID of the account.
     *
     * <code>
     *  $accountId = 1;
     *
     *  $gateway     = new Virtualcurrency\Account\Gateway\JoomlaGateway(\JFactory::getDbo());
     *  $mapper      = new Virtualcurrency\Account\Mapper($gateway);
     *  $repository  = new Virtualcurrency\Account\Repository($mapper);
     *
     *  $account = $repository->fetchById($accountId);
     *
     *  echo $account->getCurrencyId();
     * </code>
     *
     * @return int
     */
    public function getCurrencyId()
    {
        return (int)$this->currency_id;
    }

    /**
     * Return the date when this account has been created.
     *
     * <code>
     *  $accountId = 1;
     *
     *  $gateway     = new Virtualcurrency\Account\Gateway\JoomlaGateway(\JFactory::getDbo());
     *  $mapper      = new Virtualcurrency\Account\Mapper($gateway);
     *  $repository  = new Virtualcurrency\Account\Repository($mapper);
     *
     *  $account = $repository->fetchById($accountId);
     *
     *  echo $account->getNote();
     * </code>
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return (string)$this->created_at;
    }

    /**
     * Return the amount collected in the account.
     *
     * <code>
     *  $accountId = 1;
     *
     *  $gateway     = new Virtualcurrency\Account\Gateway\JoomlaGateway(\JFactory::getDbo());
     *  $mapper      = new Virtualcurrency\Account\Mapper($gateway);
     *  $repository  = new Virtualcurrency\Account\Repository($mapper);
     *
     *  $account = $repository->fetchById($accountId);
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
     * Check if account is active.
     *
     * <code>
     *  $accountId = 1;
     *
     *  $gateway     = new Virtualcurrency\Account\Gateway\JoomlaGateway(\JFactory::getDbo());
     *  $mapper      = new Virtualcurrency\Account\Mapper($gateway);
     *  $repository  = new Virtualcurrency\Account\Repository($mapper);
     *
     *  $account = $repository->fetchById($accountId);
     *
     *  if (!$account->isActive()) {
     *  ...
     *  }
     * </code>
     *
     * @return bool
     */
    public function isActive()
    {
        return (bool)$this->published;
    }

    /**
     * Calculate the price in real currency.
     *
     * <code>
     *  $accountId = 1;
     *  $numberOfUnits = 10;
     *
     *  $gateway     = new Virtualcurrency\Account\Gateway\JoomlaGateway(\JFactory::getDbo());
     *  $mapper      = new Virtualcurrency\Account\Mapper($gateway);
     *  $repository  = new Virtualcurrency\Account\Repository($mapper);
     *
     *  $account = $repository->fetchById($accountId);
     *
     *  echo $account->calculateRealPrice($numberOfUnits);
     * </code>
     *
     * @param int $numberOfUnits
     *
     * @return float
     */
    public function calculateRealPrice($numberOfUnits)
    {
        $price = $this->currency->getParam('price_real', 0.00);
        if ($price > 0 and $numberOfUnits > 0) {
            return round($price * $numberOfUnits, 2);
        }

        return 0.00;
    }

    /**
     * Return the price in virtual currency.
     *
     * <code>
     *  $accountId = 1;
     *  $numberOfUnits = 10;
     *
     *  $gateway     = new Virtualcurrency\Account\Gateway\JoomlaGateway(\JFactory::getDbo());
     *  $mapper      = new Virtualcurrency\Account\Mapper($gateway);
     *  $repository  = new Virtualcurrency\Account\Repository($mapper);
     *
     *  $account = $repository->fetchById($accountId);
     *
     *  echo $account->calculateVirtualPrice($numberOfUnits);
     * </code>
     *
     * @param int $numberOfUnits
     *
     * @return float
     */
    public function calculateVirtualPrice($numberOfUnits)
    {
        $price = $this->currency->getParam('price_virtual', 0.00);
        if ($price > 0 and $numberOfUnits > 0) {
            return round($price * $numberOfUnits, 2);
        }

        return 0.00;
    }

    /**
     * Return currency object on which is based current account.
     *
     * <code>
     *  $accountId = 1;
     *
     *  $gateway     = new Virtualcurrency\Account\Gateway\JoomlaGateway(\JFactory::getDbo());
     *  $mapper      = new Virtualcurrency\Account\Mapper($gateway);
     *  $repository  = new Virtualcurrency\Account\Repository($mapper);
     *
     *  $account = $repository->fetchById($accountId);
     *
     *  $currency = $account->getCurrency();
     * </code>
     *
     * @return Currency
     */
    public function getCurrency()
    {
        if ($this->currency === null) {
            $this->currency = new Currency();
        }

        return $this->currency;
    }
}
