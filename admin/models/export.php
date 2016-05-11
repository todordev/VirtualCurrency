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

class VirtualcurrencyModelExport extends JModelLegacy
{
    public function getCurrencies()
    {
        $db = $this->getDbo();
        /** @var $db JDatabaseDriver */

        // Create a new query object.
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query
            ->select('a.id, a.title, a.code, a.symbol, a.position')
            ->from($db->quoteName('#__vc_realcurrencies', 'a'));


        $db->setQuery($query);
        $results = $db->loadAssocList();

        $output = $this->prepareXML($results, 'currencies', 'currency');

        return $output;
    }

    protected function prepareXML($results, $root, $child)
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><' . $root . '/>');
        $xml->addAttribute('generator', 'com_virtualcurrency');

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
