<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Transaction
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Transaction;

use Prism\Domain;
use Prism\Database\Request\Request;
use Virtualcurrency\Transaction\Gateway\TransactionGateway;

/**
 * This class provides a glue between persistence layer and transaction object.
 *
 * @package      Virtualcurrency
 * @subpackage   Transaction
 */
class Repository extends Domain\Repository implements Domain\CollectionFetcher
{
    /**
     * Collection object.
     *
     * @var Domain\Collection
     */
    protected $collection;

    /**
     * @var TransactionGateway
     */
    protected $gateway;

    public function __construct(Mapper $mapper)
    {
        $this->mapper  = $mapper;
        $this->gateway = $mapper->getGateway();
    }

    /**
     * Load the data from database and return an entity.
     *
     * <code>
     * $currencyId  = 1;
     *
     * $gateway     = new Virtualcurrency\Transaction\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Virtualcurrency\Transaction\Mapper($gateway);
     * $repository  = new Virtualcurrency\Transaction\Repository($mapper);
     *
     * $currency    = $repository->findById($currencyId);
     * </code>
     *
     * @param int $id
     * @param Request $request
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return Transaction
     */
    public function fetchById($id, Request $request = null)
    {
        if (!$id) {
            throw new \InvalidArgumentException('There is no ID.');
        }

        $data = $this->gateway->fetchById($id, $request);

        return $this->mapper->create($data);
    }

    /**
     * Load the data from database by conditions and return an entity.
     *
     * <code>
     * $conditions = array(
     *     'code' => 'USD',
     *     'symbol' => '$'
     * );
     *
     * $gateway     = new Virtualcurrency\Transaction\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Virtualcurrency\Transaction\Mapper($gateway);
     * $repository  = new Virtualcurrency\Transaction\Repository($mapper);
     *
     * $currency    = $repository->fetch($conditions);
     * </code>
     *
     * @param Request $request
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     *
     * @return Transaction
     */
    public function fetch(Request $request)
    {
        if (!$request) {
            throw new \UnexpectedValueException('There are no conditions that the system should use to fetch data.');
        }

        $data = $this->gateway->fetch($request);

        return $this->mapper->create($data);
    }

    /**
     * Load the data from database and return a collection.
     *
     * <code>
     * $conditions = array(
     *     'ids' => array(1,2,3,4)
     * );
     *
     * $gateway     = new Virtualcurrency\Transaction\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Virtualcurrency\Transaction\Mapper($gateway);
     * $repository  = new Virtualcurrency\Transaction\Repository($mapper);
     *
     * $currencies  = $repository->fetchCollection($conditions);
     * </code>
     *
     * @param Request $request
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     *
     * @return Transactions
     */
    public function fetchCollection(Request $request)
    {
        if (!$request) {
            throw new \UnexpectedValueException('There are no conditions that the system should use to fetch data.');
        }

        $data = $this->gateway->fetchCollection($request);

        if ($this->collection === null) {
            $this->collection = new Transactions;
        }

        $this->collection->clear();
        if ($data) {
            foreach ($data as $row) {
                $this->collection[] = $this->mapper->create($row);
            }
        }

        return $this->collection;
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
     * $transaction    = new Virtualcurrency\Transaction\Transaction;
     * $transaction->bind($data);
     *
     * $gateway     = new Virtualcurrency\Transaction\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Virtualcurrency\Transaction\Mapper($gateway);
     * $repository  = new Virtualcurrency\Transaction\Repository($mapper);
     *
     * $repository->store($transaction);
     * </code>
     *
     * @param Transaction $transaction
     */
    public function store(Transaction $transaction)
    {
        $this->mapper->save($transaction);
    }
}
