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

class VirtualcurrencyViewCommodity extends JViewLegacy
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
    protected $form;

    protected $option;
    protected $documentTitle;
    protected $mediaFolder;

    public function display($tpl = null)
    {
        $this->option = JFactory::getApplication()->input->get('option');

        $this->state = $this->get('State');
        $this->item  = $this->get('Item');
        $this->form  = $this->get('Form');

        $this->params = $this->state->get('params');

        $this->mediaFolder = JUri::root() . $this->params->get('media_folder', 'images/virtualcurrency');

        // Prepare actions, behaviors, scripts and document
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
        JFactory::getApplication()->input->set('hidemainmenu', true);
        $isNew = ($this->item->id === 0);

        $this->documentTitle = $isNew ? JText::_('COM_VIRTUALCURRENCY_NEW_PRODUCT') : JText::_('COM_VIRTUALCURRENCY_EDIT_PRODUCT');

        JToolBarHelper::title($this->documentTitle);

        JToolBarHelper::apply('commodity.apply');
        JToolBarHelper::save2new('commodity.save2new');
        JToolBarHelper::save('commodity.save');

        if (!$isNew) {
            JToolBarHelper::cancel('commodity.cancel', 'JTOOLBAR_CANCEL');
        } else {
            JToolBarHelper::cancel('commodity.cancel', 'JTOOLBAR_CLOSE');
        }
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle($this->documentTitle);

        // Add behaviors
        JHtml::_('behavior.tooltip');
        JHtml::_('behavior.formvalidation');

//        JHtml::_('formbehavior.chosen', 'select');

        JText::script('COM_VIRTUALCURRENCY_QUESTION_REMOVE_IMAGES');

        // Add scripts
        $this->document->addScript('../media/' . $this->option . '/js/admin/' . strtolower($this->getName()) . '.js');
    }
}
