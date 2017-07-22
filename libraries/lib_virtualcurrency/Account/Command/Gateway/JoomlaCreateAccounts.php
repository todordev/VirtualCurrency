<?php
/**
 * @package      Virtualcurrency\Account
 * @subpackage   Command\Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Account\Command\Gateway;

use Prism\Database\JoomlaDatabase;

/**
 * Joomla database gateway.
 *
 * @package      Virtualcurrency\Account
 * @subpackage   Command\Gateway
 */
class JoomlaCreateAccounts extends JoomlaDatabase implements CreateAccountsGateway
{
    /**
     * Create account records.
     *
     * @param int  $userId
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     */
    public function create($userId = 0)
    {
        // Check for a currency that is not assigned to an user account.
        $query = $this->db->getQuery(true);
        $query
            ->select('a.id, c.id as currency_id')
            ->from($this->db->quoteName('#__users', 'a'))
            ->join('', $this->db->quoteName('#__vc_currencies', 'c'))
            ->leftJoin($this->db->quoteName('#__vc_accounts', 'b') . ' ON (a.id = b.user_id AND c.id = b.currency_id)');

        if ($userId > 0) {
            $query->where('a.id = ' . (int)$userId);
        }

        $query->where('b.user_id IS NULL');

        $this->db->setQuery($query);
        $results = (array)$this->db->loadAssocList();

        // Create accounts for those currencies.
        $newAccounts = array();
        if (count($results) > 0) {
            foreach ($results as $result) {
                $newAccounts[] = (int)$result['id'] .','. (int)$result['currency_id'];
            }

            if (count($newAccounts) > 0) {
                $query = $this->db->getQuery(true);
                $query
                    ->insert($this->db->quoteName('#__vc_accounts'))
                    ->columns($this->db->quoteName(['user_id', 'currency_id']))
                    ->values($newAccounts);

                $this->db->setQuery($query);
                $this->db->execute();
            }
        }
    }
}
