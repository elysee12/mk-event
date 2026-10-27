<?php
require_once __DIR__ . '/_service.php';
render_service_page(
  'Photographers (MK)',
  'Profiles, portfolios, packages and booking.',
  'photographers',
  'photographers',
  array(
    'title'=>'name',
    'meta'=>function($i){ return (isset($i['package_name'])?$i['package_name']:'').' · '.(isset($i['portfolio'])?$i['portfolio']:''); },
    'desc'=>'bio','price'=>'price','initial'=>'PH','service'=>'photographers'
  )
);
