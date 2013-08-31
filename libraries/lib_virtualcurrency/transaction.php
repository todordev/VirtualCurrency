<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

JLoader::register("VirtualCurrencyTableTransaction", JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_virtualcurrency".DIRECTORY_SEPARATOR."tables".DIRECTORY_SEPARATOR."transaction.php");

/**
 * This class contains methods that are used for managing transactions.
 *
 * @package 	 VirtualCurrency
 * @subpackage   Library 
 */
class VirtualCurrencyTransaction extends VirtualCurrencyTableTransaction {
    
    /**
     * This method initializes the object.
     *
     * <code>
     *
     *  // Prepare data
     *  $data = array(
     *      "number"        => 100,
     *      "txn_id"        => TXN0J09290U2,
     *      "txn_amount"    => "10.0",
     *      "txn_currency"  => "USD",
     *      "txn_status"    => "completed",
     *      "txn_date"      => "2013-08-18 20:46:16",
     *      "currency_id"   => 1,
     *      "seneder_id"    => 200,
     *      "receiver_id"   => 300,
     *      "service_provider"      => "PayPal"
     *  );
     *
     *  // Create an object and store transaction data.
     *  $temporary    = new VirtualCurrencyTransaction();
     *  $temporary->bind($data);
     *  $temporary->store();
     *
     * </code>
     *
     */
    public function __construct() {
        
        $db = JFactory::getDbo();
        parent::__construct($db);
        
    }
    
}
