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

class VirtualcurrencyViewPartners extends JViewLegacy
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

    protected $listOrder;
    protected $listDirn;
    protected $saveOrder;

    protected $option;

    public function display($tpl = null)
    {
        $this->option = JFactory::getApplication()->input->get('option');

        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        // Prepare filters
        $this->listOrder = $this->escape($this->state->get('list.ordering'));
        $this->listDirn  = $this->escape($this->state->get('list.direction'));
        $this->saveOrder = (strcmp($this->listOrder, 'a.ordering') === 0);

        // Add submenu
        VirtualcurrencyHelper::addSubmenu($this->getName());
        
        // Prepare actions
        $this->addToolbar();
        $this->setDocument();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        // Set toolbar items for the page
        JToolBarHelper::title(JText::_('COM_VIRTUALCURRENCY_PARTNERS_MANAGER'));
        JToolBarHelper::addNew('partner.add');
        JToolBarHelper::editList('partner.edit');
        JToolBarHelper::divider();
        JToolBarHelper::publishList('partners.publish');
        JToolBarHelper::unpublishList('partners.unpublish');
        JToolBarHelper::divider();
        JToolBarHelper::deleteList(JText::_('COM_VIRTUALCURRENCY_DELETE_ITEMS_QUESTION'), 'partners.delete');
        JToolBarHelper::divider();
        JToolBarHelper::custom('partners.backToDashboard', 'dashboard', '', JText::_('COM_VIRTUALCURRENCY_DASHBOARD'), false);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_VIRTUALCURRENCY_PARTNERS_MANAGER'));

        JHtml::_('behavior.tooltip');
    }
}
