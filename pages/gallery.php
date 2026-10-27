<?php
require_once __DIR__ . '/../includes/layout.php';
$res = mk_api('gallery');
$items = isset($res['data']) ? $res['data'] : array();
$gal = '';
if (empty($items)) {
  $gal = '<div class="empty">Gallery images will appear here.</div>';
} else {
  $gal = '<div class="gallery-grid">';
  foreach ($items as $g) {
    $title = htmlspecialchars(isset($g['title']) ? $g['title'] : $g['category']);
    $image = isset($g['image']) ? basename($g['image']) : '';
    // The seeded gal1-gal8 rows point at files that were never uploaded.
    $hasImage = $image !== '' && is_file(__DIR__ . '/../uploads/gallery/' . $image);
    $gal .= '<div class="gallery-item">'
          . ($hasImage ? '<img src="' . mk_url('/uploads/gallery/') . htmlspecialchars($image) . '" alt="' . $title . '" loading="lazy" />' : '')
          . '<span>' . $title . '</span></div>';
  }
  $gal .= '</div>';
}
$content = '
<section class="page-hero"><div><h1>Gallery</h1><p>A look at MK Event Company celebrations.</p></div></section>
<section class="section"><div class="container">' . $gal . '</div></section>';
mk_page('Gallery', $content, 'gallery');
