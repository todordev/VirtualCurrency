<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Commodities
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Commodity;

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
     * Load commodities data.
     *
     * <code>
     *  // The state could be 1 = published, 0 = unpublished, null = all
     *  $options = array(
     *      "state" => Prism\Constants::PUBLISHED
     *  );
     *
     *  $commodities = new Virtualcurrency\Commodity\Commodities();
     *  $commodities->setDb(JFactory::getDbo());
     *  $commodities->load($options);
     * </code>
     *
     * @param array $options
     *
     */
    public function load(array $options = array())
    {
        $query = $this->db->getQuery(true);

        $query
            ->select(
                'a.id, a.title, a.number, a.price, a.price_virtual, ' .
                'a.currency_id, a.minimum, a.published, a.image, a.image_icon'
            )
            ->from($this->db->quoteName('#__vc_commodities', 'a'));

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
     * $commodities = new Virtualcurrency\Commodity\Commodities(JFactory::getDbo());
     * $commodities->load();
     *
     * $commodity   = $commodities->getCommodity($commodityId);
     * </code>
     *
     * @param int|string $id
     *
     * @return Commodity|null
     */
    public function getCommodity($id)
    {
        $commodity = null;

        foreach ($this->items as $item) {
            if ((int)$id === (int)$item['id']) {
                $commodity = new Commodity($this->db);
                $commodity->bind($item);
                break;
            }
        }

        return $commodity;
    }

    /**
     * Returns the data of commodities as object.
     *
     * <code>
     * $options = array(
     *    'state' => Prism\Constants::PUBLISHED
     * )
     *
     * $commodity  = new Virtualcurrency\Commodity\Commodities(JFactory::getDbo());
     * $commodity->load($options);
     *
     * $results = $commodity->getCommodities();
     * </code>
     *
     * @return array
     */
    public function getCommodities()
    {
        $results = array();

        $i = 1;
        foreach ($this->items as $item) {
            $results[$i] = new Commodity($this->db);
            $results[$i]->bind($item);
            $i++;
        }

        return $results;
    }
}
