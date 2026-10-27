<?php
require_once __DIR__ . '/_service.php';
render_service_page(
  'Catering (MK)',
  'Menus, food packages and guest-ready pricing.',
  'catering',
  'catering',
  array(
    'title'=>'package_name',
    'meta'=>function($i){ return (isset($i['people_count'])?$i['people_count']:'0').' people'; },
    'desc'=>'menu_items','price'=>'price','initial'=>'CT','service'=>'catering'
  )
);
