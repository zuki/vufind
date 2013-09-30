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
        if (isset($util)) {
            return $this->forwardTo('Admin', 'ViewRecord');
        } else {
            return $this->createViewModel();
        }
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
}

