<?php
/**
 * Search params plugin factory
 *
 * PHP version 7
 *
 * Copyright (C) Keiji Suzuki 2013-2020.
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
 * @category VuFind2
 * @package  RecordDrivers
 * @author   Keiji Suzuki <zuki.ebetsu@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:record_drivers Wiki
 */
namespace Zuki\Search\Params;
use Interop\Container\ContainerInterface;

/**
 * Search params plugin factory
 *
 * @category VuFind
 * @package  Search
 * @author   Keiji Suzuki <zuki.ebetsu@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:record_drivers Wiki
 */
class PluginFactory extends \VuFind\ServiceManager\AbstractPluginFactory
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->defaultNamespace = 'Zuki\Search';
        $this->classSuffix = '\Params';
    }

    /**
     * Create a service for the specified name.
     *
     * @param ContainerInterface $container     Service container
     * @param string             $requestedName Name of service
     * @param array              $extras        Extra options
     *
     * @return object
     */
    public function __invoke(ContainerInterface $container, $requestedName,
        array $extras = null
    ) {
        $optionsService = preg_replace('/Params$/', 'Options', $requestedName);
        $options = $container->get(\VuFind\Search\Options\PluginManager::class)
            ->get($optionsService);
        $class = $this->getClassName($requestedName);
        $configLoader = $container->get(\VuFind\Config\PluginManager::class);
        // Clone the options instance in case caller modifies it:
        return new $class(clone $options, $configLoader, ...($extras ?: []));
    }
}