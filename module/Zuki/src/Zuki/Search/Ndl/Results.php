<?php
/**
 * NDLSearch Search Results
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

use VuFindSearch\ParamBag;

/**
 * NDLSearch Search Parameters
 *
 * @category VuFind2
 * @package  SearchObject
 * @author   Keiji Suzuki <zuki.ebetsu@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class Results extends \VuFind\Search\Base\Results
{
    /**
     * Facet details:
     *
     * @var array
     */
    protected $responseFacets = null;

    /**
     * Constructor
     *
     * @param \VuFind\Search\Base\Params $params Object representing user search
     * parameters.
     */
    public function __construct(Params $params)
    {
        parent::__construct($params);
    }

    /**
     * Support method for performAndProcessSearch -- perform a search based on the
     * parameters passed to the object.
     *
     * @return void
     */
    protected function performSearch()
    {
        $query  = $this->getParams()->getQuery();
        $limit  = $this->getParams()->getLimit();
        $offset = $this->getStartRecord();
        $params = new ParamBag();
        $params->add('filter', $this->getParams()->getFilters());
        $collection = $this->getSearchService()
            ->search('Ndl', $query, $offset, $limit, $params);

        $this->resultTotal = $collection->getTotal();
        $this->results = $collection->getRecords();
        $this->responseFacets = $collection->getFacets();
    }

    /**
     * Returns the stored list of facets for the last search
     *
     * @param array $filter Array of field => on-screen description listing
     * all of the desired facet fields; set to null to get all configured values.
     *
     * @return array        Facets data arrays
     */
    public function getFacetList($filter = null)
    {
        // Make sure we have processed the search before proceeding:
        if (null === $this->responseFacets) {
            $this->performAndProcessSearch();
        }

        // If there is no filter, we'll use all facets as the filter:
        if (is_null($filter)) {
            $filter = $this->getParams()->getFacetConfig();
        }

        // Start building the facet list:
        $list = array();

        // If we have no facets to process, give up now
        if (!isset($this->responseFacets)) {
            return $list;
        }

        $conversionList = $this->getParams()->getOptions()->getConversionList();

        // Loop through every field returned by the result set
        $validFields = array_keys($filter);
        foreach ($this->responseFacets as $xfacet) {
            //$facet = simplexml_load_string($xfacet);
            $facet = $xfacet;
            $field = (String)$facet['name'];
            // Skip filtered fields and empty arrays:
            if (!in_array($field, $validFields) || $facet->count() < 1) {
                continue;
            }
            // Initialize the settings for the current field
            $list[$field] = array();
            // Add the on-screen label
            $list[$field]['label'] = $filter[$field];
            // Build our array of values for this field
            $list[$field]['list']  = array();
            // Should we translate values for the current facet?
            $translate = in_array($field, $this->getParams()->getOptions()->getTranslatedFacets());
            $convert   = in_array($field, $this->getParams()->getOptions()->getConvertedFacets());
            // Loop through values:
            foreach ($facet->children() as $item) {
                $item_str = (String)$item;
                // Initialize the array of data about the current facet:
                $currentSettings = array();
                $currentSettings['value'] = $item_str;
                $displayText = $convert ? $conversionList[$item_str] : $item_str;
                $currentSettings['displayText']
                    = $translate ? $this->params->getOptions()->translate($displayText) : $displayText;
                $currentSettings['count'] = intval($item['count']);
                $currentSettings['isApplied']
                    = $this->params->hasFilter("$field:".$item_str);

                // Store the collected values:
                $list[$field]['list'][] = $currentSettings;
            }
        }
        return $list;
    }
}
