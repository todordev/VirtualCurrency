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

/**
 * Joomla database gateway.
 *
 * @package      Virtualcurrency\Commodity
 * @subpackage   Command\Gateway
 */
class JoomlaCreateCommodities extends JoomlaDatabase implements CreateCommoditiesGateway
{
    /**
     * Create commodity records.
     *
     * <code>
     * $conditions = array(
     *     'code'   => 'USD',
     *     'symbol' => '$'
     * );
     *
     * $gateway = new JoomlaGateway(\JFactory::getDbo());
     * $item    = $dbGateway->fetch($conditions);
     * </code>
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
            ->select('a.id, c.id as commodity_id')
            ->from($this->db->quoteName('#__users', 'a'))
            ->join('', $this->db->quoteName('#__vc_commodities', 'c'))
            ->leftJoin($this->db->quoteName('#__vc_usercommodities', 'b') . ' ON (a.id = b.user_id AND c.id = b.commodity_id)');

        if ($userId) {
            $query->where('a.id = ' . (int)$userId);
        }

        $query->where('b.user_id IS NULL');

        $this->db->setQuery($query);
        $results = (array)$this->db->loadAssocList();

        // Create accounts for those currencies.
        $newCommodities = array();
        if (count($results) > 0) {
            foreach ($results as $result) {
                $newCommodities[] = (int)$result['id'] .','. (int)$result['commodity_id'];
            }

            if (count($newCommodities) > 0) {
                $query = $this->db->getQuery(true);
                $query
                    ->insert($this->db->quoteName('#__vc_usercommodities'))
                    ->columns($this->db->quoteName(['user_id', 'commodity_id']))
                    ->values($newCommodities);

                $this->db->setQuery($query);
                $this->db->execute();
            }
        }
    }
}
