<?php
require_once __DIR__ . '/_service.php';
render_service_page(
  'Cakes (MK)',
  'Cake gallery, sizes, prices and custom orders.',
  'cakes',
  'cakes',
  array(
    'title'=>'name',
    'meta'=>function($i){ return (isset($i['cake_type'])?$i['cake_type']:'').' · '.(isset($i['size'])?$i['size']:''); },
    'desc'=>'description','price'=>'price','initial'=>'🎂','service'=>'cakes'
  )
);
