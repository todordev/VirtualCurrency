<?php
/**
 * @package      Virtualcurrency\User\Commodity
 * @subpackage   Command\Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\User\Commodity\Command\Gateway;

use Prism\Database\JoomlaDatabase;
use Virtualcurrency\User\Commodity\Commodity;

/**
 * Joomla database gateway.
 *
 * @package      Virtualcurrency\User\Commodity
 * @subpackage   Command\Gateway
 */
class JoomlaStoreNumber extends JoomlaDatabase implements StoreNumberGateway
{
    /**
     * Update the number of user commodities.
     *
     * @param Commodity $commodity
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     */
    public function store(Commodity $commodity)
    {
        if (!$commodity->getId()) {
            throw new \UnexpectedValueException('The commodity does not provide an ID.');
        }

        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__vc_usercommodities'))
            ->set($this->db->quoteName('number') . '=' . $this->db->quote($commodity->getNumber()))
            ->where($this->db->quoteName('id') . '=' . (int)$commodity->getId());

        $this->db->setQuery($query);
        $this->db->execute();
    }
}
