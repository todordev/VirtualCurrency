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
        if (!defined('COM_VIRTUALCURRENCY_PATH_COMPONENT_ADMINISTRATOR')) {
            define('COM_VIRTUALCURRENCY_PATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtualcurrency');
        }

        jimport('Prism.init');
        jimport('Virtualcurrency.init');

        // Register Component helpers
        JLoader::register('VirtualcurrencyInstallHelper', COM_VIRTUALCURRENCY_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'install.php');

        // Start table with the information
        VirtualcurrencyInstallHelper::startTable();

        // Requirements
        VirtualcurrencyInstallHelper::addRowHeading(JText::_('COM_VIRTUALCURRENCY_MINIMUM_REQUIREMENTS'));

        // Display result about verification for GD library
        $title = JText::_('COM_VIRTUALCURRENCY_GD_LIBRARY');
        $info  = '';
        if (!extension_loaded('gd') and function_exists('gd_info')) {
            $result = array('type' => 'important', 'text' => JText::_('COM_VIRTUALCURRENCY_WARNING'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JON'));
        }
        VirtualcurrencyInstallHelper::addRow($title, $result, $info);

        // Display result about verification Magic Quotes
        $title = JText::_('COM_VIRTUALCURRENCY_MAGIC_QUOTES');
        $info  = '';
        if (get_magic_quotes_gpc()) {
            $info   = JText::_('COM_VIRTUALCURRENCY_MAGIC_QUOTES_INFO');
            $result = array('type' => 'important', 'text' => JText::_('JON'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JOFF'));
        }
        VirtualcurrencyInstallHelper::addRow($title, $result, $info);

        // Display result about verification FileInfo
        $title = JText::_('COM_VIRTUALCURRENCY_FILEINFO');
        $info  = '';
        if (!function_exists('finfo_open')) {
            $info   = JText::_('COM_VIRTUALCURRENCY_FILEINFO_INFO');
            $result = array('type' => 'important', 'text' => JText::_('JOFF'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JON'));
        }
        VirtualcurrencyInstallHelper::addRow($title, $result, $info);

        // Display result about verification PHP Intl
        $title = JText::_('COM_VIRTUALCURRENCY_PHPINTL');
        $info  = '';
        if (!extension_loaded('intl')) {
            $info   = JText::_('COM_VIRTUALCURRENCY_PHPINTL_INFO');
            $result = array('type' => 'important', 'text' => JText::_('JNO'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JYES'));
        }
        VirtualcurrencyInstallHelper::addRow($title, $result, $info);

        // Display result about verification for cURL library
        $title = JText::_('COM_VIRTUALCURRENCY_CURL_LIBRARY');
        $info  = '';
        if (!extension_loaded('curl')) {
            $info   = JText::_('COM_VIRTUALCURRENCY_PHP_CURL_INFO');
            $result = array('type' => 'important', 'text' => JText::_('JNO'));
        } else {
            $currentVersion = VirtualcurrencyInstallHelper::getCurlVersion();
            $text           = JText::sprintf('COM_VIRTUALCURRENCY_CURRENT_V_S', $currentVersion);

            if (version_compare($currentVersion, '7.34.0', '<')) {
                $info   = JText::sprintf('COM_VIRTUALCURRENCY_REQUIRES_V_S', '7.34.0+');
                $result = array('type' => 'warning', 'text' => $text);
            } else {
                $result = array('type' => 'success', 'text' => $text);
            }
        }
        VirtualcurrencyInstallHelper::addRow($title, $result, $info);

        // Display result about verification Open SSL
        $title = JText::_('COM_VIRTUALCURRENCY_OPEN_SSL');
        $info  = '';
        if (!function_exists('curl_init')) {
            $result = array('type' => 'important', 'text' => JText::_('JNO'));
        } else {
            $currentVersion = VirtualcurrencyInstallHelper::getOpenSslVersion();
            $text           = JText::sprintf('COM_VIRTUALCURRENCY_CURRENT_V_S', $currentVersion);

            if (version_compare($currentVersion, '1.0.1.3', '<')) {
                $info   = JText::sprintf('COM_VIRTUALCURRENCY_REQUIRES_V_S', '1.0.1.3+');
                $result = array('type' => 'warning', 'text' => $text);
            } else {
                $result = array('type' => 'success', 'text' => $text);
            }
        }
        VirtualcurrencyInstallHelper::addRow($title, $result, $info);

        // Display result about PHP version
        $title = JText::_('COM_VIRTUALCURRENCY_PHP_VERSION');
        $info  = '';
        if (version_compare(PHP_VERSION, '5.5.19') < 0) {
            $result = array('type' => 'important', 'text' => JText::_('COM_VIRTUALCURRENCY_WARNING'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JYES'));
        }
        VirtualcurrencyInstallHelper::addRow($title, $result, $info);

        // Display result about MySQL Version.
        $title     = JText::_('COM_VIRTUALCURRENCY_MYSQL_VERSION');
        $info      = '';
        $dbVersion = JFactory::getDbo()->getVersion();
        if (version_compare($dbVersion, '5.5.3', '<')) {
            $result = array('type' => 'important', 'text' => JText::_('COM_VIRTUALCURRENCY_WARNING'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JYES'));
        }
        VirtualcurrencyInstallHelper::addRow($title, $result, $info);

        // Display result about verification of installed ITPrism Library
        $info = '';
        if (!class_exists('Prism\\Version')) {
            $title  = JText::_('COM_VIRTUALCURRENCY_PRISM_LIBRARY');
            $info   = JText::_('COM_VIRTUALCURRENCY_PRISM_LIBRARY_DOWNLOAD');
            $result = array('type' => 'important', 'text' => JText::_('JNO'));
        } else {
            $prismVersion = new Prism\Version();
            $text         = JText::sprintf('COM_VIRTUALCURRENCY_CURRENT_V_S', $prismVersion->getShortVersion());

            if (class_exists('Virtualcurrency\\Version')) {
                $componentVersion = new Virtualcurrency\Version();
                $title            = JText::sprintf('COM_VIRTUALCURRENCY_PRISM_LIBRARY_S', $componentVersion->requiredPrismVersion);

                if (version_compare($prismVersion->getShortVersion(), $componentVersion->requiredPrismVersion, '<')) {
                    $info   = JText::_('COM_VIRTUALCURRENCY_PRISM_LIBRARY_DOWNLOAD');
                    $result = array('type' => 'warning', 'text' => $text);
                }

            } else {
                $title  = JText::_('COM_VIRTUALCURRENCY_PRISM_LIBRARY');
                $result = array('type' => 'success', 'text' => $text);
            }
        }
        VirtualcurrencyInstallHelper::addRow($title, $result, $info);

        // Installed extensions
        VirtualcurrencyInstallHelper::addRowHeading(JText::_('COM_VIRTUALCURRENCY_INSTALLED_EXTENSIONS'));

        // Virtual Currency Library
        $result = array('type' => 'success', 'text' => JText::_('COM_VIRTUALCURRENCY_INSTALLED'));
        VirtualcurrencyInstallHelper::addRow(JText::_('COM_VIRTUALCURRENCY_VIRTUALCURRENCY_LIBRARY'), $result, JText::_('COM_VIRTUALCURRENCY_LIBRARY'));

        // Virtual Currency Accounts
        $result = array('type' => 'success', 'text' => JText::_('COM_VIRTUALCURRENCY_INSTALLED'));
        VirtualcurrencyInstallHelper::addRow(JText::_('COM_VIRTUALCURRENCY_MOD_VIRTUALCURRENCYACCOUNTS'), $result, JText::_('COM_VIRTUALCURRENCY_MODULE'));

        // VirtualcurrencyPayment - PayPal
        $result = array('type' => 'success', 'text' => JText::_('COM_VIRTUALCURRENCY_INSTALLED'));
        VirtualcurrencyInstallHelper::addRow(JText::_('COM_VIRTUALCURRENCY_VIRTUALCURRENCYPAYMENT_PAYPAL'), $result, JText::_('COM_VIRTUALCURRENCY_PLUGIN'));

        // VirtualcurrencyPayment - Payment Gateway
        $result = array('type' => 'success', 'text' => JText::_('COM_VIRTUALCURRENCY_INSTALLED'));
        VirtualcurrencyInstallHelper::addRow(JText::_('COM_VIRTUALCURRENCY_VIRTUALCURRENCYPAYMENT_PAYMENTGATEWAY'), $result, JText::_('COM_VIRTUALCURRENCY_PLUGIN'));

        // User - Virtual Currency Account
        $result = array('type' => 'success', 'text' => JText::_('COM_VIRTUALCURRENCY_INSTALLED'));
        VirtualcurrencyInstallHelper::addRow(JText::_('COM_VIRTUALCURRENCY_USER_VIRTUALCURRENCYACCOUNT'), $result, JText::_('COM_VIRTUALCURRENCY_PLUGIN'));

        // End table
        VirtualcurrencyInstallHelper::endTable();

        echo JText::sprintf('COM_VIRTUALCURRENCY_MESSAGE_REVIEW_SAVE_SETTINGS', JRoute::_('index.php?option=com_virtualcurrency'));

        if (!class_exists('Prism\\Version')) {
            echo JText::_('COM_VIRTUALCURRENCY_MESSAGE_INSTALL_PRISM_LIBRARY');
        } else {
            if (class_exists('Virtualcurrency\\Version')) {
                $prismVersion     = new Prism\Version();
                $componentVersion = new Virtualcurrency\Version();
                if (version_compare($prismVersion->getShortVersion(), $componentVersion->requiredPrismVersion, '<')) {
                    echo JText::_('COM_VIRTUALCURRENCY_MESSAGE_INSTALL_PRISM_LIBRARY');
                }
            }
        }

        // Remove the files that the system does not use anymore.
//        $this->removeUnusedFiles();
    }

    private function removeUnusedFiles()
    {
        $files = array(
            '/administrator/components/com_virtualcurrency/tables/...',
            '/administrator/components/com_virtualcurrency/models/...',
            '/administrator/components/com_virtualcurrency/models/fields/...',
            '/administrator/components/com_virtualcurrency/models/forms/...',
            '/administrator/components/com_virtualcurrency/controllers/...',
        );

        $folders = array(
            '/administrator/components/com_virtualcurrency/views/realcurrency',
            '/administrator/components/com_virtualcurrency/views/realcurrencies'
        );

        foreach ($files as $file) {
            $file = JPath::clean(JPATH_ROOT . $file);

            if (JFile::exists($file)) {
                JFile::delete($file);
            }
        }
    }
}
