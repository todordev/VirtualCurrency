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

/**
 * Script file of the component
 */
class pkg_virtualcurrencyInstallerScript {
    
    /**
     * method to install the component
     *
     * @return void
     */
    public function install($parent) {
    }
    
    /**
     * method to uninstall the component
     *
     * @return void
     */
    public function uninstall($parent) {
    }
    
    /**
     * method to update the component
     *
     * @return void
     */
    public function update($parent) {
    }
    
    /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
    public function preflight($type, $parent) {
    }
    
    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    public function postflight($type, $parent) {
        
        if(strcmp($type, "install") == 0) {
        
            if(!defined("COM_VIRTUALCURRENCY_PATH_COMPONENT_ADMINISTRATOR")) {
                define("COM_VIRTUALCURRENCY_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR ."com_virtualcurrency");
            }
        
            // Register Component helpers
            JLoader::register("VirtualCurrencyInstallHelper", COM_VIRTUALCURRENCY_PATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."installer.php");
        
            $this->bootstrap    = JPath::clean( JPATH_SITE.DIRECTORY_SEPARATOR."media".DIRECTORY_SEPARATOR."com_virtualcurrency".DIRECTORY_SEPARATOR."css".DIRECTORY_SEPARATOR. "admin".DIRECTORY_SEPARATOR."bootstrap.min.css" );
        
            $style = '<style>'.file_get_contents($this->bootstrap).'</style>';
            echo $style;
        
            // Start table with the information
            VirtualCurrencyInstallHelper::startTable();
        
            // Requirements
            VirtualCurrencyInstallHelper::addRowHeading(JText::_("COM_VIRTUALCURRENCY_MINIMUM_REQUIREMENTS"));
        
            // Display result about verification for GD library
            $title  = JText::_("COM_VIRTUALCURRENCY_GD_LIBRARY");
            $info   = "";
            if(!extension_loaded('gd') AND function_exists('gd_info')) {
                $result = array("type" => "important", "text" => JText::_("COM_VIRTUALCURRENCY_WARNING"));
            } else {
                $result = array("type" => "success"  , "text" => JText::_("JON"));
            }
            VirtualCurrencyInstallHelper::addRow($title, $result, $info);
        
            // Display result about verification for cURL library
            $title  = JText::_("COM_VIRTUALCURRENCY_CURL_LIBRARY");
            $info   = "";
            if( !extension_loaded('curl') ) {
                $info   = JText::_("COM_VIRTUALCURRENCY_CURL_INFO");
                $result = array("type" => "important", "text" => JText::_("JOFF"));
            } else {
                $result = array("type" => "success"  , "text" => JText::_("JON"));
            }
            VirtualCurrencyInstallHelper::addRow($title, $result, $info);
        
            // Display result about verification Magic Quotes
            $title  = JText::_("COM_VIRTUALCURRENCY_MAGIC_QUOTES");
            $info   = "";
            if( get_magic_quotes_gpc() ) {
                $info   = JText::_("COM_VIRTUALCURRENCY_MAGIC_QUOTES_INFO");
                $result = array("type" => "important", "text" => JText::_("JON"));
            } else {
                $result = array("type" => "success"  , "text" => JText::_("JOFF"));
            }
            VirtualCurrencyInstallHelper::addRow($title, $result, $info);
        
            // Display result about verification of installed ITPrism Library
            jimport("itprism.version");
            $title  = JText::_("COM_VIRTUALCURRENCY_ITPRISM_LIBRARY");
            $info   = "";
            if( !class_exists("ITPrismVersion") ) {
                $info   = JText::_("COM_VIRTUALCURRENCY_ITPRISM_LIBRARY_DOWNLOAD");
                $result = array("type" => "important", "text" => JText::_("JNO"));
            } else {
                $result = array("type" => "success", "text" => JText::_("JYES"));
            }
            VirtualCurrencyInstallHelper::addRow($title, $result, $info);
        
            // Installed extensions
        
            VirtualCurrencyInstallHelper::addRowHeading(JText::_("COM_VIRTUALCURRENCY_INSTALLED_EXTENSIONS"));
        
            // Virtual Currency Library
            $result = array("type" => "success"  , "text" => JText::_("COM_VIRTUALCURRENCY_INSTALLED"));
            VirtualCurrencyInstallHelper::addRow(JText::_("COM_VIRTUALCURRENCY_VIRTUALCURRENCY_LIBRARY"), $result, JText::_("COM_VIRTUALCURRENCY_LIBRARY"));
        
            // Virtual Currency Accounts
            $result = array("type" => "success"  , "text" => JText::_("COM_VIRTUALCURRENCY_INSTALLED"));
            VirtualCurrencyInstallHelper::addRow(JText::_("COM_VIRTUALCURRENCY_MOD_VIRTUALCURRENCYACCOUNTS"), $result, JText::_("COM_VIRTUALCURRENCY_MODULE"));
            
            // VirtualCurrencyPayment - PayPal
            $result = array("type" => "success"  , "text" => JText::_("COM_VIRTUALCURRENCY_INSTALLED"));
            VirtualCurrencyInstallHelper::addRow(JText::_("COM_VIRTUALCURRENCY_VIRTUALCURRENCYPAYMENT_PAYPAL"), $result, JText::_("COM_VIRTUALCURRENCY_PLUGIN"));
        
            // User - Virtual Currency Account
            $result = array("type" => "success"  , "text" => JText::_("COM_VIRTUALCURRENCY_INSTALLED"));
            VirtualCurrencyInstallHelper::addRow(JText::_("COM_VIRTUALCURRENCY_USER_VIRTUALCURRENCYNEWACCOUNT"), $result, JText::_("COM_VIRTUALCURRENCY_PLUGIN"));
        
            // End table
            VirtualCurrencyInstallHelper::endTable();
        
        }
        
        echo JText::sprintf("COM_VIRTUALCURRENCY_MESSAGE_REVIEW_SAVE_SETTINGS", JRoute::_("index.php?option=com_virtualcurrency"));
        
        jimport("itprism.version");
        if(!class_exists("ITPrismVersion")) {
            echo JText::_("COM_VIRTUALCURRENCY_MESSAGE_INSTALL_ITPRISM_LIBRARY");
        }
        
    }
}
