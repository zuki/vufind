<?php
/**
 * Class for accessing Library of Congress Online Catalog
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
 * @package  Library of Congress Online Catalog
 * @author   Andrew S. Nagy <vufind-tech@lists.sourceforge.net>
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Zuki\Backend\Loc;

use VuFindSearch\Query\AbstractQuery;
use VuFindSearch\ParamBag;
use VuFind\XSLT\Processor as XSLTProcessor;
use VuFindSearch\Backend\Exception\HttpErrorException;
use VuFindSearch\Backend\Exception\BackendException;

/**
 * Library of Congress Online Catalog SRU Search Interface
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
            'http://lx2.loc.gov:210/LCDB', $client
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
        $params->set('query', 'rec.id='.$id);
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
        $params->set('recordSchema', 'marcxml');

        try
        {        
            $response = $this->call('GET', $params->getArrayCopy(), false);
        } catch (HttpErrorException $e) {
            throw new BackendException($e.getMessage());
        }

        // sxml: SimpleXMLELement
        $sxml = $this->process($response);

        // retrieve records: MARCXML
        $records = array();
        foreach ($sxml->record as $rec) {
            $marc = $rec->saveXML();
            $records[] = $marc;
        }

        return array(
            'docs' => $records,
            'facets' => array(),
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
     * @return SimpleXMLElement
     */
    protected function process($response) 
    {
        $result = XSLTProcessor::process('sru-loc.xsl', $response);
        if (!$result) {
            throw new BackendException(
                sprintf('Error processing LoC Online Catalog response: %20s', $response)
            );
        }

        return simplexml_load_string($result);
    }

    /**
     * Get <RecordCount> from XML
     *
     * @param DOMDocument
     *
     * @return int
     */
    private function getRecordCount($xml)
    {
        $nlist = $xml->getElementsByTagName('RecordCount');
        if ($nlist->length > 0) {
            return (int)$nlist->item(0)->nodeValue;
        } else {
            return 0;
        }
    }
    
}
