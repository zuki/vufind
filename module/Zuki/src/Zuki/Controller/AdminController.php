<?php
/**
 * Admin Controller
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
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org   Main Site
 */
namespace Zuki\Controller;

use Zuki\Tools\MARCRecord;
use Zuki\Tools\SolrRecord;

use VuFindSearch\Query\Query;
use VuFindSearch\Query\QueryGroup;
use VuFindSearch\Backend\Exception\BackendException;

/**
 * Class controls VuFind administration.
 *
 * @category VuFind2
 * @package  Controller
 * @author   Keiji Suzuki <zuki.ebetsupgmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org   Main Site
 */

class AdminController extends \VuFind\Controller\AdminController
{
    /**
     * Records maintenance
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function recordsAction()
    {
        $util = $this->params()->fromQuery('util');
        $id = $this->params()->fromQuery('id');
        if (isset($util)) {
            if ($id) {
                return $this->forwardTo('Admin', $util);
            } else {
                $this->flashMessenger()->setNamespace('error')
                    ->addMessage($this->translate(
                        'Please specify a record ID'
                    ));
            }
        }
        return $this->createViewModel();
    }

    /**
     * Support action for maintenance -- delete expired searches.
     *
     * @return mixed
     */
    public function viewrecordAction()
    {
        $id = $this->params()->fromQuery('id');
        $first = $this->getServiceLocator()->get('VuFind\Search')->retrieve('Solr', $id)->first();

        if (null === $first) {
            $this->flashMessenger()->setNamespace('error')
                ->addMessage($this->translate(
                    'The specified record does not exist.'
                ));
            $this->getRequest()->setQuery(new \Zend\Stdlib\Parameters());
            return $this->forwardTo('Admin', 'Records');
        }

        $view = $this->createViewModel();
        $view->record = $first->getRawData();
        $view->recordId = $id;

        return $view;
    }

    /**
     * Edit the specified record.
     *
     * @return void
     * @access public
     */
    public function editrecordAction()
    {
        // Read in the original record:
        $id = $this->params()->fromQuery('id');
        $record = $this->getServiceLocator()->get('VuFind\Search')->retrieve('Solr', $id)->first();
        $tool = new MARCRecord($this->getConfig(), $record);
        $html = $tool->getMarcHtml();

        $view = $this->createViewModel();
        $view->marc = $html;
        $view->id   = $id;        

        return $view;
    }
    
    /**
     * Delete the specified record.
     *
     * @return void
     * @access public
     */
    public function deleterecordAction()
    {
        // Read in the original record:
        //$id = $this->params()->fromQuery('id');
        
        $this->flashMessenger()->setNamespace('info')
                    ->addMessage($this->translate(
                        'Not implemented'
                    ));
        
        $this->getRequest()->setQuery(new \Zend\Stdlib\Parameters());
        return $this->forwardTo('Admin', 'Records');
    }
    
    /**
     * Process parameters and display the page.
     *
     * @return void
     * @access public
     */
    public function saverecordAction()
    {
        $util = $this->params()->fromPost('util');
        if (isset($util) && $util !== 'cancel') {  
            // $tags[tag][random1][code-random2] = value
            $tags = $this->params()->fromPost('tag');
            $tool = new SolrRecord($this->getConfig(), $tags);   
            switch ($util) {
                case 'editrecord':
                    $retval = $tool->writeMarc(false);
                    break;
                case 'copyrecord':
                    $retval = $tool->writeMarc(true);
                    breank;
            }
            if ($retval) {
                $this->flashMessenger()->setNamespace('info')
                    ->addMessage(sprintf('%s (%s)', $this->translate(
                        'Add a job to the Germand'
                    ) ,$retval));
            } else {
                $this->flashMessenger()->setNamespace('error')
                    ->addMessage($this->translate(
                        'Failed add job to Germand'
                    ));
            }
        } 
        
        return $this->forwardTo('Admin', 'Records');
    }

