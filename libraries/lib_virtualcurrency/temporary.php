<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

JLoader::register("VirtualCurrencyTableTemporary", JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_virtualcurrency".DIRECTORY_SEPARATOR."tables".DIRECTORY_SEPARATOR."temporary.php");

/**
 * This class contains methods that are used for managing temporary table.
 * In the temporary table are saved data, 
 * which will be used during the process of completing transactions.
 *
 * @package 	 VirtualCurrency
 * @subpackage   Library
 */
class VirtualCurrencyTemporary extends VirtualCurrencyTableTemporary {
    
    /**
     * This method initializes the object.
     * 
     * <code>
     *
     *  // Prepare data
     *  $data = array(
     *      "user_id"     => 300,
     *      "currency_id" => 1,
     *      "number"      => 100
     *  );
     *  
     *  // Create an object for storing temporary data.
     *  $temporary    = new VirtualCurrencyTemporary();
     *  $temporary->bind($data);
     *  $temporary->store();
     *
     * </code>
     * 
     */
    public function __construct() {
        
        // Set database driver
        $db = JFactory::getDbo();
        parent::__construct($db);
        
    }
    
}
