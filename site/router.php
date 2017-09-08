<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Routing class from com_virtualcurrency
 *
 * @since  3.3
 */
class VirtualcurrencyRouter extends JComponentRouterBase
{
    /**
     * Build the route for the com_content component
     *
     * @param   array &$query An array of URL arguments
     *
     * @return  array  The URL arguments to use to assemble the subsequent URL.
     *
     * @since   3.3
     */
    public function build(&$query)
    {
        $segments = array();

        // We need a menu item.  Either the one specified in the query, or the current active one if none specified
        if (empty($query['Itemid'])) {
            $menuItem      = $this->menu->getActive();
            $menuItemGiven = false;
        } else {
            $menuItem      = $this->menu->getItem($query['Itemid']);
            $menuItemGiven = true;
        }

        // Check again
        if ($menuItemGiven && $menuItem !== null && $menuItem->component !== 'com_virtualcurrency') {
            $menuItemGiven = false;
            unset($query['Itemid']);
        }

        if (isset($query['view'])) {
            $view = $query['view'];
        } else {
            // We need to have a view in the query or it is an invalid URL
            return $segments;
        }

        $mOption = empty($menuItem->query['option']) ? null : $menuItem->query['option'];
        $mView   = empty($menuItem->query['view']) ? null : $menuItem->query['view'];

        // Are we dealing with a view that is attached to a menu item?
        if (isset($view) and ($mView === $view)) {
            unset($query['view']);
            return $segments;
        }

        // Views
        if (isset($view)) {
            switch ($view) {
                case 'cart':
                    unset($query['view']);
                    break;

                case 'accounts':
                    unset($query['view']);
                    break;

                case 'transactions':
                    unset($query['view']);
                    break;

            }
        }

        // Layout
        if (isset($query['layout'])) {
            if (!empty($query['Itemid']) and isset($menuItem->query['layout'])) {
                if ($query['layout'] === $menuItem->query['layout']) {
                    unset($query['layout']);
                }
            } else {
                if ($query['layout'] === 'default') {
                    unset($query['layout']);
                }
            }
        }

        foreach ($segments as $key => $segment) {
            $segments[$key] = str_replace(':', '-', $segment);
        }

        return $segments;
    }

    /**
     * Parse the segments of a URL.
     *
     * @param   array &$segments The segments of the URL to parse.
     *
     * @return  array  The URL attributes to be used by the application.
     *
     * @since   3.3
     */
    public function parse(&$segments)
    {
        $total = count($segments);
        $vars  = array();

        foreach ($segments as $key => $segment) {
            $segments[$key] = str_replace('-', ':', $segment);
        }

        // Get the active menu item.
        $item = $this->menu->getActive();

        /*
         * Standard routing for articles.  If we don't pick up an Itemid then we get the view from the segments
         * the first segment is the view and the last segment is the id of the article or category.
         */
        if (!isset($item)) {
            $vars['view'] = $segments[0];
            $vars['id']   = $segments[$total - 1];

            return $vars;
        }

        /*
         * If there is only one segment, then it points to either an article or a category.
         * We test it first to see if it is a category.  If the id and alias match a category,
         * then we assume it is a category.  If they don't we assume it is an article
         */
        if ($total === 1) {
            $view = $segments[$total - 1];

            switch ($view) {
                case 'cart':
                    $vars['view'] = 'cart';
                    break;

                case 'accounts':
                    $vars['view'] = 'accounts';
                    break;

                case 'transactions':
                    $vars['view'] = 'transactions';
                    break;

            }

            return $vars;
        }

        return $vars;
    }
}

/**
 * Content router functions
 *
 * These functions are proxys for the new router interface
 * for old SEF extensions.
 *
 * @param   array &$query An array of URL arguments
 *
 * @return  array  The URL arguments to use to assemble the subsequent URL.
 *
 * @deprecated  4.0  Use Class based routers instead
 */
function virtualcurrencyBuildRoute(&$query)
{
    $router = new VirtualcurrencyRouter;

    return $router->build($query);
}

/**
 * Parse the segments of a URL.
 *
 * This function is a proxy for the new router interface
 * for old SEF extensions.
 *
 * @param   array $segments The segments of the URL to parse.
 *
 * @return  array  The URL attributes to be used by the application.
 *
 * @since       3.3
 * @deprecated  4.0  Use Class based routers instead
 */
function virtualcurrencyParseRoute($segments)
{
    $router = new VirtualcurrencyRouter;

    return $router->parse($segments);
}
