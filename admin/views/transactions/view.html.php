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

class VirtualcurrencyViewTransactions extends JViewLegacy
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

    public $activeFilters;
    public $filterForm;

    public function display($tpl = null)
    {
        $this->option = JFactory::getApplication()->input->get('option');
        
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        $params       = $this->state->get('params');
        /** @var  $params Joomla\Registry\Registry */

        $this->params = $params;

        // Load all currencies
        $this->currencies = new Virtualcurrency\Currency\Currencies(JFactory::getDbo());
        $this->currencies->load();

        // Get real currencies
        $this->realCurrency = new Virtualcurrency\Currency\RealCurrency(JFactory::getDbo());
        $this->realCurrency->load($this->params->get('payments_currency_id'));

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
        $this->saveOrder = (strcmp($this->listOrder, 'a.ordering') === 0);

        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
    }

    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar()
    {
        // Add submenu
        VirtualcurrencyHelper::addSubmenu($this->getName());
        
        JHtmlSidebar::setAction('index.php?option=' . $this->option . '&view=' . $this->getName());

        $states = array(
            'completed' => JText::_('COM_VIRTUALCURRENCY_COMPLETED'),
            'pending'   => JText::_('COM_VIRTUALCURRENCY_PENDING'),
            'canceled'  => JText::_('COM_VIRTUALCURRENCY_CANCELED'),
            'refunded'  => JText::_('COM_VIRTUALCURRENCY_REFUNDED'),
            'failed'    => JText::_('COM_VIRTUALCURRENCY_FAILED')
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
        JToolBarHelper::title(JText::_('COM_VIRTUALCURRENCY_TRANSACTION_MANAGER'));
        JToolBarHelper::editList('transaction.edit');
        JToolBarHelper::divider();
        JToolBarHelper::custom('transactions.backToDashboard', 'dashboard', '', JText::_('COM_VIRTUALCURRENCY_DASHBOARD'), false);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_VIRTUALCURRENCY_TRANSACTION_MANAGER'));

        // Scripts
        JHtml::_('behavior.multiselect');
        JHtml::_('bootstrap.tooltip');

        JHtml::_('formbehavior.chosen', 'select');

        JHtml::_('prism.ui.joomlaList');
    }
}
