<?php
/**
 * @package      Virtualcurrency\Cart
 * @subpackage   Command
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Cart\Command;

use Joomla\Utilities\ArrayHelper;
use Prism\Domain\BindException;
use Prism\Money\Currency;
use Prism\Money\Formatter;
use Prism\Money\Money;
use Prism\Utilities\MathHelper;
use Virtualcurrency\Cart\Item;
use Virtualcurrency\Cart\Session;
use Virtualcurrency\Commodity\Gateway\CommodityGateway;
use Virtualcurrency\Commodity\Mapper as CommodityMapper;
use Virtualcurrency\Commodity\Repository as CommodityRepository;
use Virtualcurrency\Currency\Gateway\CurrencyGateway;
use Virtualcurrency\Currency\Mapper as VirtualCurrencyMapper;
use Virtualcurrency\Currency\Repository as VirtualCurrencyRepository;
use Virtualcurrency\RealCurrency\Currency as RealCurrency;

/**
 * This class provides functionality to prepare cart item.
 *
 * @package      Virtualcurrency\Cart
 * @subpackage   Command
 */
class CommodityPreparation implements ItemPreparer
{
    /**
     * @var Formatter
     */
    protected $formatter;

    /**
     * @var CurrencyGateway
     */
    protected $gateway;

    /**
     * @var CommodityGateway
     */
    protected $commodityGateway;

    /**
     * @var RealCurrency
     */
    protected $realCurrency;

    public function __construct(Formatter $formatter, RealCurrency $realCurrency, CurrencyGateway $gateway, CommodityGateway $commodityGateway)
    {
        $this->formatter        = $formatter;
        $this->realCurrency     = $realCurrency;
        $this->gateway          = $gateway;
        $this->commodityGateway = $commodityGateway;
    }

    /**
     * Prepare and return an object that will be used in cart views.
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \OutOfBoundsException
     * @throws BindException
     *
     * @param Session $cartSession
     *
     * @return \stdClass
     */
    public function prepare(Session $cartSession)
    {
        $repository = new CommodityRepository(new CommodityMapper($this->commodityGateway));
        $unit       = $repository->fetchById($cartSession->getItemId());

        if (!$unit->getId()) {
            return null;
        }

        $itemPrice        = (float)$unit->getParam('price_real');
        $itemPriceVirtual = (float)$unit->getParam('price_virtual');
        $currencyId       = (int)$unit->getParam('currency_id');

        $currency = new Currency($unit->getProperties());
        $money    = new Money($cartSession->getItemsNumber(), $currency);
        $amountFormatted = $this->formatter->format($money) . ' ' . $unit->getTitle();

        $totalCostFormatted         = '';
        $totalCostVirtualFormatted  = '';
        $currencyType               = '';

        $orderItem = new Item($cartSession->getItemType(), $cartSession->getItemsNumber());

        // Get real currency
        if ($itemPrice) {
            // Calculate total amount that should be paid in real currency.
            $totalCost = (string)MathHelper::calculateTotal(array(
                $cartSession->getItemsNumber(),
                $itemPrice
            ));

            $orderItem->price('real')->setPrice($itemPrice);
            $orderItem->price('real')->setTotal($totalCost);
            $orderItem->price('real')->setCurrencyCode($this->realCurrency->getCode());

            $currency           = new Currency($this->realCurrency->getProperties());
            $money              = new Money($totalCost, $currency);
            $totalCostFormatted = $this->formatter->formatCurrency($money);

            $currencyType = 'real';
        }

        // Get virtual currency
        if ($itemPriceVirtual) {
            $mapper             = new VirtualCurrencyMapper($this->gateway);
            $repository         = new VirtualCurrencyRepository($mapper);
            $virtualCurrency    = $repository->fetchById($currencyId);

            // Calculate total amount that should be paid.
            $totalCostVirtual = (string)MathHelper::calculateTotal(array(
                $cartSession->getItemsNumber(),
                $itemPriceVirtual
            ));

            $orderItem->price('virtual')->setPrice($itemPriceVirtual);
            $orderItem->price('virtual')->setTotal($totalCostVirtual);
            $orderItem->price('virtual')->setCurrencyId($virtualCurrency->getId());
            $orderItem->price('virtual')->setCurrencyCode($virtualCurrency->getCode());

            $currency = new Currency($virtualCurrency->getProperties());
            $money    = new Money($totalCostVirtual, $currency);
            $totalCostVirtualFormatted = $this->formatter->formatCurrency($money);

            $currencyType = 'virtual';
        }

        $orderItem->setItemsNumberFormatted($amountFormatted);
        $orderItem->price('real')->setTotalFormatted($totalCostFormatted);
        $orderItem->price('virtual')->setTotalFormatted($totalCostVirtualFormatted);

        // Check for possibility to buy virtual goods by real and virtual currencies.
        if ($itemPrice and $itemPriceVirtual) {
            $orderItem->setCurrencyType('both');
        } else {
            $orderItem->setCurrencyType($currencyType);
        }

        $item = $unit->getProperties();
        $item = ArrayHelper::toObject($item);

        $item->order = $orderItem;

        return $item;
    }
}
