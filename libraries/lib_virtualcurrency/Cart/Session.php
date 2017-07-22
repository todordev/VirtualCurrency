<?php
/**
 * @package         Virtualcurrency/Cart
 * @subpackage      Helper
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Cart;

use Joomla\Data\DataObject;

/**
 * Cart session class.
 *
 * @package         Virtualcurrency/Cart
 * @subpackage      Helper
 */
class Session
{
    protected $item_id = 0;
    protected $item_type = '';
    protected $items_number = 0.0;
    protected $step1 = false;
    protected $step2 = false;
    protected $session_id = '';

    public function __construct(DataObject $data)
    {
        $this->item_id      = (int)$data->item_id;
        $this->item_type    = (string)$data->item_type;
        $this->items_number = (float)$data->items_number;
        $this->step1        = (bool)$data->step1;
        $this->step2        = (bool)$data->step2;
        $this->session_id   = (string)$data->session_id;
    }

    /**
     * @return int
     */
    public function getItemId()
    {
        return (int)$this->item_id;
    }

    /**
     * @return string
     */
    public function getItemType()
    {
        return (string)$this->item_type;
    }

    /**
     * @return float
     */
    public function getItemsNumber()
    {
        return (float)$this->items_number;
    }

    /**
     * @return bool
     */
    public function getStep1()
    {
        return (bool)$this->step1;
    }

    /**
     * @return bool
     */
    public function getStep2()
    {
        return (bool)$this->step2;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return (string)$this->session_id;
    }
}
