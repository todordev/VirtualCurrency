<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\Data\DataObject;

// no direct access
defined('_JEXEC') or die;

class VirtualcurrencyViewCart extends JViewLegacy
{
    /**
     * @var JApplicationSite
     */
    public $app;

    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $state;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $params;

    protected $item;

    protected $option;

    /**
     * @var JUser
     */
    protected $user;

    protected $realCurrency;

    /**
     * Real currency amount formatter.
     *
     * @var Prism\Money\Formatter
     */
    protected $formatter;

    protected $layoutData;
    protected $disabledButton = false;

    protected $currencies;
    protected $commodities;
    protected $numberOfItems = array();

    protected $version;

    protected $layout;
    protected $imageFolder;

    protected $pageclass_sfx;

    public function display($tpl = null)
    {
        $this->app    = JFactory::getApplication();
        $this->option = $this->app->input->get('option');

        // Get params
        $this->params = $this->app->getParams();

        // Create an object that will contain the data during the payment process.
//        $this->app->setUserState(Virtualcurrency\Constants::PAYMENT_SESSION_CONTEXT, null);
        $cartSession = $this->app->getUserState(Virtualcurrency\Constants::PAYMENT_SESSION_CONTEXT);

        // Create payment session object.
        if (!$cartSession or !isset($cartSession->step1)) {
            $cartSession = $this->initCartSession();
        }

        // Images
        $this->imageFolder = $this->params->get('media_folder', 'images/virtualcurrency');

        // Prepare amount formatter.
        $mapper             = new Virtualcurrency\RealCurrency\Mapper(new Virtualcurrency\RealCurrency\Gateway\JoomlaGateway(JFactory::getDbo()));
        $repository         = new Virtualcurrency\RealCurrency\Repository($mapper);
        $this->realCurrency = $repository->fetchById((int)$this->params->get('currency_id'));

        $this->formatter    = Virtualcurrency\Money\Helper::factory('joomla')->getFormatter();

        $this->layout = $this->getLayout();

        switch ($this->layout) {
            case 'payment':
                $this->preparePayment($cartSession);
                break;

            case 'summary':
                $this->prepareSummary($cartSession);
                break;

            default: //  Cart
                $this->prepareCart($cartSession);
                break;
        }

        // Prepare the data of the layout
        $this->layoutData = new DataObject(array(
            'layout'      => $this->getLayout(),
            'cartSession' => $cartSession
        ));

        $this->prepareDocument();

        parent::display($tpl);
    }

    /**
     * @param DataObject $cartSession
     */
    protected function prepareCart($cartSession)
    {
        // Create payment session ID.
        $cartSession->session_id = (string)Ramsey\Uuid\Uuid::uuid4();

        // Load virtual currencies.
        $conditions = array(
            'published' => Prism\Constants::PUBLISHED
        );

        $mapper           = new Virtualcurrency\Currency\Mapper(new Virtualcurrency\Currency\Gateway\JoomlaGateway(JFactory::getDbo()));
        $repository       = new Virtualcurrency\Currency\Repository($mapper);
        $this->currencies = $repository->fetchCollection($conditions);

        $mapper            = new Virtualcurrency\Commodity\Mapper(new Virtualcurrency\Commodity\Gateway\JoomlaGateway(JFactory::getDbo()));
        $repository        = new Virtualcurrency\Commodity\Repository($mapper);
        $this->commodities = $repository->fetchCollection($conditions);

        // Check days left. If there is no days, disable the button.
        $this->disabledButton = '';

        // Check for debug mode
        if ($this->params->get('debug_payment_disabled', 0)) {
            $msg = trim($this->params->get('debug_disabled_functionality_msg'));
            if (!$msg) {
                $msg = JText::_('COM_VIRTUALCURRENCY_DEBUG_MODE_DEFAULT_MSG');
            }
            $this->app->enqueueMessage($msg, 'notice');

            $this->disabledButton = "disabled='disabled'";
        }

        // Set payment data to the sessions
        $this->app->setUserState(Virtualcurrency\Constants::PAYMENT_SESSION_CONTEXT, $cartSession);
    }

