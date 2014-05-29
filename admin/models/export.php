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

jimport('joomla.application.component.model');

class VirtualCurrencyModelExport extends JModelLegacy
{
    public function getCurrencies()
    {
        $db = $this->getDbo();
        /** @var $db JDatabaseMySQLi * */

        // Create a new query object.
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query
            ->select('a.id, a.title, a.abbr, a.symbol, a.position')
            ->from($db->quoteName('#__crowdf_currencies', 'a'));


        $db->setQuery($query);
        $results = $db->loadAssocList();

        $output = $this->prepareXML($results, "currencies", "currency");

        return $output;
    }

    public function getLocations()
    {
        $db = $this->getDbo();
        /** @var $db JDatabaseMySQLi * */

        // Create a new query object.
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query
            ->select(
                'a.id, a.name, a.latitude, a.longitude, a.country_code, ' .
                'a.timezone, a.state_code, a.published'
            )
            ->from($db->quoteName('#__crowdf_locations', 'a'));


        $db->setQuery($query);
        $results = $db->loadAssocList();

        $output = $this->prepareXML($results, "locations", "location");

        return $output;
    }

    public function getStates()
    {
        $db = $this->getDbo();
        /** @var $db JDatabaseMySQLi * */

        // Create a new query object.
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query
            ->select('a.id, a.name, a.state_code')
            ->from($db->quoteName('#__crowdf_locations', 'a'));

        $db->setQuery($query);
        $results = $db->loadAssocList();

        $output = $this->prepareXML($results, "states", "state");

        return $output;
    }

    public function getCountries()
    {
        $db = $this->getDbo();
        /** @var $db JDatabaseMySQLi * */

        // Create a new query object.
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query
            ->select('a.id, a.name, a.code, a.code4, a.latitude, a.longitude, a.timezone')
            ->from($db->quoteName('#__crowdf_countries', 'a'));

        $db->setQuery($query);
        $results = $db->loadAssocList();

        $output = $this->prepareXML($results, "countries", "country");

        return $output;
    }

    protected function prepareXML($results, $root, $child)
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><' . $root . '/>');
        $xml->addAttribute("generator", "com_virtualcurrency");

        if (!empty($root) and !empty($child)) {

            foreach ($results as $data) {

                $item = $xml->addChild($child);

                foreach ($data as $key => $value) {
                    $item->addChild($key, $value);
                }
            }
        }

        $dom               = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;

        return $dom->saveXML();
    }
}
