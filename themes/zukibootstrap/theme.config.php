<?php 
  return array(
    'extends' => 'bootstrap',
    'css' => array(
        'biblio.css',
        'zuki.css',
    ),
    'helpers' => array(
        'invokables' => array(
            'sortfacetlist' => 'Zuki\View\Helper\Root\SortFacetList',
        )
    ),
  );