    protected function preparePayment($cartSession)
    {
        if ($this->params->get('debug_payment_disabled', 0)) {
            $this->app->redirect(JRoute::_(VirtualcurrencyHelperRoute::getCartRoute(), false));
            return;
        }

        $this->item = $this->prepareItem($cartSession);

        if ($this->item === null or !$this->item->id) {
            $this->app->enqueueMessage(JText::_('COM_VIRTUALCURRENCY_ERROR_INVALID_ITEM'), 'warning');
            $this->app->redirect(JRoute::_(VirtualcurrencyHelperRoute::getCartRoute(), false));
            return;
        }

        // Events
        JPluginHelper::importPlugin('virtualcurrencypayment');
        $dispatcher        = JEventDispatcher::getInstance();
        $this->item->event = new stdClass;

        $results                             = $dispatcher->trigger('onPreparePayment', array('com_virtualcurrency.payment.prepare', &$this->item, &$this->params));
        $this->item->event->onPreparePayment = trim(implode("\n", $results));
    }

    protected function prepareSummary($cartSession)
    {
        if ($this->params->get('debug_payment_disabled', 0)) {
            $this->app->redirect(JRoute::_(VirtualcurrencyHelperRoute::getCartRoute(), false));
            return;
        }

        $this->item = $this->prepareItem($cartSession);

        // Initialize the payment process object.
        $cartSession = $this->initCartSession();
        $this->app->setUserState(Virtualcurrency\Constants::PAYMENT_SESSION_CONTEXT, $cartSession);
    }

    /**
     * Prepare the document
     */
    protected function prepareDocument()
    {
        // Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        // Prepare page heading
        $this->preparePageHeading();

        // Prepare page heading
        $this->preparePageTitle();

        if ($this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords')) {
            $this->document->setMetaData('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->document->setMetaData('robots', $this->params->get('robots'));
        }

        // Add scripts
        JHtml::_('jquery.framework');
        JHtml::script('com_virtualcurrency/site/payment.js', false, true, false);
    }

    public function preparePageHeading()
    {
        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menus = $this->app->getMenu();
        $menu  = $menus->getActive();

        // Prepare page heading
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::sprintf('COM_VIRTUALCURRENCY_PAYMENT_DEFAULT_PAGE_TITLE', $this->item->title));
        }
    }

    private function preparePageTitle()
    {
        // Prepare page title
        $title = $this->params->get('page_title', '');

        // Add title before or after Site Name
        if (!$title) {
            $title = $this->app->get('sitename');
        } elseif ((int)$this->app->get('sitename_pagetitles', 0) === 1) {
            $title = JText::sprintf('JPAGETITLE', $this->app->get('sitename'), $title);
        } elseif ((int)$this->app->get('sitename_pagetitles', 0) === 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $this->app->get('sitename'));
        }

        $this->document->setTitle($title);
    }

    private function initCartSession()
    {
        $cartSession               = new DataObject();
        $cartSession->item_id      = '';
        $cartSession->item_type    = '';
        $cartSession->items_number = 0;
        $cartSession->step1        = false;
        $cartSession->session_id   = '';

        return $cartSession;
    }

    private function prepareItem($cartSession)
    {
        switch ($cartSession->item_type) {
            case 'currency':
                $currencyGateway  = new Virtualcurrency\Currency\Gateway\JoomlaGateway(JFactory::getDbo());
                $currencyPreparer = new Virtualcurrency\Cart\Command\CurrencyPreparation($this->formatter, $this->realCurrency, $currencyGateway);
                $item = $currencyPreparer->prepare(new Virtualcurrency\Cart\Session($cartSession));
                break;

            case 'commodity':
                $currencyGateway   = new Virtualcurrency\Currency\Gateway\JoomlaGateway(JFactory::getDbo());
                $commodityGateway  = new Virtualcurrency\Commodity\Gateway\JoomlaGateway(JFactory::getDbo());
                $commodityPreparer = new Virtualcurrency\Cart\Command\CommodityPreparation($this->formatter, $this->realCurrency, $currencyGateway, $commodityGateway);
                $item = $commodityPreparer->prepare(new Virtualcurrency\Cart\Session($cartSession));
                break;

            default:
                $item = null;
                break;
        }

        return $item;
    }
}
