<?php
/**
 * @package      Virtualcurrency\Money
 * @subpackage   Helper
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Money\Helper;

use Prism\Container;
use Prism\Domain\BindException;
use Prism\Money\Formatter\IntlDecimalFormatter;
use Prism\Money\Parser\IntlDecimalParser;
use Virtualcurrency\RealCurrency\Currency;
use Virtualcurrency\RealCurrency\Mapper;
use Virtualcurrency\RealCurrency\Repository;
use Virtualcurrency\RealCurrency\Gateway\JoomlaGateway;
use Virtualcurrency\Constants;
use Prism\Utilities\StringHelper;
use Joomla\Registry\Registry;

/**
 * This class provides functionality to prepare money data.
 *
 * @package      Virtualcurrency\Money
 * @subpackage   Helper
 */
class Joomla extends MoneyHelper
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Registry
     */
    private $params;

    /**
     * Helper constructor.
     */
    public function __construct()
    {
        $this->container = Container::getContainer();
        $this->params    = \JComponentHelper::getParams('com_virtualcurrency');
    }

    /**
     * Return a currency object. It tries to get it from the container.
     * If the object does not exist in the container, it will create an object and will add it to the container.
     *
     * <code>
     * $currencyId  =   1;
     *
     * $helper     = new Helper();
     * $formatter  = $moneyHelper->getFormatter();
     *
     * $currency   = $this->getCurrency($currencyId);
     * </code>
     *
     * @param int $currencyId
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \OutOfBoundsException
     * @throws BindException
     *
     * @return Currency
     */
    public function getCurrency($currencyId = 0)
    {
        $currencyId    = $currencyId ? (int)$currencyId : (int)$this->params->get('currency_id');
        $currencyHash  = StringHelper::generateMd5Hash(Constants::CONTAINER_REAL_CURRENCY, $currencyId);

        if (!$this->container->exists($currencyHash)) {
            $repository = new Repository(new Mapper(new JoomlaGateway(\JFactory::getDbo())));
            $currency   = $repository->fetchById($this->params->get('currency_id'));

            $this->container->set($currencyHash, $currency);
        } else {
            $currency = $this->container->get($currencyHash);
        }

        return $currency;
    }

    /**
     * Return money formatter object. It tries to get it from the container.
     * If the object does not exist in the container, it will create an object and will add it to the container.
     *
     * <code>
     * $locale  =   'bg-BG';
     * $digits  =   2;
     *
     * $helper     = new Helper($container);
     * $formatter  = $moneyHelper->getFormatter();
     * </code>
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \OutOfBoundsException
     *
     * @return IntlDecimalFormatter
     */
    public function getFormatter()
    {
        $language   = \JFactory::getLanguage();
        $locale     = $language->getTag();

        $formatterHash  = StringHelper::generateMd5Hash(Constants::CONTAINER_FORMATTER_MONEY, $locale);

        if (!$this->container->exists($formatterHash)) {
            $numberFormatter = $this->prepareNumberFormatter($locale, (int)$this->params->get('fraction_digits', 2));

            $formatter  = new IntlDecimalFormatter($numberFormatter);

            $this->container->set($formatterHash, $formatter);
        } else {
            $formatter = $this->container->get($formatterHash);
        }

        return $formatter;
    }

    /**
     * Return money parser object. It tries to get it from the container.
     * If the object does not exist in the container, it will create an object and will add it to the container.
     *
     * <code>
     * $locale  =  'bg-BG';
     * $digits  =  2;
     *
     * $helper  = new Helper($container);
     * $parser  = $moneyHelper->getParser($locale, $digits);
     * </code>
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \OutOfBoundsException
     *
     * @return IntlDecimalParser
     */
    public function getParser()
    {
        $language   = \JFactory::getLanguage();
        $locale     = $language->getTag();

        $parserHash  = StringHelper::generateMd5Hash(Constants::CONTAINER_PARSER_MONEY, $locale);

        if (!$this->container->exists($parserHash)) {
            $numberFormatter = $this->prepareNumberFormatter($locale, (int)$this->params->get('fraction_digits', 2));

            $parser  = new IntlDecimalParser($numberFormatter);

            $this->container->set($parserHash, $parser);
        } else {
            $parser = $this->container->get($parserHash);
        }

        return $parser;
    }
}
