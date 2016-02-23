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
        $option = $this->input->getCmd('option');

        $document = JFactory::getDocument();
        /** @var $document JDocumentHtml */

        // Add component style
        $document->addStyleSheet('../media/' . $option . '/css/backed.style.css');

        $viewName = $this->input->getCmd('view', 'dashboard');
        $this->input->set('view', $viewName);

        parent::display();

        return $this;
    }
}
