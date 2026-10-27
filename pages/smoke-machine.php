<?php
require_once __DIR__ . '/_service.php';
render_service_page(
  'Smoke Machine',
  'Cinematic fog, dance-floor haze and grand-entrance reveals.',
  'smoke-machines',
  'smoke',
  array(
    'title' => 'name',
    'meta' => function ($i) {
      $pkg = isset($i['package_name']) ? $i['package_name'] : '';
      $dur = isset($i['duration']) ? $i['duration'] : '';
      return trim($pkg . ($dur ? ' · ' . $dur : ''));
    },
    'desc' => 'description',
    'price' => 'price',
    'initial' => 'SM',
    'service' => 'smoke_machine',
  )
);
