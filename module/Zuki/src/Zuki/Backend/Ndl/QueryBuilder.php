<?php

/**
 * NDLSearch QueryBuilder.
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
 * @package  Search
 * @author   Keiji Suzuki <zuki.ebetsu@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */

namespace Zuki\Backend\Ndl;

use VuFindSearch\ParamBag;
use VuFindSearch\Query\AbstractQuery;
use VuFindSearch\Query\QueryGroup;
use VuFindSearch\Query\Query;

/**
 * NDLSearch QueryBuilder.
 *
 * @category VuFind
 * @package  Search
 * @author   Keiji Suzuki <zuki.ebetsu@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class QueryBuilder
{
    /**
     * conversionList(Araay: provider id => provider code)
     *
     * @var string
     */
    protected $conversionList = array();

    /// Public API

    /**
     * Constructor
     *
     * @param array $Providers_Code List for converting code to name
     */
    public function __construct($Providers_Code = null)
    {
        if (null !== $Providers_Code && count($Providers_Code) > 0) {
            foreach ($Providers_Code as $key => $val) {
                $this->conversionList[$key] = $val;
            }
        }
    }

    /**
     * Return Ndl search parameters based on a user query and params.
     *
     * @param AbstractQuery $query User query
     *
     * @return ParamBag
     */
    public function build(AbstractQuery $query, ParamBag $bag = null)
    {
        // Build base query
        $queryStr = $this->abstractQueryToString($query);

        // Process filters
        if (null !== $bag) {
            $filters = $bag->get('filter');
            if (null !== $filters && is_array($filters) && count($filters) > 0) {
                $not_set_mtype = true;
                foreach ($filters as $key => $value) {
                    if (is_array($value)) {
                        $value = $value[0];
                    }
                    switch($key) {
                        case 'REPOSITORY_NO':
                            $queryStr .= ' AND dpid=' . $this->conversionList[$value];
                            break;
                        case 'NDC':
                            $queryStr .= ' AND ndc=' . $value;
                            break;
                        case 'ISSUED_DATE':
                            if (preg_match('/\[(\d+) TO (\d+)\]/', $value, $matches)) {
                                $from  = $matches[1] . '-01-01';
                                $until = $matches[2] . '-12-31';
                                $queryStr .= ' AND from=' . $from . ' AND until=' . $until;
                            }
                            break;
                        case 'mediatype1':
                        case 'mediatype2':
                        case 'mediatype6':
                            if ($not_set_mtype) {
                                $queryStr .= ' AND mediatype=' .$value;
                                $not_set_mtype = false;
                            }
                            break;
                    }
                }
            }
        }
        // Send back results
        $params = new ParamBag();
        $params->set('query', $queryStr);
        return $params;
    }

    /// Internal API

    /**
     * Convert an AbstractQuery object to a query string.
     *
     * @param AbstractQuery $query Query to convert
     *
     * @return string
     */
    protected function abstractQueryToString(AbstractQuery $query)
    {
        if ($query instanceof Query) {
            return $this->queryToString($query);
        } else {
            return $this->queryGroupToString($query);
        }
    }

    /**
     * Convert a QueryGroup object to a query string.
     *
     * @param QueryGroup $query QueryGroup to convert
     *
     * @return string
     */
    protected function queryGroupToString(QueryGroup $query)
    {
        $groups = $excludes = array();

        foreach ($query->getQueries() as $params) {
            // Advanced Search
            if ($params instanceof QueryGroup) {
                $thisGroup = array();
                // Process each search group
                foreach ($params->getQueries() as $group) {
                    // Build this group individually as a basic search
                    $thisGroup[] = $this->abstractQueryToString($group);
                }
                // Is this an exclusion (NOT) group or a normal group?
                if ($params->isNegated()) {
                    $excludes[] = join(" OR ", $thisGroup);
                } else {
                    $groups[]
                        = join(" ".$params->getOperator()." ", $thisGroup);
                }
            } else {
                // Basic Search
                $groups[] = $this->queryToString($params);
            }
        }

        // Put our advanced search together
        $queryStr = '';
        if (count($groups) > 0) {
            $queryStr
                = '(' . join(") " . $query->getOperator() . " (", $groups) . ')';
        }
        /*
        // and concatenate exclusion after that
        if (count($excludes) > 0) {
            $queryStr .= " NOT ((" . join(") OR (", $excludes) . "))";
        }
        */

        return $queryStr;
    }

    /**
     * Convert a single Query object to a query string.
     *
     * @param Query $query Query to convert
     *
     * @return string
     */
    protected function queryToString(Query $query)
    {
        $exacts = array('jpno', 'itemno', 'publisher', 'ndc', 'isbn', 'issn', 'dpid');
        // Clean and validate input:
        $field = $query->getHandler();
        if (empty($field)) {
            $field = 'anywhere';
        }
        $lookfor = str_replace('"', '', $query->getString());

        if (in_array($field, $exacts)) {
            $queryStr = $field . '="' . $lookfor . '"';
        } else {
            $queryStr = $field . ' all "' . $lookfor . '"';
        }

        return $queryStr;
    }


}
