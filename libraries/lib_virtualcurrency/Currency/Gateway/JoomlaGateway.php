<?php
/**
 * @package      Virtualcurrency/Currency
 * @subpackage   Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Currency\Gateway;

use Joomla\Utilities\ArrayHelper;
use Prism\Database\JoomlaDatabase;

/**
 * Joomla database gateway.
 *
 * @package      Virtualcurrency/Currency
 * @subpackage   Gateway
 */
class JoomlaGateway extends JoomlaDatabase implements CurrencyGateway
{
    /**
     * Load the data from database by conditions and return an entity.
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

        // Filter by codes.
        if (array_key_exists('codes', $conditions) and is_array($conditions['codes']) and count($conditions['codes']) > 0) {
            $escapedCodes = array_map(function ($value) {
                return $this->db->quote($value);
            }, $conditions['codes']);

            $query->where($this->db->quoteName('a.code') .' IN ('. implode(',', $escapedCodes) .')');
            unset($conditions['codes']);
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
            ->select('a.id, a.title, a.description, a.code, a.symbol, a.position, a.image, a.image_icon, a.params, a.published')
            ->from($this->db->quoteName('#__vc_currencies', 'a'));

        return $query;
    }
}
