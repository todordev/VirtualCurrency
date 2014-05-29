<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

class VirtualCurrencyViewPayment extends JViewLegacy
{
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

    protected $currency;
    protected $currencies;
    protected $amount;
    protected $layoutData;
    protected $total;
    protected $disabledButton = false;
    protected $currencyAmount;
    protected $realCurrency;

    protected $version;
    protected $layoutsBasePath;

    protected $pageclass_sfx;

    protected $paymentData = array(
        "payment_id" => 0,
        "item_id" => 0,
        "currency_id" => 0,
        "amount" => 0,
        "step1" => false,
        "step2" => false,
        "step3" => false
    );

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");

        $this->layoutsBasePath = JPath::clean(JPATH_COMPONENT_ADMINISTRATOR . "/layouts");
    }

    public function display($tpl = null)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Get model state.
        $this->state  = $this->get('State');
        $this->params = $this->state->get("params");

        $this->user = JFactory::getUser();
        if (!$this->user->id) {
            $app->enqueueMessage(JText::_("COM_VIRTUALCURRENCY_ERROR_NOT_LOG_IN"), "notice");
            $app->redirect(JRoute::_('index.php?option=com_users&view=login', false));

            return;
        }

        $this->version = new VirtualCurrencyVersion();

        // Prepare the data of the layout
        $this->layoutData         = new stdClass();
        $this->layoutData->layout = $this->getLayout();

        // Set the flag for step one.
        $paymentProcessData = $this->getPaymentProcessData($app);

        $this->layoutData->flagStep1 = $paymentProcessData["step1"];

        switch ($this->layoutData->layout) {

            case "services":
                $this->prepareServices($app);
                break;

            case "information":
                $this->prepareInformation($app);
                break;

            default: //  Currency selecting
                $this->prepareCurrency($app, $paymentProcessData);
                break;
        }

        $this->prepareDocument();

        parent::display($tpl);
    }

    /**
     * @param $app JApplicationSite
     *
     * @return array
     */
    protected function getPaymentProcessData($app)
    {
        $paymentProcessData = $app->getUserState("payment.data");

        if (!$paymentProcessData) {
            $paymentProcessData = $this->paymentData;

            $app->setUserState("payment.data", $paymentProcessData);
        }

        return $paymentProcessData;
    }

    /**
     * @param JApplicationSite $app
     * @param array $paymentProcessData
     */
    protected function prepareCurrency($app, $paymentProcessData)
    {
        // Load currencies
        $options = array(
            "state" => 1
        );

        jimport("virtualcurrency.currencies");
        $this->currencies       = new VirtualCurrencyCurrencies(JFactory::getDbo());
        $this->currencies->load($options);

        // Get amount from session
        $this->currencyAmount = $paymentProcessData["amount"];

        // Get item if there is one
        $itemId = $paymentProcessData["item_id"];

        if (!empty($itemId)) {

            $item = new VirtualCurrencyCurrency(JFactory::getDbo());
            $item->load($itemId);

            // Compare amount with the minimum allowed amount.
            if ($this->currencyAmount < $item->getParam("minimum")) {

                // Reset payment data.
                $paymentProcessData = $this->paymentData;

                $app->setUserState("payment.data", $paymentProcessData);

                $this->layoutData->flagStep1 = false;

            }
        }

        // Check days left. If there is no days, disable the button.
        $this->disabledButton = "";

        // Check for debug mode
        if ($this->params->get("debug_payment_disabled", 0)) {

            $msg = JString::trim($this->params->get("debug_disabled_functionality_msg"));
            if (!$msg) {
                $msg = JText::_("COM_VIRTUALCURRENCY_DEBUG_MODE_DEFAULT_MSG");
            }
            $app->enqueueMessage($msg, "notice");

            $this->disabledButton = 'disabled="disabled"';

        }

    }

    /**
     * @param JApplicationSite $app
     */
    protected function prepareServices($app)
    {
        if ($this->params->get("debug_payment_disabled", 0)) {
            $app->redirect(JRoute::_('index.php?option=com_virtualcurrency&view=payment', false));
            return;
        }

        $paymentSessionData = $app->getUserState("payment.data");

        $itemId       = $paymentSessionData["item_id"];
        $this->amount = $paymentSessionData["amount"];

        jimport("virtualcurrency.currency");
        $this->item = new VirtualCurrencyCurrency(JFactory::getDbo());
        $this->item->load($itemId);

        // Calculate total amount that should be paid.
        jimport("itprism.math");
        $total = new ITPrismMath();
        $total->calculateTotal(array(
            $this->amount,
            $this->item->getParam("amount")
        ));
        $this->total = (string)$total;

        // Get real currency
        $realCurrencyId = $this->params->get("payments_currency_id");
        jimport("virtualcurrency.realcurrency");
        $this->realCurrency = VirtualCurrencyRealCurrency::getInstance(JFactory::getDbo(), $realCurrencyId, $this->params);

        // Events
        JPluginHelper::importPlugin('virtualcurrencypayment');
        $dispatcher        = JEventDispatcher::getInstance();
        $this->item->event = new stdClass;

        $properties = $this->item->getProperties();
        $item = JArrayHelper::toObject($properties);
        $item->total       = $this->total;

        $results           = $dispatcher->trigger('onProjectPayment', array('com_virtualcurrency.payment', &$item, &$this->params));
        $this->item->event->onProjectPayment = trim(implode("\n", $results));

    }

    /**
     * @param JApplicationSite $app
     */
    protected function prepareInformation($app)
    {
        if ($this->params->get("debug_payment_disabled", 0)) {
            $app->redirect(JRoute::_('index.php?option=com_virtualcurrency&view=payment', false));
        }

        $paymentSessionData = $app->getUserState("payment.data");

        $itemId       = $paymentSessionData["item_id"];
        $this->amount = $paymentSessionData["amount"];

        jimport("virtualcurrency.currency");
        $this->item = new VirtualCurrencyCurrency(JFactory::getDbo());
        $this->item->load($itemId);

        // Calculate total amount that should be paid.
        jimport("itprism.math");
        $total = new ITPrismMath();
        $total->calculateTotal(array(
            $this->amount,
            $this->item->getParam("amount")
        ));
        $this->total = (string)$total;

        // Get real currency
        $realCurrencyId = $this->params->get("payments_currency_id");
        jimport("virtualcurrency.realcurrency");
        $this->realCurrency= VirtualCurrencyRealCurrency::getInstance(JFactory::getDbo(), $realCurrencyId, $this->params);
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
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }

        // Add scripts
        JHtml::_("jquery.framework");
        JHtml::script("com_virtualcurrency/site/payment.js", false, true, false);
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
        } elseif ($app->get('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        } elseif ($app->get('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }

        $this->document->setTitle($title);
    }
}
