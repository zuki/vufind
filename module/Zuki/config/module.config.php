<?php

return array (
  'vufind' => 
  array (
    'plugin_managers' => 
    array (
      'ils_driver' => 
      array (
        'factories' => 
        array (
          'Zuki\\ILS\\Driver\\MyLibrary' => 'Zuki\\ILS\\Driver\\MyLibraryFactory',
        ),
        'aliases' => 
        array (
          'mylibrary' => 'Zuki\\ILS\\Driver\\MyLibrary',
        ),
      ),
      'recorddriver' => 
      array (
        'factories' => 
        array (
          'Zuki\\RecordDriver\\SolrMarc' => 'VuFind\\RecordDriver\\SolrDefaultFactory',
        ),
        'aliases' => 
        array (
          'VuFind\\RecordDriver\\SolrMarc' => 'Zuki\\RecordDriver\\SolrMarc',
        ),
        'delegators' => 
        array (
          'Zuki\\RecordDriver\\SolrMarc' => 
          array (
            0 => 'VuFind\\RecordDriver\\IlsAwareDelegatorFactory',
          ),
        ),
      ),
    ),
  ),
);