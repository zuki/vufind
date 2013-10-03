<?php 
  return array(
    'extends' => 'blueprint',
    'css' => array(
        'biblio.css',
    ),
    'helpers' => array(
        'invokables' => array(
            'sortfacetlist' => 'Zuki\View\Helper\Root\SortFacetList',
        )
    ),
  );

