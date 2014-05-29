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

jimport('joomla.application.component.view');

class VirtualCurrencyViewTransactions extends JViewLegacy
{
    /**
     * @var Joomla\Registry\Registry
     */
    protected $params;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $state;

    /**
     * @var JDocumentHtml
     */
    public $document;

    protected $items;
    protected $pagination;

    protected $realCurrency;

    protected $option;

    protected $listOrder;
    protected $listDirn;
    protected $saveOrder;
    protected $saveOrderingUrl;
    protected $sortFields;

    protected $sidebar;

    protected $currencies;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }

    public function display($tpl = null)
    {
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        $params       = $this->state->get('params');
        /** @var  $params Joomla\Registry\Registry */

        $this->params = $params;

        // Load all currencies
        jimport("virtualcurrency.currencies");
        $this->currencies = new VirtualCurrencyCurrencies(JFactory::getDbo());
        $this->currencies->load();

        // Get real currenices
        jimport("virtualcurrency.realcurrency");
        $this->realCurrency = new VirtualCurrencyRealCurrency(JFactory::getDbo());
        $this->realCurrency->load($this->params->get("payments_currency_id"));

        // Add submenu
        VirtualCurrencyHelper::addSubmenu($this->getName());

        // Prepare sorting data
        $this->prepareSorting();

        // Prepare actions
        $this->addToolbar();
        $this->addSidebar();
        $this->setDocument();

        parent::display($tpl);
    }

    /**
     * Prepare sortable fields, sort values and filters.
     */
    protected function prepareSorting()
    {
        // Prepare filters
        $this->listOrder = $this->escape($this->state->get('list.ordering'));
        $this->listDirn  = $this->escape($this->state->get('list.direction'));
        $this->saveOrder = (strcmp($this->listOrder, 'a.ordering') != 0) ? false : true;

        if ($this->saveOrder) {
            $this->saveOrderingUrl = 'index.php?option=' . $this->option . '&task=' . $this->getName() . '.saveOrderAjax&format=raw';
            JHtml::_('sortablelist.sortable', $this->getName() . 'List', 'adminForm', strtolower($this->listDirn), $this->saveOrderingUrl);
        }

        $this->sortFields = array(
            'a.title'     => JText::_('COM_VIRTUALCURRENCY_CURRENCY'),
            'a.published' => JText::_('JSTATUS'),
            'a.code'      => JText::_('COM_VIRTUALCURRENCY_CURRENCY_CODE'),
            'a.id'        => JText::_('JGRID_HEADING_ID')
        );
    }

    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar()
    {
        JHtmlSidebar::setAction('index.php?option=' . $this->option . '&view=' . $this->getName());

        $states = array(
            "completed" => JText::_('COM_VIRTUALCURRENCY_COMPLETED'),
            "pending"   => JText::_('COM_VIRTUALCURRENCY_PENDING')
        );

        JHtmlSidebar::addFilter(
            JText::_('COM_VIRTUALCURRENCY_SELECT_STATUS'),
            'filter_state',
            JHtml::_('select.options', $states, 'value', 'text', $this->state->get('filter.state'), true)
        );

        $this->sidebar = JHtmlSidebar::render();
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        // Set toolbar items for the page
        JToolBarHelper::title(JText::_('COM_VIRTUALCURRENCY_TRANSACTIONS_MANAGER'));
        JToolBarHelper::editList('transaction.edit');
        JToolBarHelper::divider();
        JToolBarHelper::custom('transactions.backToDashboard', "dashboard", "", JText::_("COM_VIRTUALCURRENCY_DASHBOARD"), false);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_VIRTUALCURRENCY_TRANSACTIONS_MANAGER'));

        // Scripts
        JHtml::_('behavior.multiselect');
        JHtml::_('bootstrap.tooltip');

        JHtml::_('formbehavior.chosen', 'select');

        JHtml::_('itprism.ui.joomla_list');
    }
}
