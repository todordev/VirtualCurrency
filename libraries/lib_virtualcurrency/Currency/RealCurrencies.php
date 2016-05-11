<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Currencies
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Currency;

use Prism\Database\Collection;
use Joomla\Utilities\ArrayHelper;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that manage real currencies.
 *
 * @package      Virtualcurrency
 * @subpackage   Currencies
 */
class RealCurrencies extends Collection
{
    /**
     * Load currencies data by ID from database.
     *
     * <code>
     * $options = array(
     *      'ids' => array(1,2,3,4,5),
     *      'codes' => array('BGN', 'EUR', 'USD')
     * }
     *
     * $currencies   = new Virtualcurrency\Currency\Real\Currencies(JFactory::getDbo());
     * $currencies->load($options);
     *
     * foreach($currencies as $currency) {
     *   echo $currency["title"];
     *   echo $currency["code"];
     * }
     * </code>
     *
     * @param array $options
     */
    public function load(array $options = array())
    {
        // Load project data
        $query = $this->db->getQuery(true);

        $query
            ->select('a.id, a.title, a.code, a.symbol, a.position')
            ->from($this->db->quoteName('#__vc_realcurrencies', 'a'));

        $ids = (array_key_exists('ids', $options)) ? (array)$options['ids'] : array();
        if (count($ids) > 0) {
            $ids = ArrayHelper::toInteger($ids);
            $query->where('a.id IN ( ' . implode(',', $ids) . ' )');
        }

        $codes = (array_key_exists('codes', $options)) ? (array)$options['codes'] : array();
        if (count($codes) > 0) {
            $codes = array_map(function ($value) {
                return $this->db->quote($value);
            }, $codes);

            $query->where('a.code IN ( ' . implode(',', $codes) . ' )');
        }

        $this->db->setQuery($query);
        $this->items = (array)$this->db->loadAssocList();
    }

    /**
     * Return Currency object.
     * You can get the currency object by ID or by currency code.
     *
     * <code>
     * $ids = array(1,2,3,4,5);
     *
     * $currencies   = new Virtualcurrency\Currency\Real\Currencies(JFactory::getDbo());
     * $currencies->load($ids);
     *
     * $currencyId = 1;
     * $currency = $currencies->getCurrency($currencyId);
     *
     * $currencyCode = 'EUR';
     * $currency = $currencies->getCurrency($currencyCode);
     * </code>
     *
     * @param string|int $id Currency ID or Currency code.
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
     * Returns the real currencies as objects.
     *
     * <code>
     *  // The state could be 1 = published, 0 = unpublished, null = all
     *  $options = array(
     *      'state' => Prism\Constants::PUBLISHED
     *  );
     *
     * $currencies  = new Virtualcurrency\Currency\Real\Currencies(JFactory::getDbo());
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

        $i = 1;
        foreach ($this->items as $item) {
            $currency    = new Currency($this->db);
            $currency->bind($item);
            $results[$i] = $currency;
            $i++;
        }

        return $results;
    }
}
