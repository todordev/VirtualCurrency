<?php
/**
 * @package      Virtualcurrency\Transaction
 * @subpackage   Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Transaction\Gateway;

use Prism\Database\JoomlaDatabase;
use Joomla\Utilities\ArrayHelper;
use Virtualcurrency\Transaction\Transaction;

/**
 * Joomla database gateway.
 *
 * @package      Virtualcurrency\Transaction
 * @subpackage   Gateway
 */
class JoomlaGateway extends JoomlaDatabase implements TransactionGateway
{
    /**
     * Load the data from database by conditions and return an entity.
     *
     * <code>
     * $conditions = array(
     *     'txn_id'   => 'TXN_12345',
     *     'user_id' => '1'
     * );
     *
     * $gateway = new JoomlaGateway(\JFactory::getDbo());
     * $item    = $dbGateway->fetch($conditions);
     * </code>
     *
     * @param array  $conditions
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     *
     * @return array
     */
    public function fetch(array $conditions = array())
    {
        if (!$conditions) {
            throw new \UnexpectedValueException('There are no conditions that the system should use to fetch data.');
        }

        $query = $this->getQuery();

        // Filter by conditions.
        foreach ($conditions as $key => $value) {
            $query->where($this->db->quoteName('a.' . $key) . '=' . $this->db->quote($value));
        }

        $this->db->setQuery($query);

        return (array)$this->db->loadAssoc();
    }

    /**
     * Load the data from database and return a collection.
     *
     * <code>
     * $conditions = array(
     *     'ids' => array(1,2,3,4)
     * );
     *
     * $gateway = new JoomlaGateway(\JFactory::getDbo());
     * $items   = $dbGateway->fetchCollection($conditions);
     * </code>
     *
     * @param array  $conditions
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     *
     * @return array
     */
    public function fetchCollection(array $conditions = array())
    {
        if (!$conditions) {
            throw new \UnexpectedValueException('There are no conditions that the system should use to fetch data.');
        }

        $query = $this->getQuery();

        // Filter by IDs
        if (array_key_exists('ids', $conditions) and is_array($conditions['ids'])) {
            $ids = ArrayHelper::toInteger($conditions['ids']);

            if (count($ids) > 0) {
                $query->where($this->db->quoteName('a.id') .' IN ('. implode(',', $ids) .')');
            }

            unset($conditions['ids']);
        }

        // Filter by state.
        if (array_key_exists('state', $conditions)) {
            $query->where($this->db->quoteName('a.published') .'='. (int)$conditions['state']);
            unset($conditions['state']);
        }

        // Filter by other conditions.
        foreach ($conditions as $key => $value) {
            $query->where($this->db->quoteName('a.' . $key) .'='. $this->db->quote($value));
        }

        $this->db->setQuery($query);

        return (array)$this->db->loadAssocList();
    }

    /**
     * Fetch a data from database by item ID.
     *
     * <code>
     * $itemId = 1;
     *
     * $gateway = new JoomlaGateway(\JFactory::getDbo());
     * $items   = $dbGateway->fetchById($itemId);
     * </code>
     *
     * @param int $id
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return array
     */
    public function fetchById($id)
    {
        if (!$id) {
            throw new \InvalidArgumentException('There is no ID.');
        }

        $query = $this->getQuery();

        // Filter by ID.
        $query->where('a.id = ' . (int)$id);

        $this->db->setQuery($query);

        return (array)$this->db->loadAssoc();
    }

    /**
     * Prepare the query by query builder.
     *
     * @return \JDatabaseQuery
     *
     * @throws \RuntimeException
     */
    protected function getQuery()
    {
        $query = $this->db->getQuery(true);
        $query
            ->select(
                'a.id, a.title, a.units, a.txn_id, a.txn_amount, a.txn_currency, a.txn_status, a.txn_date, a.error_msg,' .
                'a.service_provider, a.service_alias, a.extra_data, a.item_id, a.item_type, a.sender_id, a.receiver_id'
            )
            ->from($this->db->quoteName('#__vc_transactions', 'a'));

        return $query;
    }

    public function insertObject(Transaction $object)
    {
        // Prepare extra data value.
        $txnDate   = $object->getTransactionDate() ? $this->db->quote($object->getTransactionDate()) : 'NULL';
        $errorMsg  = $object->getErrorMessage() ? $this->db->quote($object->getErrorMessage()) : 'NULL';

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
}
