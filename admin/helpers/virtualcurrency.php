<?php
/**
 * @package      ITPrism Components
 * @subpackage   Virtual Currency
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Virtual Currency is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

/**
 * It is Virtual Currency helper class
 */
class VirtualCurrencyHelper {
	
    static $currency   = null;
    static $extension  = "com_virtualcurrency";
      
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 * @since	1.6
	 */
	public static function addSubmenu($vName = 'dashboard') {
	    
	    JSubMenuHelper::addEntry(
			JText::_('COM_VIRTUALCURRENCY_DASHBOARD'),
			'index.php?option='.self::$extension.'&view=dashboard',
			$vName == 'dashboard'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_VIRTUALCURRENCY_CURRENCIES'),
			'index.php?option='.self::$extension.'&view=currencies',
			$vName == 'currencies'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_VIRTUALCURRENCY_ACCOUNTS'),
			'index.php?option='.self::$extension.'&view=accounts',
			$vName == 'accounts'
		);
		
		/*JSubMenuHelper::addEntry(
			JText::_('COM_VIRTUALCURRENCY_PARTNERS'),
			'index.php?option='.self::$extension.'&view=partners',
			$vName == 'partners'
		);*/
		
		JSubMenuHelper::addEntry(
			JText::_('COM_VIRTUALCURRENCY_TRANSACTIONS'),
			'index.php?option='.self::$extension.'&view=transactions',
			$vName == 'transactions'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_VIRTUALCURRENCY_PLUGINS'),
			'index.php?option=com_plugins&view=plugins&filter_search='.rawurlencode("virtual currency"),
			$vName == 'plugins'
		);
		
	}
	
}