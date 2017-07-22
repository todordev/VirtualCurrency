<?php
/**
 * @package      Virtualcurrency/Commodity
 * @subpackage   Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Commodity\Gateway;

use Joomla\Utilities\ArrayHelper;
use Prism\Database\JoomlaDatabase;
use Virtualcurrency\Commodity\Commodity;

/**
 * Joomla database gateway.
 *
 * @package      Virtualcurrency/Commodity
 * @subpackage   Gateway
 */
class JoomlaGateway extends JoomlaDatabase implements CommodityGateway
{
    /**
     * Load the data from database by conditions and return an entity.
     *
     * <code>
     * $conditions = array(
     *     'id'   => 1,
     *     'published' => Prism\Constants::PUBLISHED
     * );
     *
     * $gateway = new JoomlaGateway(\JFactory::getDbo());
     * $item    = $dbGateway->fetch($conditions);
     * </code>
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
     * Load all data from database.
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     *
     * @return array
     */
    public function fetchAll()
    {
        $query = $this->getQuery();

        $this->db->setQuery($query);

        return (array)$this->db->loadAssocList();
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
            ->select('a.id, a.title, a.description, a.in_stock, a.published, a.image, a.image_icon, a.params')
            ->from($this->db->quoteName('#__vc_commodities', 'a'));

        return $query;
    }

    public function insertObject(Commodity $object)
    {
        $description = !$object->getDescription() ? 'NULL' : $this->db->quote($object->getDescription());
        $params      = !$object->getParams() ? 'NULL' : $this->db->quote(json_encode($object->getParams()));

        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName('#__vc_commodities'))
            ->set($this->db->quoteName('title') .'='. $this->db->quote($object->getTitle()))
            ->set($this->db->quoteName('description') .'='. $description)
            ->set($this->db->quoteName('in_stock') .'='. (int)$object->getInStock())
            ->set($this->db->quoteName('params') .'='. $params);

        $this->db->setQuery($query);
        $this->db->execute();

        $object->setId($this->db->insertid());
    }

    public function updateObject(Commodity $object)
    {
        $description = !$object->getDescription() ? 'NULL' : $this->db->quote($object->getDescription());
        $params      = !$object->getParams() ? 'NULL' : $this->db->quote(json_encode($object->getParams()));

        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__vc_commodities'))
            ->set($this->db->quoteName('title') .'='. $this->db->quote($object->getTitle()))
            ->set($this->db->quoteName('description') .'='. $description)
            ->set($this->db->quoteName('in_stock') .'='. (int)$object->getInStock())
            ->set($this->db->quoteName('params') .'='. $params)
            ->where($this->db->quoteName('id') . '=' . (int)$object->getId());

        $this->db->setQuery($query);
        $this->db->execute();
    }
}
