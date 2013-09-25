<?php

/**
 * NDLSearch record collection.
 *
 * PHP version 5
 *
 * Copyright (C) Keiji Suzuki 2013..
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

namespace Zuki\Backend\Ndl\Response;

use VuFindSearch\Response\AbstractRecordCollection;

/**
 * WorldCat record collection.
 *
 * @category VuFind2
 * @package  Search
 * @author   David Maus <maus@hab.de>
 * @author   Keiji Suzuki <zuki.ebetsu@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class RecordCollection extends AbstractRecordCollection
{
    /**
     * Raw response.
     *
     * @var array
     */
    protected $response;

    /**
     * Constructor.
     *
     * @param array $response WorldCat response
     *
     * @return void
     */
    public function __construct(array $response)
    {
        $this->response = $response;
        $this->rewind();
    }

    /**
     * Return total number of records found.
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->response['total'];
    }

    /**
     * Return facet information.
     *
     * @return array
     */
    public function getFacets()
    {
        return $this->response['facets'];
    }

    /**
     * Return offset in the total search result set.
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->response['offset'];
    }
}
