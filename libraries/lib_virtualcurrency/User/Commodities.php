<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Commodities
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\User;

use Prism\Constants;
use Prism\Database\Collection;
use Joomla\Utilities\ArrayHelper;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods used for managing a set of virtual goods.
 *
 * @package      Virtualcurrency
 * @subpackage   Commodities
 */
class Commodities extends Collection
{
    /**
     * Load commodities of an user.
     *
     * <code>
     *  // The state could be 1 = published, 0 = unpublished, null = all
     *  $options = array(
     *      'user_id' => 1, // Can be array with IDs
     *      'commodity_id' => 2 // Can be array with IDs
     *      'state' => Prism\Constants::PUBLISHED
     *  );
     *
     *  $commodities = new Virtualcurrency\User\Commodities();
     *  $commodities->setDb(JFactory::getDbo());
     *  $commodities->load($options);
     * </code>
     *
     * @param array $options
     */
    public function load(array $options = array())
    {
        $userId      = (!array_key_exists('user_id', $options)) ? 0 : $options['user_id'];
        $commodityId = (!array_key_exists('commodity_id', $options)) ? 0 : $options['commodity_id'];

        $query = $this->db->getQuery(true);

        $query
            ->select(
                'a.title, a.description, a.published, a.image, a.image_icon, ' .
                'b.id, b.commodity_id, b.user_id, b.number'
            )
            ->from($this->db->quoteName('#__vc_commodities', 'a'))
            ->rightJoin($this->db->quoteName('#__vc_usercommodities', 'b') . ' ON b.commodity_id = a.id');

        // Filter by user ID.
        if (is_numeric($userId) and (int)$userId > 0) {
            $query->where('b.user_id = ' . (int)$userId);
        } elseif (is_array($userId) and count($userId) > 0) {
            $userId = ArrayHelper::toInteger($userId);
            $query->where('b.user IN (' . implode(',', $userId) . ')');
        }

        // Filter by commodity ID.
        if (is_numeric($commodityId) and (int)$commodityId > 0) {
            $query->where('b.commodity_id = ' . (int)$commodityId);
        } elseif (is_array($commodityId) and count($commodityId) > 0) {
            $commodityId = ArrayHelper::toInteger($commodityId);
            $query->where('b.commodity_id IN (' . implode(',', $commodityId) . ')');
        }

        // Filter by state.
        $state = ArrayHelper::getValue($options, 'state');
        if ($state !== null) {
            $state = (!$state) ? Constants::UNPUBLISHED : Constants::PUBLISHED;
            $query->where('a.published = ' . (int)$state);
        }

        $this->db->setQuery($query);
        $this->items = (array)$this->db->loadAssocList();
    }

    /**
     * Return a commodity data by ID.
     *
     * <code>
     * $commodityId = 1;
     *
     * // The state could be 1 = published, 0 = unpublished, null = all
     * $options = array(
     *      'user_id' => 1, // Can be array with IDs
     *      'state' => Prism\Constants::PUBLISHED
     * );
     *
     * $commodities = new Virtualcurrency\Commodity\Commodities(JFactory::getDbo());
     * $commodities->load($options);
     *
     * $commodity   = $commodities->getCommodity($commodityId);
     * </code>
     *
     * @param int $id Commodity ID
     *
     * @return null|Commodity
     */
    public function getCommodity($id)
    {
        $commodity = null;

        foreach ($this->items as $item) {
            if ((int)$id === (int)$item['commodity_id']) {
                $commodity = new Commodity($this->db);
                $commodity->bind($item);
                break;
            }
        }

        return $commodity;
    }

    /**
     * Returns the data of user commodities as array that contains objects of User\Commodity.
     *
     * <code>
     *  // The state could be 1 = published, 0 = unpublished, null = all
     *  $options = array(
     *      'user_id' => 1, // Can be array with IDs
     *      'state' => Prism\Constants::PUBLISHED
     *  );
     *
     * $commodities  = new Virtualcurrency\User\Commodities(JFactory::getDbo());
     * $commodities->load($options);
     *
     * $results = $commodities->getCommodities();
     * </code>
     *
     * @return array
     */
    public function getCommodities()
    {
        $results = array();

        $i = 0;
        foreach ($this->items as $item) {
            $results[$i] = new Commodity($this->db);
            $results[$i]->bind($item);
            $i++;
        }

        return $results;
    }
}
