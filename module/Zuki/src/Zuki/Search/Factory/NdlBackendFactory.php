<?php

/**
 * Factory for NDLSearch backends.
 *
 * PHP version 5
 *
 * Copyright (C) Keiji Suzuki 2013.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category VuFind
 * @package  Search
 * @author   Keiji Suzuki <zuki.ebetsu@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org   Main Site
 */

namespace Zuki\Search\Factory;

use Interop\Container\ContainerInterface;

use Zuki\Backend\Ndl\Response\RecordCollectionFactory;
use Zuki\Backend\Ndl\QueryBuilder;
use Zuki\Backend\Ndl\Connector;
use Zuki\Backend\Ndl\Backend;

use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for NDLSearch backends.
 *
 * @category VuFind
 * @package  Search
 * @author   Keiji Suzuki <zuki.ebetsu@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org   Main Site
 */
class NdlBackendFactory implements FactoryInterface
{
    /**
     * Logger.
     *
     * @var Zend\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Superior service manager.
     *
     * @var ContainerInterface
     */
    protected $serviceLocator;

    /**
     * NDL configuration
     *
     * @var \Zend\Config\Config
     */
    protected $config;

    /**
     * Create service
     *
     * @param ContainerInterface $sm      Service manager
     * @param string             $name    Requested service name (unused)
     * @param array              $options Extra options (unused)
     * @return Backend
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $sm, $name, array $options = null)
    {
        $this->serviceLocator = $sm;
        $this->config = $this->serviceLocator
            ->get(\VuFind\Config\PluginManager::class)->get('Ndl');
        if ($this->serviceLocator->has(\VuFind\Log\Logger::class)) {
            $this->logger = $this->serviceLocator->get(\VuFind\Log\Logger::class);
        }
        $connector = $this->createConnector();
        $backend   = $this->createBackend($connector);
        return $backend;
    }

    /**
     * Create the WorldCat backend.
     *
     * @param Connector $connector Connector
     *
     * @return Backend
     */
    protected function createBackend(Connector $connector)
    {
        $backend = new Backend($connector, $this->createRecordCollectionFactory());
        $backend->setLogger($this->logger);
        $backend->setQueryBuilder($this->createQueryBuilder());
        return $backend;
    }

    /**
     * Create the WorldCat connector.
     *
     * @return Connector
     */
    protected function createConnector()
    {
        $client = $this->serviceLocator->get(\VuFindHttp\HttpService::class)
            ->createClient();
        $connector = new Connector($client);
        $connector->setLogger($this->logger);
        return $connector;
    }

    /**
     * Create the WorldCat query builder.
     *
     * @return QueryBuilder
     */
    protected function createQueryBuilder()
    {
        return new QueryBuilder($this->config->Providers_Code);
    }

    /**
     * Create the record collection factory
     *
     * @return RecordCollectionFactory
     */
    protected function createRecordCollectionFactory()
    {
        $manager = $this->serviceLocator->get(\VuFind\RecordDriver\PluginManager::class);
        $callback = function ($data) use ($manager) {
            $driver = $manager->get('Ndl');
            $driver->setRawData($data);
            return $driver;
        };

        return new RecordCollectionFactory($callback);
    }
}
