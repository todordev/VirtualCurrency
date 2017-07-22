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
class JoomlaCreateAccount extends JoomlaDatabase implements CreateAccountGateway
{
    /**
     * Create an account record.
     *
     * @param int  $userId
     * @param int  $currencyId
     * @param boolean  $force
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     */
    public function create($userId, $currencyId, $force = false)
    {
        $result = 0;
        if (!$force) {
            // Check for a currency that is not assigned to an user account.
            $query = $this->db->getQuery(true);
            $query
                ->select('COUNT(*)')
                ->from($this->db->quoteName('#__vc_accounts', 'a'))
                ->where('a.user_id = ' . (int)$userId)
                ->where('a.currenyc_id = ' . (int)$currencyId);

            $this->db->setQuery($query);
            $result = (int)$this->db->loadResult();
        }

        // Create accounts for those currencies.
        if ($result === 0) {
            $query = $this->db->getQuery(true);

            $query
                ->insert($this->db->quoteName('#__vc_accounts'))
                ->set($this->db->quoteName('user_id') .'='. (int)$userId)
                ->set($this->db->quoteName('user_id') .'='. (int)$currencyId);

            $this->db->setQuery($query);
            $this->db->execute();
        }
    }
}
