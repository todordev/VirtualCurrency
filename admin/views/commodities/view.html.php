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

class VirtualCurrencyViewCommodities extends JViewLegacy
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

    protected $items;
    protected $pagination;

    protected $option;

    protected $listOrder;
    protected $listDirn;
    protected $saveOrder;
    protected $saveOrderingUrl;
    protected $sortFields;

    protected $sidebar;

    protected $amount;
    protected $mediaFolderUrl;
    protected $realCurrency;
    protected $currencies;

    public function display($tpl = null)
    {
        $this->option     = JFactory::getApplication()->input->get('option');

        VirtualCurrencyHelper::createCommodities();

        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        $this->params         = $this->state->get('params');

        $this->mediaFolderUrl = JUri::root() . $this->params->get('media_folder', 'images/virtualcurrency');

        // Get currency
        $this->realCurrency = Virtualcurrency\Currency\Real\Currency::getInstance(JFactory::getDbo(), $this->params->get('payments_currency_id'));
        $this->currencies   = new Virtualcurrency\Currency\Currencies(JFactory::getDbo());
        $this->currencies->load();

        $this->amount = new Virtualcurrency\Amount($this->params);

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

        if ($this->saveOrder) {
            $this->saveOrderingUrl = 'index.php?option=' . $this->option . '&task=' . $this->getName() . '.saveOrderAjax&format=raw';
            JHtml::_('sortablelist.sortable', $this->getName() . 'List', 'adminForm', strtolower($this->listDirn), $this->saveOrderingUrl);
        }

        $this->sortFields = array(
            'a.title'     => JText::_('COM_VIRTUALCURRENCY_TITLE'),
            'a.nummber'   => JText::_('COM_VIRTUALCURRENCY_IN_STOCK'),
            'a.sold'      => JText::_('COM_VIRTUALCURRENCY_SOLD'),
            'a.published' => JText::_('JSTATUS'),
            'a.id'        => JText::_('JGRID_HEADING_ID')
        );
    }

    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar()
    {
        // Add submenu
        VirtualCurrencyHelper::addSubmenu($this->getName());
        
        JHtmlSidebar::setAction('index.php?option=' . $this->option . '&view=' . $this->getName());

        JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'),
            'filter_state',
            JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived' => false)), 'value', 'text', $this->state->get('filter.state'), true)
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
        JToolBarHelper::title(JText::_('COM_VIRTUALCURRENCY_VIRTUAL_GOODS_MANAGER'));
        JToolBarHelper::addNew('commodity.add');
        JToolBarHelper::editList('commodity.edit');
        JToolBarHelper::divider();
        JToolBarHelper::publishList('commodities.publish');
        JToolBarHelper::unpublishList('commodities.unpublish');
        JToolBarHelper::divider();

        if ((int)$this->state->get('filter.state') === -2) {
            JToolbarHelper::deleteList('', 'commodities.delete', 'JTOOLBAR_EMPTY_TRASH');
        } else {
            JToolbarHelper::trash('commodities.trash');
        }

        JToolBarHelper::divider();
        JToolBarHelper::custom('commodities.backToDashboard', 'dashboard', '', JText::_('COM_VIRTUALCURRENCY_DASHBOARD'), false);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_VIRTUALCURRENCY_VIRTUAL_GOODS_MANAGER'));

        // Scripts
        JHtml::_('behavior.multiselect');
        JHtml::_('bootstrap.tooltip');

        JHtml::_('formbehavior.chosen', 'select');

        JHtml::_('prism.ui.joomlaList');
    }
}
