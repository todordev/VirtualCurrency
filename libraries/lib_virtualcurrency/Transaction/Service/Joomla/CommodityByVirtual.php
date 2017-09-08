<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Transactions\Service
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Transaction\Service\Joomla;

use Prism\Domain\ApplicationService;
use Virtualcurrency\Account\Account;
use Virtualcurrency\Account\Command\Gateway\JoomlaUpdateAmount;
use Virtualcurrency\Account\Command\UpdateAmount;
use Virtualcurrency\Account\Gateway\JoomlaGateway as AccountJoomlaGateway;
use Virtualcurrency\Account\Mapper as AccountMapper;
use Virtualcurrency\Account\Repository as AccountRepository;
use Virtualcurrency\Transaction\Transaction;
use Virtualcurrency\Transaction\Mapper as TransactionMapper;
use Virtualcurrency\Transaction\Repository as TransactionRepository;
use Virtualcurrency\Transaction\Gateway\JoomlaGateway as TransactionJoomlaGateway;
use Virtualcurrency\User\Commodity\Command\StoreNumber;
use Virtualcurrency\User\Commodity\Command\Gateway\JoomlaStoreNumber;
use Virtualcurrency\User\Commodity\Mapper as UserCommodityMapper;
use Virtualcurrency\User\Commodity\Repository as UserCommodityRepository;
use Virtualcurrency\User\Commodity\Gateway\JoomlaGateway as UserCommodityJoomlaGateway;
use Virtualcurrency\Commodity\Command\Gateway\JoomlaUpdateInStock;
use Virtualcurrency\Commodity\Command\UpdateInStock;
use Prism\Database\Condition\Condition;
use Prism\Database\Condition\Conditions;
use Prism\Database\Request\Request;

class CommodityByVirtual implements ApplicationService
{
    protected $transaction;
    protected $account;
    protected $db;

    public function __construct(Transaction $transaction, Account $account, \JDatabaseDriver $db)
    {
        $this->account     = $account;
        $this->transaction = $transaction;
        $this->db          = $db;
    }

    public function execute(array $request = array())
    {
        // Store the new transaction data.
        $txnMapper      = new TransactionMapper(new TransactionJoomlaGateway($this->db));
        $txnRepository  = new TransactionRepository($txnMapper);
        $txnRepository->store($this->transaction);

        // Decrease the amount in user's account.
        $this->account->decreaseAmount($this->transaction->getAmount());

        $updateAmountCommand  = new UpdateAmount($this->account);
        $updateAmountCommand->setGateway(new JoomlaUpdateAmount($this->db));
        $updateAmountCommand->handle();

        // Increase units to other receiver account or commodity store.
        if (strcmp('currency', $this->transaction->getItemType()) === 0) {
            // Prepare conditions.
            $conditionUserId = new Condition(['column' => 'user_id', 'value' => $this->transaction->getReceiverId(), 'operator'=> '=', 'table' => 'a']);
            $conditionCurrencyId = new Condition(['column' => 'currency_id', 'value' => $this->transaction->getItemId(), 'operator'=> '=', 'table' => 'a']);

            $conditions = new Conditions();
            $conditions
                ->addCondition($conditionUserId)
                ->addCondition($conditionCurrencyId);

            // Prepare database request.
            $databaseRequest = new Request();
            $databaseRequest->setConditions($conditions);

            $accountMapper      = new AccountMapper(new AccountJoomlaGateway($this->db));
            $accountRepository  = new AccountRepository($accountMapper);
            $account = $accountRepository->fetch($databaseRequest);

            // Update account amount.
            $account->increaseAmount($this->transaction->getUnits());

            $updateAmountCommand  = new UpdateAmount($account);
            $updateAmountCommand->setGateway(new JoomlaUpdateAmount($this->db));
            $updateAmountCommand->handle();
        } else {
            // Prepare conditions.
            $conditionUserId = new Condition(['column' => 'user_id', 'value' => $this->transaction->getReceiverId(), 'operator'=> '=', 'table' => 'a']);
            $conditionCurrencyId = new Condition(['column' => 'commodity_id', 'value' => $this->transaction->getItemId(), 'operator'=> '=', 'table' => 'a']);

            $conditions = new Conditions();
            $conditions
                ->addCondition($conditionUserId)
                ->addCondition($conditionCurrencyId);

            // Prepare database request.
            $databaseRequest = new Request();
            $databaseRequest->setConditions($conditions);

            $commodityMapper      = new UserCommodityMapper(new UserCommodityJoomlaGateway($this->db));
            $commodityRepository  = new UserCommodityRepository($commodityMapper);
            $userCommodity        = $commodityRepository->fetch($databaseRequest);

            // If there are no enough units to be given, leave a message to the administrator.
            $commodity            = $userCommodity->getCommodity();
            if (!$commodity->isUnlimited() and !$commodity->hasUnits($this->transaction->getUnits())) {
                throw new \RuntimeException('Transaction process successfully but there was not enough commodities to be given to the receiver.');
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

            $updateCommodityNumber  = new StoreNumber($userCommodity);
            $updateCommodityNumber->setGateway(new JoomlaStoreNumber($this->db));
            $updateCommodityNumber->handle();
        }
    }
}
