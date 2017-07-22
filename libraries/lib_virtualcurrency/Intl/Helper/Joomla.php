<?php
/**
 * @package      Virtualcurrency\Intl
 * @subpackage   Helper
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Intl\Helper;

use Prism\Container;
use Prism\Intl\Formatter\IntlDecimalFormatter;
use Prism\Intl\Parser\IntlDecimalParser;
use Virtualcurrency\Constants;
use Prism\Utilities\StringHelper;
use Joomla\Registry\Registry;

/**
 * This class provides functionality to prepare accounts data.
 *
 * @package      Virtualcurrency\Intl
 * @subpackage   Helper
 */
class Joomla extends IntlHelper
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
    public function getNumberFormatter()
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
    public function getNumberParser()
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
