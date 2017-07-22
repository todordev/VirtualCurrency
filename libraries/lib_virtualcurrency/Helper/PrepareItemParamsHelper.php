<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Helper
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Helper;

use Joomla\Registry\Registry;
use Prism\Helper\HelperInterface;

/**
 * This class provides functionality to prepare an item params.
 *
 * @package      Virtualcurrency
 * @subpackage   Helper
 */
class PrepareItemParamsHelper implements HelperInterface
{
    /**
     * Prepare an item parameters.
     *
     * @param \stdClass $data
     * @param array $options
     */
    public function handle(&$data, array $options = array())
    {
        if (is_object($data)) {
            if ($data->params === null) {
                $data->params = '{}';
            }

            if (is_string($data->params) and $data->params !== '') {
                $params = new Registry;
                $params->loadString($data->params);
                $data->params = $params;
            }
        }
    }
}
