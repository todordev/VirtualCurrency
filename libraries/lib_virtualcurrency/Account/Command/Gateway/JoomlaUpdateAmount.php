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
use Virtualcurrency\Account\Account;

/**
 * Joomla database gateway.
 *
 * @package      Virtualcurrency\Account
 * @subpackage   Command\Gateway
 */
class JoomlaUpdateAmount extends JoomlaDatabase implements UpdateAmountGateway
{
    /**
     * Update account amount.
     *
     * @param Account  $account
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     */
    public function update(Account $account)
    {
        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__vc_accounts'))
            ->set($this->db->quoteName('amount') . '=' . $this->db->quote($account->getAmount()))
            ->where($this->db->quoteName('id') . '=' . (int)$account->getId());

        $this->db->setQuery($query);
        $this->db->execute();
    }
}
