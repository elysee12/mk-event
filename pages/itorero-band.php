<?php
require_once __DIR__ . '/_service.php';
render_service_page(
  'Itorero & Band (MK)',
  'Itorero, live bands and music groups for ceremony and reception.',
  'itorero-bands',
  'itorero',
  array(
    'title'=>'name',
    'meta'=>function($i){ return (isset($i['group_type'])?$i['group_type']:'').' · '.(isset($i['package_name'])?$i['package_name']:''); },
    'desc'=>'bio','price'=>'price','initial'=>'IB','service'=>'itorero_band'
  )
);
