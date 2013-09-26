<?php
namespace Zuki\Module\Configuration;

$config = array(
    'vufind' => array(
        'plugin_managers' => array(
            'ils_driver' => array(
                'factories' => array(
                    'mylibrary' => function ($sm) {
                        return new \Zuki\ILS\Driver\MyLibrary(
                            $sm->getServiceLocator()->get('VuFind\Search')
                        );
                    },
                ),
            ),
            'search_backend' => array(
                'factories' => array(
                    'Ndl' => 'Zuki\Search\Factory\NdlBackendFactory',
                ),
            ),
            'search_options' => array(
                'factories' => array(
                    'Ndl' => function ($sm) {
                        return new \Zuki\Search\Ndl\Options(
                            $sm->getServiceLocator()->get('VuFind\Config')
                        );
                    },
                ),
            ),
            'search_params' => array(
                'factories' => array(
                    'Ndl' => function ($sm) {
                        $options = $sm->getServiceLocator()
                                      ->get('VuFind\SearchOptionsPluginManager')
                                      ->get('Ndl');
                        return new \Zuki\Search\Ndl\Params(
                            clone($options),
                            $sm->getServiceLocator()->get('VuFind\Config')
                        );
                    },
                ),
            ),
            'search_results' => array(
                'factories' => array(
                    'Ndl' => function ($sm) {
                        $params = $sm->getServiceLocator()
                                     ->get('VuFind\SearchParamsPluginManager')
                                     ->get('Ndl');
                        return new \Zuki\Search\Ndl\Results($params);
                    },
                ),
            ),
            'recorddriver' => array(
                'factories' => array(
                    'solrdefault' => function ($sm) {
                        return new \Zuki\RecordDriver\SolrDefault(
                            $sm->getServiceLocator()->get('VuFind\Config')->get('config'),
                            null,
                            $sm->getServiceLocator()->get('VuFind\Config')->get('searches')
                        );
                    },
                    'solrmarc' => function ($sm) {
                        $driver = new \Zuki\RecordDriver\SolrMarc(
                            $sm->getServiceLocator()->get('VuFind\Config')->get('config'),
                            null,
                            $sm->getServiceLocator()->get('VuFind\Config')->get('searches')
                        );
                        $driver->attachILS(
                            $sm->getServiceLocator()->get('VuFind\ILSConnection'),
                            $sm->getServiceLocator()->get('VuFind\ILSHoldLogic'),
                            $sm->getServiceLocator()->get('VuFind\ILSTitleHoldLogic')
                        );
                        return $driver;
                    },
                    'Ndl' => function ($sm) {
                        return new \Zuki\RecordDriver\Ndl(
                            $sm->getServiceLocator()->get('VuFind\Config')->get('config'),
                            $sm->getServiceLocator()->get('VuFind\Config')->get('Zuki')
                        );
                    },
                ),
            ),
        ),
        'recorddriver_tabs' => array(
            'Zuki\RecordDriver\SolrDefault' => array(
                'tabs' => array (
                    'Holdings' => 'HoldingsILS', 'Description' => 'Description',
                    'TOC' => 'TOC', 'UserComments' => 'UserComments',
                    'Reviews' => 'Reviews', 'Excerpt' => 'Excerpt',
                    'HierarchyTree' => 'HierarchyTree', 'Map' => 'Map',
                    'Details' => 'StaffViewMARC',
                ),
                'defaultTab' => null,
            ),
            'Zuki\RecordDriver\Ndl' => array(
                'tabs' => array(
                    'Description' => 'Description', 
                    'Details' => 'StaffViewArray',
                ),
                'defaultTab' => null,
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'ndl' => 'Zuki\Controller\NdlController',
            'ndlrecord' => 'Zuki\Controller\NdlrecordController',
        ),
    ),
);

$recordRoutes = array(
    'ndlrecord' => 'NdlRecord'
);

$staticRoutes = array(
    'Ndl/Advanced', 'Ndl/Home', 'Ndl/Search'
);

$nonTabRecordActions = array(
    'AddComment', 'DeleteComment', 'AddTag', 'Save', 'Email', 'SMS', 'Cite',
    'Export', 'RDF', 'Hold', 'BlockedHold', 'Home'
);

// Build record routes
foreach ($recordRoutes as $routeBase => $controller) {
    // catch-all "tab" route:
    $config['router']['routes'][$routeBase] = array(
        'type'    => 'Zend\Mvc\Router\Http\Segment',
        'options' => array(
            'route'    => '/' . $controller . '/[:id[/:tab]]',
            'constraints' => array(
                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => $controller,
                'action'     => 'Home',
            )
        )
    );
    // special non-tab actions that each need their own route:
    foreach ($nonTabRecordActions as $action) {
        $config['router']['routes'][$routeBase . '-' . strtolower($action)] = array(
            'type'    => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route'    => '/' . $controller . '/[:id]/' . $action,
                'constraints' => array(
                    'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => $controller,
                    'action'     => $action,
                )
            )
        );
    }
}

// Build static routes
foreach ($staticRoutes as $route) {
    list($controller, $action) = explode('/', $route);
    $routeName = str_replace('/', '-', strtolower($route));
    $config['router']['routes'][$routeName] = array(
        'type' => 'Zend\Mvc\Router\Http\Literal',
        'options' => array(
            'route'    => '/' . $route,
            'defaults' => array(
                'controller' => $controller,
                'action'     => $action,
            )
        )
    );
}

return $config;
