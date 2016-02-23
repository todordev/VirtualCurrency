<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Accounts
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Account;

use Prism\Database\Collection;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality
 * for managing user accounts.
 *
 * @package      Virtualcurrency
 * @subpackage   Accounts
 */
class Accounts extends Collection
{
    /**
     * Load the data for all user accounts by userId
     *
     * <code>
     * $options = array(
     *     'user_id' => $userId
     * );
     *
     * $accounts  = new Virtualcurrency\Account\Accounts(JFactory::getDbo());
     * $accounts->load($options);
     * </code>
     *
     * @param array $options
     */
    public function load(array $options = array())
    {
        $userId      = (array_key_exists('user_id', $options)) ? (int)$options['user_id'] : 0;
        $currencyIds = (array_key_exists('currency_ids', $options) and is_array($options['currency_ids'])) ? (array)$options['currency_ids'] : array();
        $state       = (array_key_exists('state', $options)) ? (int)$options['state'] : null;

        if ($userId > 0) {
            $query = $this->db->getQuery(true);

            $query
                ->select(
                    'a.id, a.amount, a.note, a.currency_id, a.user_id, ' .
                    'b.title, b.code, b.symbol, b.image, b.image_icon, ' .
                    'c.name'
                )
                ->from($this->db->quoteName('#__vc_accounts', 'a'))
                ->innerJoin($this->db->quoteName('#__vc_currencies', 'b') . ' ON a.currency_id = b.id')
                ->innerJoin($this->db->quoteName('#__users', 'c') . ' ON a.user_id = c.id')
                ->where('a.user_id = ' . (int)$userId);

            // Filter by account ID.
            if (count($currencyIds) > 0) {
                $query->where('a.currency_id IN (' . implode(',', $currencyIds) . ')');
            }

            // Filter by item state.
            if ($state !== null and is_numeric($state)) {
                $query->where('b.published = ' . (int)$state);
            }

            $this->db->setQuery($query);
            $this->items = (array)$this->db->loadAssocList();
        }
    }

    /**
     * Return account as object.
     *
     * <code>
     * $accountId = 1;
     *
     * $options = array(
     *     'user_id' => $userId
     * );
     *
     * $accounts = new Virtualcurrency\Account\Accounts(JFactory::getDbo());
     * $accounts->load($options);
     *
     * $account   = $accounts->getAccount($accountId);
     * </code>
     *
     * @param int|string $id
     *
     * @return Account|null
     */
    public function getAccount($id)
    {
        $account = null;

        foreach ($this->items as $item) {
            if ((int)$id === (int)$item['id']) {
                $account = new Account($this->db);
                $account->bind($item);
                break;
            }
        }

        return $account;
    }

    /**
     * Returns the data of accounts as object Account.
     *
     * <code>
     * $options = array(
     *    'state' => Prism\Constants::PUBLISHED
     * );
     *
     * $accounts  = new Virtualcurrency\Account\Accounts(JFactory::getDbo());
     * $accounts->load($options);
     *
     * $results = $accounts->getAccounts();
     * </code>
     *
     * @return array
     */
    public function getAccounts()
    {
        $results = array();

        $i = 0;
        foreach ($this->items as $item) {
            $results[$i] = new Account($this->db);
            $results[$i]->bind($item);
            $i++;
        }

        return $results;
    }
}
