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

class VirtualcurrencyViewAccounts extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $state;

    protected $items;
    protected $pagination;

    protected $option;

    protected $listOrder;
    protected $listDirn;
    protected $saveOrder;
    protected $saveOrderingUrl;
    protected $sortFields;

    protected $sidebar;
    protected $formatter;
    protected $currencies;

    public $activeFilters;
    public $filterForm;
    
    public function display($tpl = null)
    {
        $this->option = JFactory::getApplication()->input->get('option');

        // Create accounts.
        $createAccountsCommand = new Virtualcurrency\Account\Command\CreateAccounts();
        $createAccountsCommand->setGateway(new \Virtualcurrency\Account\Command\Gateway\JoomlaCreateAccounts(JFactory::getDbo()));
        $createAccountsCommand->handle();

        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        $mapper = new Virtualcurrency\Currency\Mapper(new Virtualcurrency\Currency\Gateway\JoomlaGateway(JFactory::getDbo()));
        $repository = new Virtualcurrency\Currency\Repository($mapper);
        $this->currencies = $repository->fetchAll();

        $this->formatter  = Virtualcurrency\Money\Helper::factory('joomla')->getFormatter();

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
        
        $this->sidebar = JHtmlSidebar::render();
    }

    /**
     * Add the page title and toolbar.
     * @since   1.6
     */
    protected function addToolbar()
    {
        // Set toolbar items for the page
        JToolBarHelper::title(JText::_('COM_VIRTUALCURRENCY_ACCOUNT_MANAGER'));
        JToolBarHelper::addNew('account.add');
        JToolBarHelper::editList('account.edit');
        JToolbarHelper::publishList('accounts.publish');
        JToolbarHelper::unpublishList('accounts.unpublish');
        JToolBarHelper::divider();
        JToolBarHelper::custom('accounts.backToDashboard', 'dashboard', '', JText::_('COM_VIRTUALCURRENCY_DASHBOARD'), false);
    }

    /**
     * Method to set up the document properties
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_VIRTUALCURRENCY_ACCOUNT_MANAGER'));

        // Scripts
        JHtml::_('behavior.multiselect');
        JHtml::_('bootstrap.tooltip');

        JHtml::_('formbehavior.chosen', 'select');
    }
}
