<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Currencies
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Currency;

use Prism\Database;
use Prism\Money\CurrencyInterface;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing currency.
 *
 * @package      Virtualcurrency
 * @subpackage   Currencies
 */
class RealCurrency extends Database\TableImmutable implements CurrencyInterface
{
    protected $id;
    protected $title;
    protected $code;
    protected $symbol;
    protected $position;

    /**
     * Load currency data from database by ID.
     *
     * <code>
     * $keys = array(
     *     "id" => 1,
     *     "code" => "EUR"
     * );
     *
     * $currency   = new Virtualcurrency\Currency\RealCurrency(\JFactory::getDbo());
     * $currency->load($keys);
     * </code>
     *
     * @param int|array $keys
     * @param array $options
     */
    public function load($keys, array $options = array())
    {
        $query = $this->db->getQuery(true);
        $query
            ->select('a.id, a.title, a.code, a.symbol, a.position')
            ->from($this->db->quoteName('#__vc_realcurrencies', 'a'));

        if (!is_array($keys)) {
            $query->where('a.id = ' . (int)$keys);
        } else {
            foreach ($keys as $key => $value) {
                $query->where($this->db->quoteName('a.'.$key) .' = ' . $this->db->quote($value));
            }
        }

        $this->db->setQuery($query);
        $result = (array)$this->db->loadAssoc();

        $this->bind($result);
    }

    /**
     * Return currency ID.
     *
     * <code>
     * $currencyId  = 1;
     *
     * $currency    = new Virtualcurrency\Currency\RealCurrency(\JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * if (!$currency->getId()) {
     * ....
     * }
     * </code>
     *
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * Return the title of the real currency.
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency    = new Virtualcurrency\Currency\RealCurrency(JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * $title = $currency->getTitle();
     * </code>
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Return currency code (abbreviation).
     *
     * <code>
     * $currencyId  = 1;
     *
     * $currency    = new Virtualcurrency\Currency\RealCurrency(\JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * // Return GBP
     * $code = $currency->getCode();
     * </code>
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Return currency symbol.
     *
     * <code>
     * $currencyId  = 1;
     *
     * $currency    = new Virtualcurrency\Currency\RealCurrency(\JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * // Return £
     * $symbol = $currency->getSymbol();
     * </code>
     *
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * Return the position of currency symbol.
     *
     * <code>
     * $currencyId  = 1;
     *
     * $currency    = new Virtualcurrency\Currency\RealCurrency(\JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * // Return 0 = beginning; 1 = end;
     * if (0 == $currency->getPosition()) {
     * ...
     * }
     * </code>
     *
     * @return int
     */
    public function getPosition()
    {
        return (int)$this->position;
    }
}
