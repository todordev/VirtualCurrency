<?php
/**
 * @package      Virtualcurrency\Payment\Session\Command
 * @subpackage   Gateway
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Payment\Session\Command\Gateway;

use Prism\Database\JoomlaDatabase;
use Virtualcurrency\Payment\Session\Service;

/**
 * Joomla database gateway.
 *
 * @package         Virtualcurrency\Payment\Session\Command
 * @subpackage      Gateway
 */
class JoomlaStoreService extends JoomlaDatabase implements StoreServiceGateway
{
    /**
     * Store data to database.
     *
     * @param Service  $service
     *
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function store(Service $service)
    {
        $primaryKey     = (int)$service->getId();
        if (!$primaryKey) {
            throw new \UnexpectedValueException('Invalid primary key of the session gateway data.');
        }

        $alias          = $service->getAlias();
        $token          = $service->getToken();
        $orderId        = $service->getOrderId();
        $gatewayData    = $service->getData();

        // Convert the gateway data to JSON format.
        if ($gatewayData) {
            $gatewayData = json_encode($gatewayData);
        } else {
            $gatewayData = 'NULL';
        }

        // Check for existing record.
        $query = $this->db->getQuery(true);
        $query
            ->select('COUNT(*)')
            ->where($this->db->quoteName('id') .'='. (int)$primaryKey)
            ->where($this->db->quoteName('alias') .'='. $this->db->quote($alias));

        $this->db->setQuery($query, 0, 1);
        $recordExists = (bool)$this->db->loadResult();

        // Prepare the query.
        $query = $this->db->getQuery(true);
        $query
            ->set($this->db->quoteName('token') . '=' . $this->db->quote($token))
            ->set($this->db->quoteName('order_id') . '=' . $this->db->quote($orderId))
            ->set($this->db->quoteName('data') . '=' . $this->db->quote($gatewayData));

        if ($recordExists) { // Update
            $query
                ->update($this->db->quoteName('#__vc_paymentsessiongateways'))
                ->where($this->db->quoteName('id') .'='. (int)$primaryKey)
                ->where($this->db->quoteName('alias') .'='. $this->db->quote($alias));
        } else {
            $query
                ->insert($this->db->quoteName('#__vc_paymentsessiongateways'))
                ->set($this->db->quoteName('alias') . '=' . $this->db->quote($alias))
                ->set($this->db->quoteName('id') . '=' . (int)$primaryKey);
        }

        $this->db->setQuery($query);
        $this->db->execute();
    }
}
