<?php
/**
 * @package      ITPrism Components
 * @subpackage   VirtualCurrency
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * VirtualCurrency is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.view');

class VirtualCurrencyViewOrdering extends JView {
    
    const PUBLISHED = 1;
    
    protected $state;
    protected $item;
    protected $params;
    
    protected $option;
    
    public function __construct($config) {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }
    
    public function display($tpl = null) {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        $model             = $this->getModel();
        
        // Get model state.
        $this->state       = $this->get('State');
        $this->params      = $this->state->get("params");
        
        $this->user        = JFactory::getUser();
        if (!$this->user->id) {
            $app->enqueueMessage(JText::_("COM_VIRTUALCURRENCY_ERROR_NOT_LOG_IN"), "notice");
            $app->redirect(JRoute::_('index.php?option=com_users&view=login', false));
            return;
        }
        
        // Set the flag for step one.
        $this->flagStep1      = $app->getUserState("ordering.step1", false);
        
        // Include HTML helper
        JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
        
	    $this->version        = new VirtualCurrencyVersion();
	    
        $this->layout         = $this->getLayout();
        
        switch($this->layout) {
            
            case "payment":
                $this->preparePayment();
                break;
                
            case "information":
                $this->preparePayment();
                break;
                
            default: //  Currency selecting
                $this->prepareCurrency();
                break;
        }
        
		$this->prepareDocument();
		
        parent::display($tpl);
    }
    
    
    protected function prepareCurrency() {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        $model                = $this->getModel();
        
        // Get amount from session
        $this->currencyAmount = $app->getUserState("ordering.amount", 0);

        jimport("virtualcurrency.currencies");
        
        $db = JFactory::getDbo();
        $currencies           = new VirtualCurrencyCurrencies($db);
        $currencies->load(self::PUBLISHED);
        
        $this->currencies     = $currencies->getCurrencies();
        
        // Get item if there is one
        $itemId       = $app->getUserState("ordering.item_id", 0);
        
        if(!empty($itemId)) {
            
            $item   = $model->getItem($itemId);
            
            // Compare amount with the minimum allowed amount.
            if($this->currencyAmount < $item->minimum) {
                
                // Initialize amount state
                $app->setUserState("ordering.amount", 0);
                
                // Set step 1 to false
                $app->setUserState("ordering.step1", false);
                
                // Initialize temporary ID
                $app->setUserState("ordering.tmp_id", 0);
                
                $this->flagStep1 = false;
                
            }
        }
        
        // Check days left. If there is no days, disable the button.
        $this->disabledButton = "";
        
        // Check for debug mode
        if($model->isDebugMode()) {
            
            $msg = JString::trim($this->params->get("debug_disabled_functionality_msg"));
            if(!$msg) {
                $msg = JText::_("COM_VIRTUALCURRENCY_DEBUG_MODE_DEFAULT_MSG");
            }
            $app->enqueueMessage($msg, "notice");
            
            $this->disabledButton = 'disabled="disabled"';
            
        }
        
    }
    
    protected function preparePayment() {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        $model        = $this->getModel();
        
        if($model->isDebugMode()) {
            $app->redirect( JRoute::_('index.php?option=com_virtualcurrency&view=ordering', false) );
            return; 
        }
        
        $itemId       = $app->getUserState("ordering.item_id");
        $this->amount = $app->getUserState("ordering.amount");
        
        $this->item   = $model->getItem($itemId);
        
        $this->currency = array(
            "code"   => $this->item->code,
            "symbol" => $this->item->symbol
        );
        
        $this->total =  JHtml::_("virtualcurrency.total", $this->amount, $this->item->amount);
        
        // Events
        JPluginHelper::importPlugin('virtualcurrencypayment');
        $dispatcher	        = JDispatcher::getInstance();
        $this->item->event  = new stdClass();
        
        $item               = new stdClass();
        
        $item->id           = $this->item->id;
        $item->title        = $this->item->title;
        $item->currency     = $this->item->currency;
        $item->amount       = $this->total;
        
        $results            = $dispatcher->trigger('onProjectPayment', array('com_virtualcurrency.payment', $item));
		$this->item->event->onProjectPayment = trim(implode("\n", $results));
		
    }
    
    protected function prepareInformation() {
    
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        $model        = $this->getModel();
        
        if($model->isDebugMode()) {
            $app->redirect( JRoute::_('index.php?option=com_virtualcurrency&view=ordering', false) );
            return; 
        }
        
        $itemId       = $app->getUserState("ordering.item_id");
        $this->amount = $app->getUserState("ordering.amount");
        
        $this->item   = $model->getItem($itemId);
        
        $this->currency = array(
            "code"   => $this->item->code,
            "symbol" => $this->item->symbol
        );
        
        $this->total =  JHtml::_("virtualcurrency.total", $this->amount, $this->item->amount);
    
    
    }
    
    /**
     * Prepare the document
     */
    protected function prepareDocument() {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        // Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
        
        // Prepare page heading
        $this->prepearePageHeading();
        
        // Prepare page heading
        $this->prepearePageTitle();
        
        if ($this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        } 
        
        if ($this->params->get('menu-meta_keywords')) {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }
        
        if ($this->params->get('robots')) {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }
        
        // Add styles
        $this->document->addStyleSheet( 'media/'.$this->option.'/css/site/bootstrap.min.css');
        $this->document->addStyleSheet( 'media/'.$this->option.'/css/site/style.css');
        
        // Add scripts
        $this->document->addScript(JURI::root() . 'media/'.$this->option.'/js/site/ordering.js');
        
    }
    
    private function prepearePageHeading() {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menus = $app->getMenu();
        $menu  = $menus->getActive();
        
        // Prepare page heading
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::sprintf('COM_VIRTUALCURRENCY_PAYMENT_DEFAULT_PAGE_TITLE', $this->item->title));
        }
    
    }
    
    private function prepearePageTitle() {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        // Prepare page title
        $title = $this->params->get('page_title', '');
//        $title = JText::sprintf("COM_VIRTUALCURRENCY_BUY_VIRTUAL_CURRENCY", $this->escape($this->item->title) );
        
        // Add title before or after Site Name
        if (!$title) {
            $title = $app->getCfg('sitename');
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }
        
        $this->document->setTitle($title);
    
    }

}