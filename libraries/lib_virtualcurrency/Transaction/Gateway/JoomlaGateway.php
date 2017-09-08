<?php
/**
 * @package      Virtualcurrency\Transaction
 * @subpackage   Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Transaction\Gateway;

use Joomla\Utilities\ArrayHelper;
use Prism\Database\JoomlaDatabaseGateway;
use Prism\Database\Request\Request;
use Virtualcurrency\Transaction\Transaction;
use Prism\Database\Joomla\FetchMethods;
use Prism\Database\Joomla\FetchCollectionMethod;

/**
 * Joomla database gateway.
 *
 * @package      Virtualcurrency\Transaction
 * @subpackage   Gateway
 */
class JoomlaGateway extends JoomlaDatabaseGateway implements TransactionGateway
{
    use FetchMethods, FetchCollectionMethod;

    /**
     * Prepare the query by query builder.
     *
     * @param Request $request
     * @return \JDatabaseQuery
     *
     * @throws \RuntimeException
     */
    protected function getQuery(Request $request = null)
    {
        $defaultFields  = [
            'a.id', 'a.title', 'a.units', 'a.txn_id', 'a.txn_amount', 'a.txn_currency', 'a.txn_status', 'a.txn_date', 'a.error_msg',
            'a.service_provider', 'a.service_alias', 'a.extra_data', 'a.item_id', 'a.item_type', 'a.sender_id', 'a.receiver_id'
        ];
        $fields  = $this->prepareFields($request, $defaultFields);

        // If there are no fields, use default ones.
        if (count($fields) === 0) {
            $fields = $defaultFields;
            unset($defaultFields);
        }

        $query = $this->db->getQuery(true);
        $query
            ->select($fields)
            ->from($this->db->quoteName('#__vc_transactions', 'a'));

        return $query;
    }

    public function insertObject(Transaction $object)
    {
        // Prepare extra data value.
        $txnDate   = $object->getTransactionDate() ? $this->db->quote($object->getTransactionDate()) : 'NULL';
        $errorMsg  = $object->getErrorMessage() ? $this->db->quote($object->getErrorMessage()) : 'NULL';

        $extraData = 'NULL';
        if ($object->getExtraData()) {
            $extraData = json_encode($object->getExtraData());
            $extraData = $extraData ? $this->db->quote($extraData) : 'NULL';
        }

        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName('#__vc_transactions'))
            ->set($this->db->quoteName('title') . '=' . $this->db->quote($object->getTitle()))
            ->set($this->db->quoteName('units') . '=' . $this->db->quote($object->getUnits()))
            ->set($this->db->quoteName('txn_id') . '=' . $this->db->quote($object->getTransactionId()))
            ->set($this->db->quoteName('txn_amount') . '=' . $this->db->quote($object->getAmount()))
            ->set($this->db->quoteName('txn_currency') . '=' . $this->db->quote($object->getTransaction()))
            ->set($this->db->quoteName('txn_status') . '=' . $this->db->quote($object->getStatus()))
            ->set($this->db->quoteName('txn_date') . '=' . $txnDate)
            ->set($this->db->quoteName('extra_data') . '=' . $extraData)
            ->set($this->db->quoteName('service_provider') . '=' . $this->db->quote($object->getServiceProvider()))
            ->set($this->db->quoteName('service_alias') . '=' . $this->db->quote($object->getServiceAlias()))
            ->set($this->db->quoteName('item_id') . '=' . $this->db->quote($object->getItemId()))
            ->set($this->db->quoteName('item_type') . '=' . $this->db->quote($object->getItemType()))
            ->set($this->db->quoteName('sender_id') . '=' . $this->db->quote($object->getSenderId()))
            ->set($this->db->quoteName('receiver_id') . '=' . $this->db->quote($object->getReceiverId()))
            ->set($this->db->quoteName('error_msg') . '=' . $errorMsg);

        $this->db->setQuery($query);
        $this->db->execute();

        $object->setId($this->db->insertid());
    }

    public function updateObject(Transaction $object)
    {
        // Prepare extra data value.
        $txnDate   = (!$object->getTransactionDate()) ? 'NULL' : $this->db->quote($object->getTransactionDate());
        $errorMsg  = (!$object->getErrorMessage()) ? 'NULL' : $this->db->quote($object->getErrorMessage());

        $extraData = 'NULL';
        if ($object->getExtraData()) {
            $extraData = json_encode($object->getExtraData());
            $extraData = $extraData ? $this->db->quote($extraData) : 'NULL';
        }

        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName('#__vc_transactions'))
            ->set($this->db->quoteName('title') . '=' . $this->db->quote($object->getTitle()))
            ->set($this->db->quoteName('units') . '=' . $this->db->quote($object->getUnits()))
            ->set($this->db->quoteName('txn_id') . '=' . $this->db->quote($object->getTransactionId()))
            ->set($this->db->quoteName('txn_amount') . '=' . $this->db->quote($object->getAmount()))
            ->set($this->db->quoteName('txn_currency') . '=' . $this->db->quote($object->getTransaction()))
            ->set($this->db->quoteName('txn_status') . '=' . $this->db->quote($object->getStatus()))
            ->set($this->db->quoteName('txn_date') . '=' . $txnDate)
            ->set($this->db->quoteName('extra_data') . '=' . $extraData)
            ->set($this->db->quoteName('service_provider') . '=' . $this->db->quote($object->getServiceProvider()))
            ->set($this->db->quoteName('service_alias') . '=' . $this->db->quote($object->getServiceAlias()))
            ->set($this->db->quoteName('item_id') . '=' . $this->db->quote($object->getItemId()))
            ->set($this->db->quoteName('item_type') . '=' . $this->db->quote($object->getItemType()))
            ->set($this->db->quoteName('sender_id') . '=' . $this->db->quote($object->getSenderId()))
            ->set($this->db->quoteName('receiver_id') . '=' . $this->db->quote($object->getReceiverId()))
            ->set($this->db->quoteName('error_msg') . '=' . $errorMsg);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Prepare some query filters.
     *
     * @param \JDatabaseQuery $query
     * @param Request         $request
     *
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    protected function filter(\JDatabaseQuery $query, Request $request)
    {
        $conditions = $request->getConditions();

        // Filter by IDs
        if ($conditions->getSpecificCondition('ids')) {
            $condition = $conditions->getSpecificCondition('ids');
            if (is_array($condition->getValue())) {
                $ids = ArrayHelper::toInteger($condition->getValue());
                $ids = array_filter(array_unique($ids));

                if (count($ids) > 0) {
                    $query->where($this->db->quoteName('a.id') . ' IN (' . implode(',', $ids) . ')');
                }
            }
        }

        // Filter by standard conditions.
        parent::filter($query, $request);
    }
}
