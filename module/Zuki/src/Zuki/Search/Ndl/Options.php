<?php
/**
 * NDLSearch Search Options
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
 * @package  Search_Ndl
 * @author   Keiji Suzuki <zuki.ebetsu@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace Zuki\Search\Ndl;

/**
 * NDLSearch Search Options
 *
 * @category VuFind2
 * @package  SearchObject
 * @author   Keiji Suzuki <zuki.ebetsu@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class Options extends \VuFind\Search\Base\Options
{
    
    /**
     * Map table of repository number to provider ID
     *
     * @var Array
     */
    protected $conversionList = array();
    
    
    /**
     * List of converted facets
     *
     * @var Array
     */
    protected $convertedFacets = array();

    /**
     * Constructor
     *
     * @param \VuFind\Config\PluginManager $configLoader Config loader
     */
    public function __construct(\VuFind\Config\PluginManager $configLoader)
    {
        parent::__construct($configLoader);
        $this->searchIni = $this->facetsIni = 'Ndl';

        // Load the configuration file:
        $searchSettings = $configLoader->get($this->searchIni);

        if (isset($searchSettings->General->default_limit)) {
            $this->defaultLimit = $searchSettings->General->default_limit;
        }

        // Search handler setup:
        $this->defaultHandler = 'anywhere';
        if (isset($searchSettings->Basic_Searches)) {
            foreach ($searchSettings->Basic_Searches as $key => $value) {
                $this->basicHandlers[$key] = $value;
            }
        }
        if (isset($searchSettings->Advanced_Searches)) {
            foreach ($searchSettings->Advanced_Searches as $key => $value) {
                $this->advancedHandlers[$key] = $value;
            }
        }

        // Load sort preferences:
        if (isset($searchSettings->Sorting)) {
            foreach ($searchSettings->Sorting as $key => $value) {
                $this->sortOptions[$key] = $value;
            }
        }
        if (isset($searchSettings->General->default_sort)) {
            $this->defaultSort = $searchSettings->General->default_sort;
        }
        if (isset($searchSettings->DefaultSortingByType)
            && count($searchSettings->DefaultSortingByType) > 0
        ) {
            foreach ($searchSettings->DefaultSortingByType as $key => $val) {
                $this->defaultSortByHandler[$key] = $val;
            }
        }
        
        if (isset($searchSettings->Providers_Code)
            && count($searchSettings->Providers_Code) > 0
        ) {
            foreach ($searchSettings->Providers_Code as $key => $val) {
                $this->conversionList[$key] = $val;
            }
        }
        
        $this->translatedFacets = array('REPOSITORY_NO', 'NDC');
        $this->convertedFacets = array('REPOSITORY_NO', 'NDC');
    }

    /**
     * Return an array describing the action used for rendering search results
     * (same format as expected by the URL view helper).
     *
     * @return array
     */
    public function getSearchAction()
    {
        return 'ndl-search';
    }

    /**
     * Return an array describing the action used for performing advanced searches
     * (same format as expected by the URL view helper).  Return false if the feature
     * is not supported.
     *
     * @return array|bool
     */
    public function getAdvancedSearchAction()
    {
        return 'ndl-advanced';
    }

    /**
     * Return the List of facets
     *
     * @return Array
     */
    public function getConvertedFacets()
    {
        return $this->convertedFacets;
    }

    /**
     * Return the Map table of repository number to provider ID.
     *
     * @return Array
     */
    public function getConversionList()
    {
        return $this->conversionList;
    }
}
