<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

class VirtualcurrencyViewImport extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $state;

    protected $form;

    protected $legend;

    protected $option;
    
    public function display($tpl = null)
    {
        $this->option = JFactory::getApplication()->input->get('option');
        
        $this->state = $this->get('State');
        $this->form  = $this->get('Form');

        // Add submenu
        VirtualcurrencyHelper::addSubmenu('realcurrencies');

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
        JToolbarHelper::title(JText::_('COM_VIRTUALCURRENCY_IMPORT_MANAGER'));

        // Upload
        JToolbarHelper::custom('import.realCurrencies', 'upload', '', JText::_('COM_VIRTUALCURRENCY_UPLOAD'), false);

        JToolbarHelper::divider();

        // Add custom buttons
        $bar = JToolbar::getInstance('toolbar');

        // Cancel
        $link = JRoute::_('index.php?option=com_virtualcurrency&view=realcurrencies');
        $bar->appendButton('Link', 'cancel', JText::_('JTOOLBAR_CANCEL'), $link);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_VIRTUALCURRENCY_IMPORT_MANAGER'));

        $this->legend     = JText::_('COM_VIRTUALCURRENCY_IMPORT_REAL_CURRENCY_DATA');

        // Scripts
        JHtml::_('behavior.formvalidation');

        JHtml::_('bootstrap.tooltip');
        JHtml::_('prism.ui.bootstrap2FileInput');

        $this->document->addScript('../media/' . $this->option . '/js/admin/' . JString::strtolower($this->getName()) . '.js');
    }
}
