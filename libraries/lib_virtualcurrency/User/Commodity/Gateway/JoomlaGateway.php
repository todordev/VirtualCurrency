<?php
/**
 * @package      Virtualcurrency\Commodity
 * @subpackage   Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\User\Commodity\Gateway;

use Joomla\Utilities\ArrayHelper;
use Prism\Database\JoomlaDatabase;
use Virtualcurrency\User\Commodity\Commodity;

/**
 * Joomla database gateway.
 *
 * @package      Virtualcurrency\Commodity
 * @subpackage   Gateway
 */
class JoomlaGateway extends JoomlaDatabase implements CommodityGateway
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
     * Load the data from database and return a collection.
     *
     * @param array $conditions
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
                $query->where($this->db->quoteName('a.id') . ' IN (' . implode(',', $ids) . ')');
            }

            unset($conditions['ids']);
        }

        // Filter by commodity IDs.
        if (array_key_exists('commodity_ids', $conditions) and is_array($conditions['commodity_ids'])) {
            $ids = ArrayHelper::toInteger($conditions['commodity_ids']);

            if (count($ids) > 0) {
                $query->where($this->db->quoteName('a.commodity_id') . ' IN (' . implode(',', $ids) . ')');
            }

            unset($conditions['commodity_ids']);
        }

        // Filter by state.
        if (array_key_exists('state', $conditions)) {
            $query->where($this->db->quoteName('b.published') .'='. $conditions['state']);
            unset($conditions['state']);
        }

        // Filter by other conditions.
        foreach ($conditions as $key => $value) {
            $query->where($this->db->quoteName('a.' . $key) . '=' . $this->db->quote($value));
        }

        $this->db->setQuery($query);

        return (array)$this->db->loadAssocList();
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
                'a.id, a.commodity_id, a.user_id, a.number, ' .
                'b.id AS c_id, b.title AS c_title, b.description AS c_description, b.published AS c_published, b.in_stock AS c_in_stock, b.image AS c_image, b.image_icon AS c_image_icon, b.params AS c_params'
            )
            ->from($this->db->quoteName('#__vc_usercommodities', 'a'))
            ->rightJoin($this->db->quoteName('#__vc_commodities', 'b') . ' ON a.commodity_id = b.id');

        return $query;
    }

    public function insertObject(Commodity $object)
    {
        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName('#__vc_usercommodities'))
            ->set($this->db->quoteName('user_id') . '=' . (int)$object->getUserId())
            ->set($this->db->quoteName('commodity_id') . '=' . (int)$object->getCommodityId())
            ->set($this->db->quoteName('number') . '=' . (int)$object->getNumber());

        $this->db->setQuery($query);
        $this->db->execute();

        $object->setId($this->db->insertid());
    }

    public function updateObject(Commodity $object)
    {
        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__vc_usercommodities'))
            ->set($this->db->quoteName('user_id') .'='. (int)$object->getUserId())
            ->set($this->db->quoteName('commodity_id') .'='. (int)$object->getCommodityId())
            ->set($this->db->quoteName('number') .'='. (int)$object->getNumber())
            ->where($this->db->quoteName('id') .'='. (int)$object->getId());

        $this->db->setQuery($query);
        $this->db->execute();
    }
}
