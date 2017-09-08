<?php
/**
 * @package      Virtualcurrency\Transaction
 * @subpackage   Service\Joomla
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Transaction\Service\Joomla;

use Prism\Domain\ApplicationService;
use Virtualcurrency\Commodity\Command\Gateway\JoomlaUpdateInStock;
use Virtualcurrency\Commodity\Command\UpdateInStock;
use Virtualcurrency\Transaction\Transaction;
use Virtualcurrency\Transaction\Mapper as TransactionMapper;
use Virtualcurrency\Transaction\Repository as TransactionRepository;
use Virtualcurrency\Transaction\Gateway\JoomlaGateway as TransactionJoomlaGateway;
use Virtualcurrency\User\Commodity\Command\StoreNumber;
use Virtualcurrency\User\Commodity\Command\Gateway\JoomlaStoreNumber;
use Virtualcurrency\User\Commodity\Mapper as UserCommodityMapper;
use Virtualcurrency\User\Commodity\Repository as UserCommodityRepository;
use Virtualcurrency\User\Commodity\Gateway\JoomlaGateway as UserCommodityJoomlaGateway;
use Prism\Database\Condition\Condition;
use Prism\Database\Condition\Conditions;
use Prism\Database\Request\Request;

class CommodityByReal implements ApplicationService
{
    protected $transaction;
    protected $db;

    public function __construct(Transaction $transaction, \JDatabaseDriver $db)
    {
        $this->transaction = $transaction;
        $this->db          = $db;
    }

    public function execute(array $request = array())
    {
        // Prepare conditions.
        $conditionUserId = new Condition(['column' => 'user_id', 'value' => $this->transaction->getReceiverId(), 'operator' => '=', 'table' => 'a']);
        $conditionCommodityId = new Condition(['column' => 'commodity_id', 'value' => $this->transaction->getItemId(), 'operator' => '=', 'table' => 'a']);
        $conditions          = new Conditions();
        $conditions
            ->addCondition($conditionUserId)
            ->addCondition($conditionCommodityId);

        // Prepare database request.
        $databaseRequest = new Request();
        $databaseRequest->setConditions($conditions);

        // Create user commodity object.
        $commodityMapper      = new UserCommodityMapper(new UserCommodityJoomlaGateway($this->db));
        $commodityRepository  = new UserCommodityRepository($commodityMapper);
        $userCommodity        = $commodityRepository->fetch($databaseRequest);

        // Decrease the number of commodities.
        $commodity            = $userCommodity->getCommodity();

        // If there are no enough units to be given, leave a message to the administrator.
        if (!$commodity->isUnlimited() and !$commodity->hasUnits($this->transaction->getUnits())) {
            $this->transaction->setErrorMessage('Transaction process successfully but there was not enough commodities to be given to the receiver.');
        }

        // If the commodities are limited, decrease their number in stock.
        if (!$commodity->isUnlimited() and $commodity->hasUnits($this->transaction->getUnits())) {
            $commodity->decreaseInStock($this->transaction->getUnits());

            $updateInStockCommand  = new UpdateInStock($commodity);
            $updateInStockCommand->setGateway(new JoomlaUpdateInStock($this->db));
            $updateInStockCommand->handle();
        }

        // Update the number of user commodities.
        $userCommodity->increaseNumber($this->transaction->getUnits());

        // Store the number of user commodities.
        $updateCommodityNumber  = new StoreNumber($userCommodity);
        $updateCommodityNumber->setGateway(new JoomlaStoreNumber($this->db));
        $updateCommodityNumber->handle();

        // Store the new transaction data.
        $txnMapper      = new TransactionMapper(new TransactionJoomlaGateway($this->db));
        $txnRepository  = new TransactionRepository($txnMapper);
        $txnRepository->store($this->transaction);
    }
}
