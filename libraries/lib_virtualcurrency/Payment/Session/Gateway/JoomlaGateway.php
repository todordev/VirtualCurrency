<?php
/**
 * @package      Virtualcurrency\Payment\Session
 * @subpackage   Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Payment\Session\Gateway;

use Prism\Database\JoomlaDatabase;
use Virtualcurrency\Payment\Session\Session;

/**
 * Joomla database gateway.
 *
 * @package      Virtualcurrency\Payment\Session
 * @subpackage   Gateway
 */
class JoomlaGateway extends JoomlaDatabase implements SessionGateway
{
    /**
     * Load the data from database by conditions and return an entity.
     *
     * @param array $conditions
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
     * Fetch a data from database by item ID.
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
        $result = (array)$this->db->loadAssoc();

        // If it is not empty, get the gateways data.
        if (array_key_exists('id', $result)) {
            $query = $this->db->getQuery(true);
            $query
                ->select('b.order_id, b.token, b.alias, b.data')
                ->from($this->db->quoteName('#__vc_paymentsessiongateways', 'b'))
                ->where('b.id = ' . (int)$result['id']);

            $this->db->setQuery($query);
            $result['services'] = (array)$this->db->loadAssocList();
        }

        return $result;
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
            ->select('a.id, a.user_id, a.item_id, a.items_number, a.item_type, a.gateway, a.session_id, a.record_date')
            ->from($this->db->quoteName('#__vc_paymentsessions', 'a'));

        return $query;
    }

    public function insertObject(Session $object)
    {
        $recordDate   = (!$object->getRecordDate()) ? 'NULL' : $this->db->quote($object->getRecordDate());

        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName('#__vc_paymentsessions'))
            ->set($this->db->quoteName('user_id') . '=' . $this->db->quote($object->getUserId()))
            ->set($this->db->quoteName('item_id') . '=' . $this->db->quote($object->getItemId()))
            ->set($this->db->quoteName('item_type') . '=' . $this->db->quote($object->getItemType()))
            ->set($this->db->quoteName('items_number') . '=' . $this->db->quote($object->getItemsNumber()))
            ->set($this->db->quoteName('gateway') . '=' . $this->db->quote($object->getGateway()))
            ->set($this->db->quoteName('record_date') . '=' . $recordDate)
            ->set($this->db->quoteName('session_id') . '=' . $this->db->quote($object->getSessionId()));

        $this->db->setQuery($query);
        $this->db->execute();

        $object->setId($this->db->insertid());
    }

    public function updateObject(Session $object)
    {
        $recordDate   = (!$object->getRecordDate()) ? 'NULL' : $this->db->quote($object->getRecordDate());

        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName('#__vc_paymentsessions'))
            ->set($this->db->quoteName('user_id') . '=' . $this->db->quote($object->getUserId()))
            ->set($this->db->quoteName('item_id') . '=' . $this->db->quote($object->getItemId()))
            ->set($this->db->quoteName('item_type') . '=' . $this->db->quote($object->getItemType()))
            ->set($this->db->quoteName('items_number') . '=' . $this->db->quote($object->getItemsNumber()))
            ->set($this->db->quoteName('gateway') . '=' . $this->db->quote($object->getGateway()))
            ->set($this->db->quoteName('record_date') . '=' . $recordDate)
            ->set($this->db->quoteName('session_id') . '=' . $this->db->quote($object->getSessionId()))
            ->where($this->db->quoteName('id') . '=' . $this->db->quote($object->getId()));

        $this->db->setQuery($query);
        $this->db->execute();
    }

    public function deleteObject(Session $object)
    {
        // Delete gateway records.
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName('#__vc_paymentsessiongateways'))
            ->where($this->db->quoteName('id') .'='. (int)$object->getId());

        $this->db->setQuery($query);
        $this->db->execute();

        // Delete the session record.
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName('#__vc_paymentsessions'))
            ->where($this->db->quoteName('id') .'='. (int)$object->getId());

        $this->db->setQuery($query);
        $this->db->execute();

        $object->reset();
    }
}
