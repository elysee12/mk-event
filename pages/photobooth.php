<?php
require_once __DIR__ . '/_service.php';
render_service_page(
  '360 Photobooth',
  'Spinning slow-motion clips, LED rings and instant sharing.',
  'photobooths',
  'photobooth',
  array(
    'title' => 'name',
    'meta' => function ($i) {
      $pkg = isset($i['package_name']) ? $i['package_name'] : '';
      $dur = isset($i['duration']) ? $i['duration'] : '';
      return trim($pkg . ($dur ? ' · ' . $dur : ''));
    },
    'desc' => 'description',
    'price' => 'price',
    'initial' => '360',
    'service' => 'photobooth',
  )
);
