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
use Joomla\Utilities\ArrayHelper;
use Prism\Database\LaravelDatabase;
use Virtualcurrency\Account\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Joomla database gateway.
 *
 * @package      Virtualcurrency\Account
 * @subpackage   Gateway
 */
class LaravelGateway extends LaravelDatabase implements AccountGateway
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
     * $gateway = new LaravelGateway();
     * $item    = $gateway->fetch($conditions);
     * </code>
     *
     * @param Request $request
     *
     * @return array
     */
    public function fetch(Request $request)
    {
        if (!$conditions) {
            throw new \UnexpectedValueException('There are no conditions that the system should use to fetch data.');
        }

        $query = $this->getQuery();
        $this->filter($query, $conditions);

        // Filter by conditions.
        foreach ($conditions as $key => $value) {
            $query->where('a.' . $key, '=', $value);
        }

        return (array)$query->first();
    }

    /**
     * Load the data from database and return a collection.
     *
     * <code>
     * $conditions = array(
     *     'ids' => array(1,2,3,4)
     * );
     *
     * $gateway = new LaravelGateway();
     * $items   = $dbGateway->fetchCollection($conditions);
     * </code>
     *
     * @param Request $request
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     *
     * @return array
     */
    public function fetchCollection(Request $request)
    {
        if (!$conditions) {
            throw new \UnexpectedValueException('There are no conditions that the system should use to fetch data.');
        }

        $query = $this->getQuery();
        $this->filter($query, $conditions);

        // Filter by other conditions.
        foreach ($conditions as $key => $value) {
            $query->where('a.' . $key, '=', $value);
        }

        $collection = $query->get();
        $collection = $collection->toArray();

        foreach ($collection as $key => $value) {
            $collection[$key] = (array)$value;
        }

        return (array)$collection;
    }

    /**
     * Fetch a data from database by item ID.
     *
     * <code>
     * $itemId = 1;
     *
     * $gateway = new LaravelGateway();
     * $items   = $gateway->fetchById($itemId);
     * </code>
     *
     * @param int $id
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
        $query->where('a.id', '=', (int)$id);

        return (array)$query->first();
    }

    /**
     * Prepare the query by query builder.
     *
     * @return QueryBuilder
     */
    protected function getQuery()
    {
        return DB::table('vc_accounts AS a')
            ->join('vc_currencies AS b', 'a.currency_id', '=', 'b.id')
            ->join('users AS c', 'a.user_id', '=', 'c.id')
            ->select(
                'a.id', 'a.amount', 'a.note', 'a.user_id', 'a.currency_id', 'a.published', 'a.created_at',
                'b.id AS c_id', 'b.title AS c_title', 'b.code AS c_code', 'b.symbol AS c_symbol', 'b.image AS c_image',
                'b.image_icon AS c_image_icon', 'b.params AS c_params',
                'c.name'
            );
    }

    public function insertObject(Account $object)
    {
        $note = $object->getNote() ?: 'NULL';

        $id = DB::table('vc_accounts')->insertGetId(
            [
                'amount'      => $object->getAmount(),
                'note'        => $note,
                'published'   => (int)$object->getPublished(),
                'user_id'     => (int)$object->getUserId(),
                'currency_id' => (int)$object->getCurrencyId(),
            ]
        );

        $object->setId($id);
    }

    public function updateObject(Account $object)
    {
        $note = $object->getNote() ?: 'NULL';

        DB::table('vc_accounts')
            ->where('id', '=', (int)$object->getId())
            ->update(
                [
                    'amount'      => $object->getAmount(),
                    'note'        => $note,
                    'published'   => (int)$object->getPublished(),
                    'user_id'     => (int)$object->getUserId(),
                    'currency_id' => (int)$object->getCurrencyId(),
                    'created_at'  => (int)$object->getCreatedAt(),
                ]
            );
    }

    /**
     * Prepare filters that will be used in the query.
     *
     * @param QueryBuilder $query
     * @param array        $conditions
     */
    protected function filter(QueryBuilder $query, array &$conditions)
    {
        // Filter by IDs
        if (array_key_exists('ids', $conditions) and is_array($conditions['ids'])) {
            $ids = ArrayHelper::toInteger($conditions['ids']);

            if (count($ids) > 0) {
                $query->whereIn('a.id', $ids);
            }

            unset($conditions['ids']);
        }

        // Filter by currency IDs
        if (array_key_exists('currency_id', $conditions) and is_array($conditions['currency_id'])) {
            $ids = ArrayHelper::toInteger($conditions['currency_id']);

            if (count($ids) > 0) {
                $query->whereIn('a.currency_id', $ids);
            }

            unset($conditions['currency_id']);
        }

        // Filter by state.
        if (array_key_exists('state', $conditions)) {
            $query->where('a.published', '=', (int)$conditions['state']);
            unset($conditions['state']);
        }

        $query->where('b.published', '=', Constants::PUBLISHED);
    }
}
