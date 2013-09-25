<?php
/**
 * Class for accessing NDLSearch API
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
 * @package  WorldCat
 * @author   Andrew S. Nagy <vufind-tech@lists.sourceforge.net>
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Zuki\Backend\Ndl;

use VuFindSearch\Query\AbstractQuery;
use VuFindSearch\ParamBag;
use VuFind\XSLT\Processor as XSLTProcessor;

/**
 * NDLSearch SRU Search Interface
 *
 * @category VuFind2
 * @package  Ndl
 * @author   Keiji Suzuki
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 */
class Connector extends \VuFindSearch\Backend\SRU\Connector
{
    /**
     * Constructor
     *
     * @param \Zend\Http\Client $client     An HTTP client object
     */
    public function __construct(\Zend\Http\Client $client)
    {
        parent::__construct(
            'http://iss.ndl.go.jp/api/sru', $client
        );
    }

    /**
     * Retrieve a specific record.
     *
     * @param string   $id     Record ID to retrieve
     * @param ParamBag $params Parameters
     *
     * @throws \Exception
     * @return SimpleXMLElement record
     */
    public function getRecord($id, ParamBag $params = null)
    {
        if (null === $params) {
            $params = new ParamBag();
        }
        $params->set('query', 'itemno='.$id);
        return $this->search($params, 1, 1);
    }

    /**
     * Execute a search.
     *
     * @param ParamBag $params Parameters
     * @param integer  $offset Search offset
     * @param integer  $limit  Search limit
     *
     * @return RecordCollectionInterface
     */
    public function search(ParamBag $params, $offset, $limit)
    {
        $params->set('operation', 'searchRetrieve');
        $params->set('startRecord', $offset);
        $params->set('maximumRecords', $limit);
        $params->set('recordPacking', 'xml');
        $params->set('recordSchema', 'dcndl_simple');
        
        $response = $this->call('GET', $params->getArrayCopy(), false);

        // xml: SimpleXMLElement
        $sxml = $this->process($response);

        // retrieve records
        $records = array();
        foreach ($sxml->record as $rec) {
            $records[] = $rec;
        }

        // retrieve facets
        $facets = array();
        foreach ($sxml->Facets->Cluster as $facet) {
            $facets[] = $facet;
        }
    
        return array(
            'docs' => $records,
            'facets' => $facets,
            'offset' => $offset,
            'total' => isset($sxml->RecordCount) ? (int)$sxml->RecordCount : 0
        );
    }

    /**
     * Process an SRU response.  Returns either the raw XML string or a
     * SimpleXMLElement based on the contents of the class' raw property.
     *
     * @param string $response SRU response
     *
     * @return string|SimpleXMLElement
     */
    protected function process($result) 
    {
        // Convert facets string to xml
        $pos1 = mb_strpos($result, '<extraResponseData>');
        $pos2 = mb_strpos($result, '</extraResponseData>');
        if ($pos1 && $pos2 && ($pos2 - $pos1) > 19) {
            $result = mb_substr($result, 0, $pos1+19)
                    . htmlspecialchars_decode(mb_substr($result, $pos1+19, ($pos2 - $pos1)-19))
                    . mb_substr($result, $pos2);
        }
        $result = XSLTProcessor::process('sru-dcndl-simple.xsl', $result);
        return simplexml_load_string($result);
    }

}
