<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class VirtualcurrencyModelPayments extends JModelLegacy
{
    /**
     * @param \Joomla\Data\DataObject $cartSession
     * @param Joomla\Registry\Registry $params
     *
     * @return stdClass
     *
     * @throws UnexpectedValueException
     */
    public function prepareItem($cartSession, $params)
    {
        // Prepare amount formatter.
        $mapper       = new Virtualcurrency\RealCurrency\Mapper(new Virtualcurrency\RealCurrency\Gateway\JoomlaGateway(JFactory::getDbo()));
        $repository   = new Virtualcurrency\RealCurrency\Repository($mapper);
        $realCurrency = $repository->fetchById((int)$params->get('currency_id'));

        $formatter    = Virtualcurrency\Money\Helper::factory('joomla')->getFormatter();

        switch ($cartSession->item_type) {
            case 'currency':
                $currencyGateway  = new Virtualcurrency\Currency\Gateway\JoomlaGateway(JFactory::getDbo());
                $currencyPreparer = new Virtualcurrency\Cart\Command\CurrencyPreparation($formatter, $realCurrency, $currencyGateway);
                $item = $currencyPreparer->prepare(new Virtualcurrency\Cart\Session($cartSession));
                break;

            case 'commodity':
                $currencyGateway   = new Virtualcurrency\Currency\Gateway\JoomlaGateway(JFactory::getDbo());
                $commodityGateway  = new Virtualcurrency\Commodity\Gateway\JoomlaGateway(JFactory::getDbo());
                $commodityPreparer = new Virtualcurrency\Cart\Command\CommodityPreparation($formatter, $realCurrency, $currencyGateway, $commodityGateway);
                $item = $commodityPreparer->prepare(new Virtualcurrency\Cart\Session($cartSession));
                break;

            default:
                $item = null;
                break;
        }

        if ($item === null or !$item->id) {
            throw new UnexpectedValueException(JText::_('COM_VIRTUALCURRENCY_ERROR_INVALID_ITEM'));
        }

        return $item;
    }
}
