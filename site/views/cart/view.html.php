<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

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
     * @var Virtualcurrency\Amount
     */
    protected $amountFormatter;

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
        $this->imageFolder      = $this->params->get('media_folder', 'images/virtualcurrency');

        // Prepare amount formatter.
        $this->realCurrency     = new Virtualcurrency\Currency\RealCurrency(JFactory::getDbo());
        $this->realCurrency->load($this->params->get('project_currency'));

        $moneyFormatter  = VirtualcurrencyHelper::getMoneyFormatter();
        $this->amountFormatter  = new Prism\Money\Money($moneyFormatter);
        $this->amountFormatter->setCurrency($this->realCurrency);

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
        $this->layoutData = new JData(array(
            'layout'       => $this->getLayout(),
            'cartSession'  => $cartSession
        ));

        $this->prepareDocument();

        parent::display($tpl);
    }

    /**
     * @param JData $cartSession
     */
    protected function prepareCart($cartSession)
    {
        // Create payment session ID.
        $cartSession->session_id = Prism\Utilities\StringHelper::generateRandomString(32);

        // Load virtual currencies.
        $options = array(
            'state' => Prism\Constants::PUBLISHED
        );

        $this->currencies       = new Virtualcurrency\Currency\Currencies(JFactory::getDbo());
        $this->currencies->load($options);

        $this->commodities      = new Virtualcurrency\Commodity\Commodities(JFactory::getDbo());
        $this->commodities->load($options);

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

        $this->item = VirtualcurrencyHelper::prepareItem($cartSession, $this->params);

        if ($this->item === null or !$this->item->id) {
            $this->app->enqueueMessage(JText::_('COM_VIRTUALCURRENCY_ERROR_INVALID_ITEM'), 'warning');
            $this->app->redirect(JRoute::_(VirtualcurrencyHelperRoute::getCartRoute(), false));
            return;
        }

        // Events
        JPluginHelper::importPlugin('virtualcurrencypayment');
        $dispatcher             = JEventDispatcher::getInstance();
        $this->item->event      = new stdClass;

        $results                = $dispatcher->trigger('onPreparePayment', array('com_virtualcurrency.payment.prepare', &$this->item, &$this->params));
        $this->item->event->onPreparePayment = trim(implode("\n", $results));
    }

    protected function prepareSummary($cartSession)
    {
        if ($this->params->get('debug_payment_disabled', 0)) {
            $this->app->redirect(JRoute::_(VirtualcurrencyHelperRoute::getCartRoute(), false));
            return;
        }

        $this->item = VirtualcurrencyHelper::prepareItem($cartSession, $this->params);

        // Initialize the payment process object.
        $cartSession        = $this->initCartSession();
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

    private function preparePageHeading()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menus = $app->getMenu();
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
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Prepare page title
        $title = $this->params->get('page_title', '');

        // Add title before or after Site Name
        if (!$title) {
            $title = $app->get('sitename');
        } elseif ((int)$app->get('sitename_pagetitles', 0) === 1) {
            $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        } elseif ((int)$app->get('sitename_pagetitles', 0) === 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }

        $this->document->setTitle($title);
    }

    private function initCartSession()
    {
        $cartSession             = new JData();
        $cartSession->item_id      = '';
        $cartSession->item_type    = '';
        $cartSession->items_number = 0;
        $cartSession->step1        = false;

        return $cartSession;
    }
}