    /**
     * Process Leader
     *
     * @return void
     * @access public
     */
    public function leaderAction()
    {
        $view = $this->createViewModel();
        $view->leader_val = $this->params()->fromQuery('leader_val');      
        
        return $view;
    }    

    /**
     * Process Field 007
     *
     * @return void
     * @access public
     */
    public function field007Action()
    {
        $view = $this->createViewModel();
        $view->field_val = $this->params()->fromQuery('field_val');
        
        return $view;
    }    

    /**
     * Process Field 008
     *
     * @return void
     * @access public
     */
    public function field008Action()
    {
        $view = $this->createViewModel();
        $view->leader_val = $this->params()->fromQuery('leader_val');      
        $view->field_val = $this->params()->fromQuery('field_val');
        
        return $view;
    }    

    /**
     * Add record from NDL Opac
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function addrecordAction()
    {
        $util = $this->params()->fromQuery('util');
        $source = $this->params()->fromQuery('source');
        $field  = $this->params()->fromQuery('field');
        $value  = $this->params()->fromQuery('value');
        if (isset($util)) {
            if (empty($value) || empty($source) || empty($field)) {
                $this->flashMessenger()->setNamespace('error')
                    ->addMessage('検索元コード、検索フィールド、検索値を入力してください。');
            } elseif ($source != 'ndl' && $source != 'lc') {
                $this->flashMessenger()->setNamespace('error')
                    ->addMessage(sprintf('検索元コードが違います: %s', $source));
            } else {
                return $this->forwardTo('Admin', $util);
            }
        }
        return $this->createViewModel();
    }

    /**
     * Search NDLSearch or LC.
     *
     * @return void
     * @access public
     */
    public function searchrecordAction()
    {
        $source = $this->params()->fromQuery('source');
        $field  = $this->params()->fromQuery('field');
        $value  = $this->params()->fromQuery('value');

        $forward  = false;
        $ns      = null;
        $message = null;        
        try
        {
            if ($source == 'ndl') {
                $records = $this->getRecordsFromNDL(
                    $this->getServiceLocator()->get('VuFind\Search'), $field, $value);
            } else {
                //$result = $this->getRecordsFromLC($field, $value);
               $forward = true;
               $ns = 'info';
               $message = 'まだ実装していません';
            }
        } catch (BackendException $e) {
            $forward = true;
            $ns = 'error';
            $message = sprintsprintf('%s 検索中にエラーが発生しました: %s', 
                    $source, $e->getMessage());
        } 
        
        if (!$records) {
            $forward = true;
            $ns = 'info';
            $message = sprintf('該当レコードなし: %s=%s', $field, $value);
        }
       
        if ($forward) {
            $this->flashMessenger()->setNamespace($ns)->addMessage($message);
            $this->getRequest()->setQuery(new \Zend\Stdlib\Parameters());
            $this->forwardTo('Admin', 'AddRecord');
        }
        
        $view = $this->createViewModel();
        $view->setTemplate('admin/addrecord');
        $view->records = $records;
        $view->source = $source;
        return $view;
    }

