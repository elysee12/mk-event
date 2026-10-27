<?php
require_once __DIR__ . '/_service.php';
render_service_page(
  'Decoration (MK)',
  'Wedding, birthday, introduction and corporate event décor.',
  'decorations',
  'decoration',
  array(
    'title'=>'title',
    'meta'=>function($i){ return (isset($i['category'])?$i['category']:'').' · '.(isset($i['package_name'])?$i['package_name']:''); },
    'desc'=>'description','price'=>'price','initial'=>'DC','service'=>'decoration'
  )
);
