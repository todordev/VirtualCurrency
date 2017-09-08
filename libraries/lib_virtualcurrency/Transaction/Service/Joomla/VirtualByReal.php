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
use Virtualcurrency\Account\Command\Gateway\JoomlaUpdateAmount;
use Virtualcurrency\Account\Command\UpdateAmount;
use Virtualcurrency\Transaction\Transaction;
use Virtualcurrency\Transaction\Mapper as TransactionMapper;
use Virtualcurrency\Transaction\Repository as TransactionRepository;
use Virtualcurrency\Transaction\Gateway\JoomlaGateway as TransactionJoomlaGateway;
use Virtualcurrency\Account\Gateway\JoomlaGateway as AccountJoomlaGateway;
use Virtualcurrency\Account\Mapper as AccountMapper;
use Virtualcurrency\Account\Repository as AccountRepository;
use Prism\Database\Condition\Condition;
use Prism\Database\Condition\Conditions;
use Prism\Database\Request\Request;

class VirtualByReal implements ApplicationService
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
        $conditionCurrencyId = new Condition(['column' => 'currency_id', 'value' => $this->transaction->getItemId(), 'operator' => '=', 'table' => 'a']);
        $conditions          = new Conditions();
        $conditions
            ->addCondition($conditionUserId)
            ->addCondition($conditionCurrencyId);

        // Prepare database request.
        $databaseRequest = new Request();
        $databaseRequest->setConditions($conditions);

        // Fetch the result.
        $accountMapper      = new AccountMapper(new AccountJoomlaGateway($this->db));
        $accountRepository  = new AccountRepository($accountMapper);
        $account            = $accountRepository->fetch($databaseRequest);

        // Update account amount.
        $account->increaseAmount($this->transaction->getUnits());

        $updateAmountCommand  = new UpdateAmount($account);
        $updateAmountCommand->setGateway(new JoomlaUpdateAmount($this->db));
        $updateAmountCommand->handle();

        // Store the new transaction data.
        $txnMapper      = new TransactionMapper(new TransactionJoomlaGateway($this->db));
        $txnRepository  = new TransactionRepository($txnMapper);
        $txnRepository->store($this->transaction);
    }
}
