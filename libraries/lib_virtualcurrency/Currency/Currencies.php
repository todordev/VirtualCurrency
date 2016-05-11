<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Currencies
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Currency;

use Prism\Constants;
use Prism\Database\Collection;
use Joomla\Utilities\ArrayHelper;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods used for managing a set of currencies.
 *
 * @package      Virtualcurrency
 * @subpackage   Currencies
 */
class Currencies extends Collection
{
    /**
     * Load currencies data.
     *
     * <code>
     * $currencyId = 1;
     *
     * $currencies = new Virtualcurrency\Currency\Currencies(JFactory::getDbo());
     * $currencies->load();
     * </code>
     *
     * @param array $options
     *
     */
    public function load(array $options = array())
    {
        $query = $this->db->getQuery(true);

        $query
            ->select('a.id, a.title, a.description, a.published, a.code, a.symbol, a.params, a.image, a.image_icon')
            ->from($this->db->quoteName('#__vc_currencies', 'a'));

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
     * Return a currency data, getting it by currency ID.
     *
     * <code>
     * $currencyId = 1;
     *
     * $currencies = new Virtualcurrency\Currency\Currencies(JFactory::getDbo());
     * $currencies->load();
     *
     * $currency   = $currencies->getCurrency($currencyId);
     * </code>
     *
     * @param int|string $id Currency ID or currency code.
     *
     * @return null|Currency
     */
    public function getCurrency($id)
    {
        $currency = null;

        if (is_numeric($id)) {
            $id = (int)$id;
            foreach ($this->items as $item) {
                if ($id === (int)$item['id']) {
                    $currency = new Currency($this->db);
                    $currency->bind($item);
                    break;
                }
            }
        } else {
            foreach ($this->items as $item) {
                if (strcmp($id, $item['code']) === 0) {
                    $currency = new Currency($this->db);
                    $currency->bind($item);
                    break;
                }
            }
        }

        return $currency;
    }

    /**
     * Returns the currencies as objects.
     *
     * <code>
     *  // The state could be 1 = published, 0 = unpublished, null = all
     *  $options = array(
     *      'state' => Prism\Constants::PUBLISHED
     *  );
     *
     * $currencies  = new Virtualcurrency\Currency\Currencies(JFactory::getDbo());
     * $currencies->load($options);
     *
     * $results = $commodities->getCurrencies();
     * </code>
     *
     * @return array
     */
    public function getCurrencies()
    {
        $results = array();

        $i = 0;
        foreach ($this->items as $item) {
            $currency = new Currency($this->db);
            $currency->bind($item);
            $results[$i] = $currency;
            $i++;
        }

        return $results;
    }
}
