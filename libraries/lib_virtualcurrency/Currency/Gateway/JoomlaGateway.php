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
use Prism\Database\JoomlaDatabaseGateway;
use Prism\Database\Request\Request;
use Prism\Database\Joomla\FetchMethods;
use Prism\Database\Joomla\FetchCollectionMethod;

/**
 * Joomla database gateway.
 *
 * @package      Virtualcurrency/Currency
 * @subpackage   Gateway
 */
class JoomlaGateway extends JoomlaDatabaseGateway implements CurrencyGateway
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
     *
     * @return \JDatabaseQuery
     *
     * @throws \RuntimeException
     */
    protected function getQuery(Request $request = null)
    {
        $defaultFields  = ['a.id', 'a.title', 'a.description', 'a.code', 'a.symbol', 'a.position', 'a.image', 'a.image_icon', 'a.params', 'a.published'];
        $fields         = $this->prepareFields($request, $defaultFields);

        // If there are no fields, use default ones.
        if (count($fields) === 0) {
            $fields = $defaultFields;
            unset($defaultFields);
        }

        $query = $this->db->getQuery(true);
        $query
            ->select($fields)
            ->from($this->db->quoteName('#__vc_currencies', 'a'));

        return $query;
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

        // Filter by IDs
        if ($conditions->getSpecificCondition('codes')) {
            $condition = $conditions->getSpecificCondition('codes');
            $codes     = $condition->getValue();

            if (is_array($codes) && count($codes) > 0) {
                $escapedCodes = array_map(function ($value) {
                    return $this->db->quote($value);
                }, $codes);

                $query->where($this->db->quoteName('a.code') .' IN ('. implode(',', $escapedCodes) .')');
            }
        }

        // Filter by standard conditions.
        parent::filter($query, $request);
    }
}
