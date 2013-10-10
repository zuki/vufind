<?php
/**
 * NDLSearch Search Parameters
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
 * @category VuFind2
 * @package  SearchObject
 * @author   Keiji Suzuki <zuki.ebetsu@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace Zuki\Search\Ndl;

/**
 * NDLSearch Search Parameters
 *
 * @category VuFind2
 * @package  SearchObject
 * @author   Keiji Suzuki <zuki.ebetsu@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class Params extends \VuFind\Search\Base\Params
{
    /**
     * Initialize facet settings for the specified configuration sections.
     *
     * @param string $facetList     Config section containing fields to activate
     * @param string $facetSettings Config section containing related settings
     *
     * @return bool                 True if facets set, false if no settings found
     */
    protected function initFacetList($facetList, $facetSettings)
    {
        $config = $this->getServiceLocator()->get('VuFind\Config')->get('Ndl');
        if (!isset($config->$facetList)) {
            return false;
        }
        foreach ($config->$facetList as $key => $value) {
            $this->addFacet($key, $value);
        }
        if (isset($config->$facetSettings->facet_limit)
            && is_numeric($config->$facetSettings->facet_limit)
        ) {
            $this->setFacetLimit($config->$facetSettings->facet_limit);
        }
        return true;
    }

    /**
     * Initialize facet settings for the advanced search screen.
     *
     * @return void
     */
    public function initAdvancedFacets()
    {
        $this->initFacetList('Advanced', 'Advanced_Settings');
    }

    /**
     * Set Facet Limit
     *
     * @param int $l the new limit value
     *
     * @return void
     */
    public function setFacetLimit($l)
    {
        $this->facetLimit = $l;
    }
}