    /**
     * Register Marc record.
     *
     * @return void
     * @access public
     */
    public function registerrecordAction()
    {
        $selected = $this->params()->fromQuery('selected');
        
        if (empty($selected)) {
            $this->getRequest()->setQuery(new \Zend\Stdlib\Parameters());
            return $this->forwardTo('Admin', 'AddRecord');
        }
        
        $shelf = $this->params()->fromQuery('shelf');
        $isbn = $this->params()->fromQuery('isbn');
        $vols = $this->params()->fromQuery('vols');
        $year = $this->params()->fromQuery('year');
        $source = $this->params()->fromQuery('source');
        
        if (empty($selected) || empty($shelf)) {
                $this->flashMessenger()->setNamespace('error')
                    ->addMessage($this->translate(
                        'レコードを指定して、書架記号を入力してください。'));
        } elseif ($source == 'ndl') {
            $client = new \GearmanClient();
            $client->addServer();
            $client->doBackground("register_marc", $selected.':'.$shelf.':'.$isbn.':'.$vols.':'.$year);
            if ($client->returnCode() == GEARMAN_SUCCESS) {
                $this->flashMessenger()->setNamespace('info')
                    ->addMessage(sprintf('Germandにジョブを登録しました: id = %s, shelf = %s, isbn = %s', $selected, $shelf, $isbn));
            } else {
                $this->flashMessenger()->setNamespace('error')
                    ->addMessage(sprintf('Germandにジョブを登録できませんでした: id = %s, shelf = %s, isbn = %s', $selected, $shelf, $isbn));
            }
      /* TODO 
        } elseif ($source == 'lc') {
            $sru = new SRU('http://lx2.loc.gov:210/LCDB');
            $record = $sru->getRecord($selected);
            if (PEAR::isError($record)) {
                $interface->assign('status', "Failed add a record: id = {$selected}, shelf = {$shelf}");
            } else {
                if (!empty($isbn)) {
                    $sfisbn[] = new File_MARC_Subfield('a', $isbn);
                    $f020 = new File_MARC_Data_Field('020', $sfisbn);
                    $record->appendField($f020);
                }
                if (!empty($vols) or !empty($year)) {
                    if (!empty($vols)) {
                        $volyear[] = new File_MARC_Subfield('a', $vols);
                    }
                    if (!empty($year)) {
			$volyear[] = new File_MARC_Subfield('i', $year);
                    }
                    $f963 = new File_MARC_Data_Field('963', $volyear, ' ', '0');
                    $record->appendField($f963);
                }
                $sfshelf[] = new File_MARC_Subfield('a', $shelf);
                $f852 = new File_MARC_Data_Field('852', $sfshelf);
                $record->appendField($f852);

                $marc_file ='/usr/local/data/marc/LC'.$selected.'.mrc'; 
                $fh = fopen($marc_file, 'w');
                fwrite($fh, $record->toRaw());
                fclose($fh);

                chgrp($marc_file, "dspace");
                chmod($marc_file, 0664);

                $client = new GearmanClient();
                $client->addServer();
                $client->doBackground("index_marc", $marc_file);
                if ($client->returnCode() == GEARMAN_SUCCESS) {
                    $interface->assign('status', "Add a job to the Germand: file = {$marc_file}");
                } else {
                    $interface->assign('status', "Failed add job to Germand: file = {$marc_file}");
                }
            }
    */
        }

        $this->getRequest()->setQuery(new \Zend\Stdlib\Parameters());
        return $this->forwardTo('Admin', 'AddRecord');
    }


    private function getRecordsFromNDL($service, $field, $value)
    {
        $queries = array();
        $queries[] = new Query($value, $field);
        $queries[] = new Query('iss-ndl-opac','dpid');
        $query = new QueryGroup('AND', $queries);
        
        $collection = $service->search('Ndl', $query, 1, 20);
        
        if ($collection->getTotal() == 0) {
            return null;
        }
        $records = array();
        foreach ($collection->getRecords() as $record) {
            $id = $record->getNDLOpacID();
            if ($id === null) {
                continue;
            }
            // $record: NDL Record Driver object
            $citation = $record->getTitle();
            if ($record->getPrimaryAuthor()) {
                $citation .=  ' / ' . $record->getPrimaryAuthor();
            }
            if ($record->getVolume()) {
                $citation .=  ', ' . $record->getVolume();
            }
            if ($record->getPubDate()) {
                $citation .=  ' (' . $record->getPubDate() . ')';
            }
            if ($record->getSeriesTitle()) {
                $citation .=  ' -- (' . $record->getSeriesTitle() . ')';
            }
            $records[] = array($id, $citation);
        }
        
        return $records;
    }

}

