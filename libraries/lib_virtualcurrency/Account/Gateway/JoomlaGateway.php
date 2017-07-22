<?php
/**
 * @package      Virtualcurrency\Account
 * @subpackage   Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Account\Gateway;

use Prism\Constants;
use Prism\Database\JoomlaDatabase;
use Joomla\Utilities\ArrayHelper;
use Virtualcurrency\Account\Account;

/**
 * Joomla database gateway.
 *
 * @package      Virtualcurrency\Account
 * @subpackage   Gateway
 */
class JoomlaGateway extends JoomlaDatabase implements AccountGateway
{
    /**
     * Load the data from database by conditions and return an entity.
     *
     * <code>
     * $conditions = array(
     *     'user_id'   => 1,
     *     'currency_id' => 2
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
        $this->filter($query, $conditions);

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
        $this->filter($query, $conditions);

        // Filter by other conditions.
        foreach ($conditions as $key => $value) {
            $query->where($this->db->quoteName('a.'.$key) .'='. $this->db->quote($value));
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
                'a.id, a.amount, a.note, a.user_id, a.currency_id, a.published, a.created_at, ' .
                'b.id AS c_id, b.title AS c_title, b.code AS c_code, b.symbol AS c_symbol, b.image AS c_image, b.image_icon AS c_image_icon, b.params AS c_params, ' .
                'c.name'
            )
            ->from($this->db->quoteName('#__vc_accounts', 'a'))
            ->innerJoin($this->db->quoteName('#__vc_currencies', 'b') . ' ON a.currency_id = b.id')
            ->innerJoin($this->db->quoteName('#__users', 'c') . ' ON a.user_id = c.id');

        return $query;
    }

    public function insertObject(Account $object)
    {
        $note   = !$object->getNote() ? 'NULL' : $this->db->quote($object->getNote());

        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName('#__vc_accounts'))
            ->set($this->db->quoteName('amount') . '=' . $this->db->quote($object->getAmount()))
            ->set($this->db->quoteName('note') . '=' . $note)
            ->set($this->db->quoteName('published') . '=' . (int)$object->getPublished())
            ->set($this->db->quoteName('user_id') . '=' . (int)$object->getUserId())
            ->set($this->db->quoteName('currency_id') . '=' . (int)$object->getCurrencyId());

        $this->db->setQuery($query);
        $this->db->execute();

        $object->setId($this->db->insertid());
    }

    public function updateObject(Account $object)
    {
        $note   = (!$object->getNote()) ? 'NULL' : $this->db->quote($object->getNote());

        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__vc_accounts'))
            ->set($this->db->quoteName('amount') . '=' . $this->db->quote($object->getAmount()))
            ->set($this->db->quoteName('note') . '=' . $note)
            ->set($this->db->quoteName('published') . '=' . (int)$object->getPublished())
            ->set($this->db->quoteName('user_id') . '=' . (int)$object->getUserId())
            ->set($this->db->quoteName('currency_id') . '=' . (int)$object->getCurrencyId())
            ->set($this->db->quoteName('created_at') . '=' . $this->db->quote($object->getCreatedAt()))
            ->where($this->db->quoteName('id') . '=' . (int)$object->getId());

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * @param \JDatabaseQuery $query
     * @param array $conditions
     */
    protected function filter(\JDatabaseQuery $query, array &$conditions)
    {
        // Filter by IDs
        if (array_key_exists('ids', $conditions) and is_array($conditions['ids'])) {
            $ids = ArrayHelper::toInteger($conditions['ids']);

            if (count($ids) > 0) {
                $query->where($this->db->quoteName('a.id') .' IN ('. implode(',', $ids) .')');
            }

            unset($conditions['ids']);
        }

        // Filter by currency IDs
        if (array_key_exists('currency_ids', $conditions) and is_array($conditions['currency_ids'])) {
            $ids = ArrayHelper::toInteger($conditions['currency_ids']);

            if (count($ids) > 0) {
                $query->where($this->db->quoteName('a.currency_id') .' IN ('. implode(',', $ids) .')');
            }

            unset($conditions['currency_ids']);
        }

        // Filter by state.
        if (array_key_exists('state', $conditions)) {
            $query->where($this->db->quoteName('a.published') .'='. (int)$conditions['state']);
            unset($conditions['state']);
        }

        $query->where($this->db->quoteName('b.published') .'='. Constants::PUBLISHED);
    }
}
