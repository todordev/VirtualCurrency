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
use Prism\Database\Request\Request;
use Prism\Database\Joomla\FetchMethods;
use Virtualcurrency\Commodity\Commodity;
use Prism\Database\JoomlaDatabaseGateway;
use Prism\Database\Joomla\FetchCollectionMethod;

/**
 * Joomla database gateway.
 *
 * @package      Virtualcurrency/Commodity
 * @subpackage   Gateway
 */
class JoomlaGateway extends JoomlaDatabaseGateway implements CommodityGateway
{
    use FetchMethods, FetchCollectionMethod;

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
     * Prepare the query by query builder.
     *
     * @param Request $request
     * @return \JDatabaseQuery
     *
     * @throws \RuntimeException
     */
    protected function getQuery(Request $request = null)
    {
        $defaultFields  = ['a.id', 'a.title', 'a.description', 'a.in_stock', 'a.published', 'a.image', 'a.image_icon', 'a.params'];
        $fields  = $this->prepareFields($request, $defaultFields);

        // If there are no fields, use default ones.
        if (count($fields) === 0) {
            $fields = $defaultFields;
            unset($defaultFields);
        }

        $query = $this->db->getQuery(true);
        $query
            ->select($fields)
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

    /**
     * Prepare some query filters.
     *
     * @param \JDatabaseQuery $query
     * @param Request         $request
     *
     * @throws \InvalidArgumentException
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
