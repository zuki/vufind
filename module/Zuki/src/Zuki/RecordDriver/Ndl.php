<?php
/**
 * Model for Solr records in NDL.
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
 * @package  RecordDrivers
 * @author   Keiji Suzuki <zuki.ebetsu@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:record_drivers Wiki
 */
namespace Zuki\RecordDriver;

/**
 * Model for Solr records in NDLSearch.
 *
 * @category VuFind2
 * @package  RecordDrivers
 * @author   Keiji Suzuki <zuki.ebetsu@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:record_drivers Wiki
 */
class Ndl extends \Zuki\RecordDriver\SolrDefault
{
    /**
     * Map table of repository number to prover ID
     *
     * @var Array
     */

    protected $providers = array();

    /**
     * Constructor
     *
     * @param \Zend\Config $mainConfig VuFind main configuration
     * @param \Zend\Config $recordConfig Record-sepcific configuration file
     */
    public function __construct($mainConfig = null, $recordConfig = null)
    {
        if (isset($recordConfig->Providers_Code)) {
            foreach ($recordConfig->Providers_Code as $key => $value) {
                $this->providers[$key] = $value;
            }
        }

        parent::__construct($mainConfig, $recordConfig);
    }

    /**
     * Set raw data to initialize the object.
     *
     * @param mixed $data Raw data representing the record; Record Model
     * objects are normally constructed by Record Driver objects using data
     * passed in from a Search Results object.  In this case, $data is a MARCXML
     * document.
     *
     * @return void
     */
    public function setRawData($data)
    {
        $this->fields = $this->sxml2array($data);
    }

    /**
     * Get an array of all ISBNs associated with the record (may be empty).
     *
     * @return array
     */
    public function getISBNs()
    {
        $isbns = array();
        foreach (array('ISBN', 'ErrorISBN', 'ISBN13') as $tag) {
            $isbns = array_merge($isbns, $this->getField($tag));
        }
        return $isbns;
    }

    /**
     * Get an array of all ISSNs associated with the record (may be empty).
     *
     * @return array
     */
    public function getISSNs()
    {
        $issns = array();
        foreach (array('ISSN', 'ISSNL', 'IncorrectISBN', 'IncorrectISSNL') as $tag) {
            $issns = array_merge($issns, $this->getField($tag));
        }
        return $issns;
    }

    /**
     * Get an array of all the formats associated with the record.
     *
     * @return array
     */
    public function getFormats()
    {
        return $this->getField('materialType', true);     
    }

    /**
     * Return the unique identifier of this record within the Solr index;
     * useful for retrieving additional information (like tags and user
     * comments) from the external MySQL database.
     *
     * @return string Unique identifier.
     */
    public function getUniqueID()
    {
        $uri = $this->fields['URI'];
        $pos = strrpos($uri, '/');
        return substr($uri, $pos+1);
    }

    /**
     * Get text that can be displayed to represent this record in
     * breadcrumbs.
     *
     * @return string Breadcrumb text to represent this record.
     */
    public function getBreadcrumb()
    {
        return $this->getShortTitle();
    }

    /**
     * Get the call number associated with the record (empty string if none).
     * If both LC and Dewey call numbers exist, LC will be favored.
     *
     * @return string
     */
    public function getCallNumber()
    {
        return "";
    }

    /**
     * Get the Dewey call number associated with this record (empty string if none).
     *
     * @return string
     */
    public function getDeweyCallNumber()
    {
        return "";
    }

    /**
     * Get the main author of the record.
     *
     * @return string
     */
    public function getPrimaryAuthor()
    {
        $authors = $this->getAuthors();
        return count($authors) > 0 ? $authors[0] : "";
    }

    /**
     * Get an array of all the languages associated with the record.
     *
     * @return array
     */
    public function getAuthors()
    {
        return $this->getField('creator');
    }

    /**
     * Get an array of all the languages associated with the record.
     *
     * @return array
     */
    public function getLanguages()
    {
        return $this->getField('language', true);
    }

    /**
     * Get the full title of the record.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->fields['title'];
    }

    /**
     * Get a full title for the record.
     *
     * @return string
     */
    public function getFullTitle()
    {
        $title = $this->getTitle();
        $authors = $this->getAuthors();
        if ($authors) {
            $title .= ' / ' . implode(' ; ', $authors);
        }  
        return $title;
    }

