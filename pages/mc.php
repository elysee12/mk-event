<?php
require_once __DIR__ . '/_service.php';
render_service_page(
  'MC (MK)',
  'Professional masters of ceremony for weddings, galas and launches.',
  'mcs',
  'mc',
  array(
    'title' => 'name',
    'meta' => function ($i) { return (isset($i['experience']) ? $i['experience'] : '') . ' experience'; },
    'desc' => 'bio',
    'price' => 'price',
    'initial' => 'MC',
    'service' => 'mc',
  )
);
