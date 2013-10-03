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

}

