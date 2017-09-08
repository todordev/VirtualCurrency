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
use Prism\Database\JoomlaDatabaseGateway;
use Joomla\Utilities\ArrayHelper;
use Virtualcurrency\Account\Account;
use Prism\Database\Request\Request;
use Prism\Database\Joomla\FetchMethods;
use Prism\Database\Joomla\FetchCollectionMethod;

/**
 * Joomla database gateway.
 *
 * @package      Virtualcurrency\Account
 * @subpackage   Gateway
 */
class JoomlaGateway extends JoomlaDatabaseGateway implements AccountGateway
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
            'a.id', 'a.amount', 'a.note', 'a.user_id', 'a.currency_id', 'a.published', 'a.created_at',
            'b.id AS c_id', 'b.title AS c_title', 'b.code AS c_code', 'b.symbol AS c_symbol', 'b.image AS c_image',
            'b.image_icon AS c_image_icon', 'b.params AS c_params',
            'c.name',
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

        // Filter by currency IDs.
        if ($conditions->getSpecificCondition('currency_ids')) {
            $condition   = $conditions->getSpecificCondition('currency_ids');
            $currencyIds = $condition->getValue();

            if (is_array($currencyIds) && count($currencyIds) > 0) {
                $currencyIds = ArrayHelper::toInteger($currencyIds);
                $currencyIds = array_filter(array_unique($currencyIds));

                $query->where($this->db->quoteName('a.currency_id') .' IN ('. implode(',', $currencyIds) .')');
            }
        }

        // Filter by standard conditions.
        parent::filter($query, $request);

        $query->where($this->db->quoteName('b.published') .'='. Constants::PUBLISHED);
    }
}
