<?php
/**
 * Solr Record tool
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
 * @package  Controller
 * @author   Keiji Suzuki <zuki.ebetsu@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org   Main Site
 */
namespace Zuki\Tools;

use File_MARC_Record as MARC_Record;
use File_MARC_Control_Field as Control_Field;
use File_MARC_Subfield as Subfield;
use File_MARC_Data_Field as Data_Field;

/**
 * Class controls VuFind administration.
 *
 * @category VuFind2
 * @package  Controller
 * @author   Keiji Suzuki <zuki.ebetsupgmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org   Main Site
 */

class SolrRecord
{
    /**
     * config
     *
     * @var config
     */
    private $config = null;

    /**
     * Solr Record
     *
     * @var array of Solr record tags
     */
    private $tags = null;

    /**
     * Constructor
     */
    public function __construct($config, $tags)
    {
        $this->config = $config;
        $this->tags = $tags;
    }

    /**
     * get Solr html from solr record
     *
     * @return string: sucess: id
     *                 failure: null
     *
     * @throws SRecordException
     */
    // $iscopy: true : このMARCを元に複製して新規作成
    //          false: このMARCを保存
    public function writeMarc($iscopy)
    {
        $marc = new MARC_Record();
        foreach ($this->tags as $tag => $contents) {
            if ($tag == '000') {    // Leader
                $val = $this->getControlValue($contents);
                $marc->setLeader($val);
            } else if ($tag < 10) { // Control fields
                $val = $this->getControlValue($contents);
                if ($iscopy) {
                    if ($tag == '001') {
                        $val = $this->getID();
                    } else if ($tag == '003') {
                        $val = 'ORIGINAL';
                    }
                }
                if ($tag == '005') {
                    $val = date('YmdHis', time()) . '.0';
                }
                $marc->appendField(new Control_Field($tag, $val));
            } else {                // Data fields
                foreach ($contents as $key => $values) {     // $key = random1
                    $field = $this->getField($tag, $values); // $values[code-random2] = value
                    if ($field) {
                        $marc->appendField($field);
                    }
                }
            }
        }

        $id   = $marc->getField('001')->getData();
        $f003 = $marc->getField('003')->getData();
        if ($f003 === 'JTNDL') {
            $id_prefix = 'JP';
        } else if ($f003 === 'ORIGINAL') {
            $id_prefix = 'OR';
        } else {
            $id_prefix = 'LC';
        }
        
        $marc_file = $this->config->MARC->Directory.$id_prefix.$id.'.mrc';
        $fh = fopen($marc_file, 'w');
        fwrite($fh, $marc->toRaw());
        fclose($fh);

        chmod($marc_file, 0664);

        $client = new \GearmanClient();
        $client->addServer();
        if ($id_prefix === 'LC') {
            $client->doBackground("index_marc", $marc_file);
        } else {
            $client->doBackground("index_original", $marc_file);
        }

        $retval = $id_prefix.$id; 
        if ($client->returnCode() !== GEARMAN_SUCCESS) {
            $retval = null;
        }
        
        return $retval;
    }

    // $obj[random1][@-random2] = value
    private function getControlValue($obj) {
        try {
            foreach ($obj as $k1 => $v1) {         // $k1: random1
                foreach ($v1 as $k2 => $val) {     // $k2: @-random2
                    return $val;
                }
            }
        } catch (Exception $e) {
            // do nothing
        }
        return null;
    }

    // Create ID for a copy record
    private function getID() 
    {
        $y2012 = date_create('2012-1-1');
        $mtime = time() - date_timestamp_get($y2012);
        return sprintf("%010d", $mtime);
    }

    // $obj['ind1/2'] = value
    // $obj[sf_code-random2] = value
    private function getField($tag, $obj) 
    {
        $ind1 = $ind2 = " ";
        $field = null;
        $subfields = array();
        foreach ($obj as $code => $val) {
            if ($code === 'ind1') {
                $ind1 = ($val === "" ? " " : $val);
            } else if ($code === 'ind2') {
                $ind2 = ($val === "" ? " " : $val);
            } else {
                $pos = strpos($code, '-');
                if ($pos !== false) {
                    $code = substr($code, 0, $pos);
                    if ($val) {                  // 入力のあったサブフィールドだけ保存
                        $subfields[] = new Subfield($code, $val);
                    }
                }
            }
        }
        // 入力のあったサブフィールドを持つフィールドだけ保存
        if ($subfields) {
            $field = new Data_Field($tag, $subfields, $ind1, $ind2);
        }
        return $field;
    } 
}    
