<?php
require_once __DIR__ . '/_service.php';
render_service_page(
  'Fireworks',
  'Sparklers, indoor-safe fountains and a night-sky finale.',
  'fireworks',
  'fireworks',
  array(
    'title' => 'name',
    'meta' => function ($i) {
      $pkg = isset($i['package_name']) ? $i['package_name'] : '';
      $dur = isset($i['duration']) ? $i['duration'] : '';
      return trim($pkg . ($dur ? ' · ' . $dur : ''));
    },
    'desc' => 'description',
    'price' => 'price',
    'initial' => 'FW',
    'service' => 'fireworks',
  )
);
