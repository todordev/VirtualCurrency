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
use Prism\Database\Request\Request;
use Prism\Database\Joomla\FetchMethods;
use Prism\Database\JoomlaDatabaseGateway;
use Virtualcurrency\User\Commodity\Commodity;
use Prism\Database\Joomla\FetchCollectionMethod;

/**
 * Joomla database gateway.
 *
 * @package      Virtualcurrency\Commodity
 * @subpackage   Gateway
 */
class JoomlaGateway extends JoomlaDatabaseGateway implements CommodityGateway
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
            'a.id', 'a.commodity_id', 'a.user_id', 'a.number',
            'b.id AS c_id', 'b.title AS c_title', 'b.description AS c_description', 'b.published AS c_published', 'b.in_stock AS c_in_stock',
            'b.image AS c_image', 'b.image_icon AS c_image_icon', 'b.params AS c_params'
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

        // Filter by IDs
        if ($conditions->getSpecificCondition('commodity_ids')) {
            $condition = $conditions->getSpecificCondition('commodity_ids');

            if (is_array($condition->getValue())) {
                $ids = ArrayHelper::toInteger($condition->getValue());
                $ids = array_filter(array_unique($ids));

                if (count($ids) > 0) {
                    $query->where($this->db->quoteName('a.commodity_id') . ' IN (' . implode(',', $ids) . ')');
                }
            }
        }

        // Filter by standard conditions.
        parent::filter($query, $request);
    }
}
