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

jimport('joomla.application.component.controller');

/**
 * Default Controller
 *
 * @package        VirtualCurrency
 * @subpackage     Components
 */
class VirtualCurrencyController extends JControllerLegacy
{
    public function display($cachable = false, $urlparams = array())
    {
        $option = $this->input->getCmd("option");

        $document = JFactory::getDocument();
        /** @var $document JDocumentHtml * */

        // Add component style
        $document->addStyleSheet('../media/' . $option . '/css/admin/style.css');

        $viewName = $this->input->getCmd('view', 'dashboard');
        $this->input->set("view", $viewName);

        parent::display();

        return $this;
    }
}
