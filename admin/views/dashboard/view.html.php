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

class VirtualCurrencyViewDashboard extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    protected $option;

    protected $version;
    protected $itprismVersion;

    protected $sidebar;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }

    public function display($tpl = null)
    {
        $this->version = new VirtualCurrencyVersion();

        // Load ITPrism library version
        jimport("itprism.version");
        if (!class_exists("ITPrismVersion")) {
            $this->itprismVersion = JText::_("COM_VIRTUALCURRENCY_ITPRISM_LIBRARY_DOWNLOAD");
        } else {
            $itprismVersion       = new ITPrismVersion();
            $this->itprismVersion = $itprismVersion->getShortVersion();
        }

        // Add submenu
        VirtualCurrencyHelper::addSubmenu($this->getName());

        $this->addToolbar();
        $this->addSidebar();
        $this->setDocument();

        parent::display($tpl);
    }

    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar()
    {
        $this->sidebar = JHtmlSidebar::render();
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        JToolBarHelper::title(JText::_("COM_VIRTUALCURRENCY_DASHBOARD"), 'itp-dashboard');

        JToolBarHelper::preferences('com_virtualcurrency');
        JToolBarHelper::divider();

        // Help button
        $bar = JToolBar::getInstance('toolbar');
        $bar->appendButton('Link', 'help', JText::_('JHELP'), JText::_('COM_VIRTUALCURRENCY_HELP_URL'));
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_VIRTUALCURRENCY_DASHBOARD'));
    }
}