    /**
     * Get the short (pre-subtitle) title of the record.
     *
     * @return string
     */
    public function getShortTitle()
    {
        $title = $this->getTitle();
        $pos = mb_strpos($title, ' : ');
        if ($pos !== false && $pos > 0) {
            $title = mb_substr($title, 0, $pos);
        }
        return $title;
    }

    /**
     * Get the subtitle of the record.
     *
     * @return string
     */
    public function getSubtitle()
    {
        $title = $this->getTitle();
        $pos = mb_strpos($title, ' : ');
        if ($pos !== false) {
            $title = mb_substr($title, $pos+3);
        }
        return $title;
    }

    /**
     * Get the publishers of the record.
     *
     * @return array
     */
    public function getPublishers()
    {
        return $this->getField('publisher');
    }

    /**
     * Get the publication dates of the record.  See also getDateSpan().
     *
     * @return array
     */
    public function getPublicationDates()
    {
        return $this->getField('issued');
    }

    /**
     * Get an array of all secondary authors (complementing getPrimaryAuthor()).
     *
     * @return array
     */
    public function getSecondaryAuthors()
    {
        $authors = $this->getAuthors();
        if (count($authors) > 1) {
            $first = array_shift($authors);
            return $authors;
        } else {
            return array();
        }
    }

    /**
     * Get an URI of the original NDL Search page.
     *
     * @return array
     */
    public function getNDLSearchURI()
    {
        return $this->getField('URI');
    }

    /**
     * Get an provider.
     *
     * @return array of array(name, uri) of providers
     */
    public function getProviders()
    {
        $dps = array();
        $seealso   = $this->getField('seeAlso');
        $uri    = $this->getField('URI');
        if (count($seealso) > 0 && count($seealso) == count($uri)) {
            for ($i=0; $i<count($seealso);$i++) {
                $id = array_pop(explode('/', $uri[$i]));
                $pos = strpos($id, '-');
                if ($pos) {
                    $dps[] = array($this->providers[substr($id, 0, $pos)], $seealso[$i]);
                }
            }
        }
        return $dps;
    }

    /**
     * Get an array of the related uris.
     *
     * @return array
     */
    public function getOriginalURI()
    {
        return $this->fields['sameAs'];
    }

    /**
     * Returns an associative array (action => description) of record tabs supported
     * by the data.
     *
     * @return array
     */
    public function getTabs()
    {
        // Turn on all tabs by default:
        $allTabs = array(
            'Description' => 'Description',
            'TOC' => 'Table of Contents',
            'UserComments' => 'Comments',
            'Reviews' => 'Reviews',
            'Excerpt' => 'Excerpt',
            'Details' => 'Staff View'
        );

        // No reviews or excerpts without ISBNs/appropriate configuration:
        $isbns = $this->getISBNs();
        if (empty($isbns)) {
            unset($allTabs['Reviews']);
            unset($allTabs['Excerpt']);
        } else {
            $config = VF_Config_Reader::getConfig();
            if (!isset($config->Content->reviews)) {
                unset($allTabs['Reviews']);
            }
            if (!isset($config->Content->excerpts)) {
                unset($allTabs['Excerpt']);
            }
        }

        // No Table of Contents tab if no data available:
        $toc = $this->getTOC();
        if (empty($toc)) {
            unset($allTabs['TOC']);
        }

        return $allTabs;
    }

    /**
     * Convert SimpleXMLElement to Array
     *
     * @return array
     */
    private function sxml2array($sxml) {
    
        $fields = array();
        foreach ($sxml->children() as $element) {
            $key = $element->getName();
            $type = $resource = '';
            if ($element['type']) {
                $type = (String)$element['type'];
                $pos = strpos($type, ':');
                $type = substr($type, $pos+1);
            } elseif ($element['resource']) {
                $resource = (String)$element['resource'];
            }
            $tag = ($type && in_array($key, array('identifier', 'subject', 'spatial'))) ? $type : $key;
            $value = $resource ? $resource : (String)$element;
            if (isset($fields[$tag])) {
                if (is_array($fields[$tag])) {
                    $fields[$tag][] = $value;
                } else {
                    $tmp = $fields[$tag];
                    $fields[$tag] = array($tmp, $value);
	        }
            } else {
                $fields[$tag] = $value;
            }
        }
        return $fields;
    }

    private function getField($tag, $uniq=false) {    
        $field = array();
        if (isset($this->fields[$tag])) {
            if (is_array($this->fields[$tag])) {
                $field = array_merge($field, $this->fields[$tag]);
            } else {
                $field[] = $this->fields[$tag];
            }
        }
        return $uniq ? array_unique($field) : $field;
    }

}
