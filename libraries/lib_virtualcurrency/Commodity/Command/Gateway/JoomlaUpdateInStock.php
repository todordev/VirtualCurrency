<?php
/**
 * @package      Virtualcurrency\Commodity
 * @subpackage   Command\Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Commodity\Command\Gateway;

use Prism\Database\JoomlaDatabase;
use Virtualcurrency\Commodity\Commodity;

/**
 * Joomla database gateway.
 *
 * @package      Virtualcurrency\Commodity
 * @subpackage   Command\Gateway
 */
class JoomlaUpdateInStock extends JoomlaDatabase implements UpdateInStockGateway
{
    /**
     * Update the number of commodities in stock.
     *
     * @param Commodity $commodity
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     */
    public function update(Commodity $commodity)
    {
        if (!$commodity->getId()) {
            throw new \UnexpectedValueException('The commodity does not provide an ID.');
        }

        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__vc_commodities'))
            ->set($this->db->quoteName('in_stock') .'='. (int)$commodity->getInStock())
            ->where($this->db->quoteName('id') .'='. (int)$commodity->getId());

        $this->db->setQuery($query);
        $this->db->execute();
    }
}
