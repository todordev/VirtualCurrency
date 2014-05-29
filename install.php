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

/**
 * Script file of the component
 */
class pkg_virtualcurrencyInstallerScript
{
    /**
     * Method to install the component.
     *
     * @param string $parent
     *
     * @return void
     */
    public function install($parent)
    {
    }

    /**
     * Method to uninstall the component.
     *
     * @param string $parent
     *
     * @return void
     */
    public function uninstall($parent)
    {
    }

    /**
     * Method to update the component.
     *
     * @param string $parent
     *
     * @return void
     */
    public function update($parent)
    {
    }

    /**
     * Method to run before an install/update/uninstall method.
     *
     * @param string $type
     * @param string $parent
     *
     * @return void
     */
    public function preflight($type, $parent)
    {
    }

    /**
     * Method to run after an install/update/uninstall method.
     *
     * @param string $type
     * @param string $parent
     *
     * @return void
     */
    public function postflight($type, $parent)
    {
        if (!defined("COM_VIRTUALCURRENCY_PATH_COMPONENT_ADMINISTRATOR")) {
            define("COM_VIRTUALCURRENCY_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_virtualcurrency");
        }

        // Register Component helpers
        JLoader::register("VirtualCurrencyInstallHelper", COM_VIRTUALCURRENCY_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "install.php");

        // Start table with the information
        VirtualCurrencyInstallHelper::startTable();

        // Requirements
        VirtualCurrencyInstallHelper::addRowHeading(JText::_("COM_VIRTUALCURRENCY_MINIMUM_REQUIREMENTS"));

        // Display result about verification for GD library
        $title = JText::_("COM_VIRTUALCURRENCY_GD_LIBRARY");
        $info  = "";
        if (!extension_loaded('gd') and function_exists('gd_info')) {
            $result = array("type" => "important", "text" => JText::_("COM_VIRTUALCURRENCY_WARNING"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JON"));
        }
        VirtualCurrencyInstallHelper::addRow($title, $result, $info);

        // Display result about verification for cURL library
        $title = JText::_("COM_VIRTUALCURRENCY_CURL_LIBRARY");
        $info  = "";
        if (!extension_loaded('curl')) {
            $info   = JText::_("COM_VIRTUALCURRENCY_CURL_INFO");
            $result = array("type" => "important", "text" => JText::_("JOFF"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JON"));
        }
        VirtualCurrencyInstallHelper::addRow($title, $result, $info);

        // Display result about verification Magic Quotes
        $title = JText::_("COM_VIRTUALCURRENCY_MAGIC_QUOTES");
        $info  = "";
        if (get_magic_quotes_gpc()) {
            $info   = JText::_("COM_VIRTUALCURRENCY_MAGIC_QUOTES_INFO");
            $result = array("type" => "important", "text" => JText::_("JON"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JOFF"));
        }
        VirtualCurrencyInstallHelper::addRow($title, $result, $info);

        // Display result about verification FileInfo
        $title = JText::_("COM_VIRTUALCURRENCY_FILEINFO");
        $info  = "";
        if (!function_exists('finfo_open')) {
            $info   = JText::_("COM_VIRTUALCURRENCY_FILEINFO_INFO");
            $result = array("type" => "important", "text" => JText::_("JOFF"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JON"));
        }
        VirtualCurrencyInstallHelper::addRow($title, $result, $info);

        // Display result about PHP version
        $title = JText::_("COM_VIRTUALCURRENCY_PHP_VERSION");
        $info  = "";
        if (version_compare(PHP_VERSION, '5.3.0') < 0) {
            $result = array("type" => "important", "text" => JText::_("COM_VIRTUALCURRENCY_WARNING"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        VirtualCurrencyInstallHelper::addRow($title, $result, $info);

        // Display result about verification of installed ITPrism Library
        jimport("itprism.version");
        $title = JText::_("COM_VIRTUALCURRENCY_ITPRISM_LIBRARY");
        $info  = "";
        if (!class_exists("ITPrismVersion")) {
            $info   = JText::_("COM_VIRTUALCURRENCY_ITPRISM_LIBRARY_DOWNLOAD");
            $result = array("type" => "important", "text" => JText::_("JNO"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        VirtualCurrencyInstallHelper::addRow($title, $result, $info);

        // Installed extensions

        VirtualCurrencyInstallHelper::addRowHeading(JText::_("COM_VIRTUALCURRENCY_INSTALLED_EXTENSIONS"));

        // Virtual Currency Library
        $result = array("type" => "success", "text" => JText::_("COM_VIRTUALCURRENCY_INSTALLED"));
        VirtualCurrencyInstallHelper::addRow(JText::_("COM_VIRTUALCURRENCY_VIRTUALCURRENCY_LIBRARY"), $result, JText::_("COM_VIRTUALCURRENCY_LIBRARY"));

        // Virtual Currency Accounts
        $result = array("type" => "success", "text" => JText::_("COM_VIRTUALCURRENCY_INSTALLED"));
        VirtualCurrencyInstallHelper::addRow(JText::_("COM_VIRTUALCURRENCY_MOD_VIRTUALCURRENCYACCOUNTS"), $result, JText::_("COM_VIRTUALCURRENCY_MODULE"));

        // VirtualCurrencyPayment - PayPal
        $result = array("type" => "success", "text" => JText::_("COM_VIRTUALCURRENCY_INSTALLED"));
        VirtualCurrencyInstallHelper::addRow(JText::_("COM_VIRTUALCURRENCY_VIRTUALCURRENCYPAYMENT_PAYPAL"), $result, JText::_("COM_VIRTUALCURRENCY_PLUGIN"));

        // User - Virtual Currency Account
        $result = array("type" => "success", "text" => JText::_("COM_VIRTUALCURRENCY_INSTALLED"));
        VirtualCurrencyInstallHelper::addRow(JText::_("COM_VIRTUALCURRENCY_USER_VIRTUALCURRENCYNEWACCOUNT"), $result, JText::_("COM_VIRTUALCURRENCY_PLUGIN"));

        // End table
        VirtualCurrencyInstallHelper::endTable();

        echo JText::sprintf("COM_VIRTUALCURRENCY_MESSAGE_REVIEW_SAVE_SETTINGS", JRoute::_("index.php?option=com_virtualcurrency"));

        jimport("itprism.version");
        if (!class_exists("ITPrismVersion")) {
            echo JText::_("COM_VIRTUALCURRENCY_MESSAGE_INSTALL_ITPRISM_LIBRARY");
        }
    }
}
