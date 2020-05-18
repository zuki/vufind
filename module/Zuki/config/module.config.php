<?php

return array (
    'vufind' => array (
        'plugin_managers' => array (
            'ils_driver' => array (
                'factories' => array (
                    'Zuki\\ILS\\Driver\\MyLibrary' => 'Zuki\\ILS\\Driver\\MyLibraryFactory',
                ),
                'aliases' => array (
                    'mylibrary' => 'Zuki\\ILS\\Driver\\MyLibrary',
                ),
            ),
            'search_backend' => array(
                'factories' => array(
                    'Zuki\\Backend\\Ndl\\Backend' => 'Zuki\\Search\\Factory\\NdlBackendFactory',
                    'Zuki\\Backend\\Loc\\Backend' => 'Zuki\\Search\\Factory\\LocBackendFactory',
                ),
                'aliases' => array (
                    'Ndl' => 'Zuki\\Backend\\Ndl\\Backend',
                    'Loc' => 'Zuki\\Backend\\Loc\\Backend',
                ),
            ),
            'search_options' => array(
                'abstract_factories' => array('Zuki\Search\Options\PluginFactory'),
            ),
            'search_params' => array(
                'abstract_factories' => array('Zuki\Search\Params\PluginFactory'),
            ),
            'search_results' => array(
                'abstract_factories' => array('Zuki\Search\Results\PluginFactory'),
            ),
            'recorddriver' => array (
                'factories' => array (
                    'Zuki\\RecordDriver\\SolrDefault' => 'VuFind\\RecordDriver\\SolrDefaultFactory',
                    'Zuki\\RecordDriver\\SolrMarc' => 'VuFind\\RecordDriver\\SolrMarcFactory',
                    'Zuki\\RecordDriver\\Ndl' => 'Zuki\\RecordDriver\\NdlFactory',
                    'Zuki\\RecordDriver\\Loc' => 'Zuki\\RecordDriver\\LocFactory',
                ),
                'aliases' => array (
                    'VuFind\\RecordDriver\\SolrMarc' => 'Zuki\\RecordDriver\\SolrMarc',
                    'VuFind\\RecordDriver\\SolDefault' => 'Zuki\\RecordDriver\\SolrDefault',
                    'Ndl' => 'Zuki\\RecordDriver\\Ndl',
                    'Loc' => 'Zuki\\RecordDriver\\Loc',
                ),
                'delegators' => array (
                    'Zuki\\RecordDriver\\SolrMarc' => array (
                        0 => 'VuFind\\RecordDriver\\IlsAwareDelegatorFactory',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array (
        'factories' => array (
            'Zuki\\Controller\\AdminController' => 'VuFind\\Controller\\AbstractBaseFactory',
            'Zuki\\Controller\\NdlController' => 'VuFind\\Controller\\AbstractBaseFactory',
            'Zuki\\Controller\\NdlrecordController' => 'VuFind\\Controller\\AbstractBaseFactory',
        ),
        'aliases' => array (
            'VuFindAdmin\\Controller\\AdminController' => 'Zuki\\Controller\\AdminController',
        ),
    ),
    'router' => array (
        'routes' => array (
            'admin-records' => array (
                'type' => 'Zend\\Router\\Http\\Literal',
                'options' => array (
                    'route' => '/Admin/Records',
                    'defaults' => array (
                        'controller' => 'Admin',
                        'action' => 'Records',
                    ),
                ),
            ),
            'admin-viewrecord' => array (
                'type' => 'Zend\\Router\\Http\\Literal',
                'options' => array (
                    'route' => '/Admin/ViewRecord',
                    'defaults' => array (
                        'controller' => 'Admin',
                        'action' => 'ViewRecord',
                    ),
                ),
            ),
            'admin-addrecord' => array (
                'type' => 'Zend\\Router\\Http\\Literal',
                'options' => array (
                    'route' => '/Admin/AddRecord',
                    'defaults' => array (
                        'controller' => 'Admin',
                        'action' => 'AddRecord',
                    ),
                ),
            ),
            'admin-searchrecord' => array (
                'type' => 'Zend\\Router\\Http\\Literal',
                'options' => array (
                    'route' => '/Admin/SearchRecord',
                    'defaults' => array (
                        'controller' => 'Admin',
                        'action' => 'SearchRecord',
                    ),
                ),
            ),
            'admin-registerrecord' => array (
                'type' => 'Zend\\Router\\Http\\Literal',
                'options' => array (
                    'route' => '/Admin/RegisterRecord',
                    'defaults' => array (
                        'controller' => 'Admin',
                        'action' => 'RegisterRecord',
                    ),
                ),
            ),
        ),
    ),
);