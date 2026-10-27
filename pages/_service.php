<?php
/**
 * Service listing page factory
 */
require_once __DIR__ . '/../includes/layout.php';

function render_service_page($title, $subtitle, $apiPath, $active, $map) {
    $res = mk_api($apiPath);
    $items = isset($res['data']) ? $res['data'] : array();
    if (empty($items) && function_exists('mk_extra_defaults')) {
        $items = mk_extra_defaults($apiPath);
    }
    $content = '
    <section class="page-hero"><div><h1>' . htmlspecialchars($title) . '</h1><p>' . htmlspecialchars($subtitle) . '</p></div></section>
    <section class="section"><div class="container">' . mk_cards($items, $map) . '</div></section>';
    mk_page($title, $content, $active);
}

function mk_extra_defaults($apiPath) {
    $all = array(
        'smoke-machines' => array(
            array('id' => 1, 'name' => 'Low Fog Sweep', 'package_name' => 'Ceremony Mist', 'duration' => '2 hours', 'description' => 'Soft ground fog for aisles and romantic couple entries.', 'price' => 80000),
            array('id' => 2, 'name' => 'Dance Floor Cloud', 'package_name' => 'Party Atmosphere', 'duration' => 'Evening set', 'description' => 'Haze that makes lights and lasers look cinematic.', 'price' => 120000),
            array('id' => 3, 'name' => 'Grand Entrance Fog', 'package_name' => 'VIP Reveal', 'duration' => 'Highlight moments', 'description' => 'Timed smoke bursts for MC announcements and reveals.', 'price' => 150000),
        ),
        'fireworks' => array(
            array('id' => 1, 'name' => 'Sparkler Send-off', 'package_name' => 'Guest Sparklers', 'duration' => '10 minutes', 'description' => 'Handheld sparklers for a glowing exit photo moment.', 'price' => 45000),
            array('id' => 2, 'name' => 'Cold Fountain Show', 'package_name' => 'Indoor Safe Fountains', 'duration' => '8–12 minutes', 'description' => 'Indoor-safe cold fireworks around the stage or first dance.', 'price' => 180000),
            array('id' => 3, 'name' => 'Night Sky Finale', 'package_name' => 'Outdoor Burst', 'duration' => 'Finale', 'description' => 'A licensed outdoor fireworks close in gold and color.', 'price' => 450000),
        ),
        'photobooths' => array(
            array('id' => 1, 'name' => 'Classic 360 Spin', 'package_name' => '360 Photobooth', 'duration' => '3 hours', 'description' => 'Spinning platform, LED ring and instant sharing.', 'price' => 150000),
            array('id' => 2, 'name' => 'LED Ring Booth', 'package_name' => 'Glow Ring 360', 'duration' => '4 hours', 'description' => 'RGB lighting, props and custom name overlays.', 'price' => 220000),
            array('id' => 3, 'name' => 'VIP Slow-Mo Booth', 'package_name' => 'Unlimited Spins', 'duration' => 'Full reception', 'description' => 'Unlimited spins, attendant and same-night video delivery.', 'price' => 280000),
        ),
        'saxophone' => array(
            array('id' => 1, 'name' => 'Cocktail Hour Sax', 'package_name' => 'Elegant Start', 'duration' => '1 hour', 'description' => 'Smooth jazz saxophone for cocktail hours and guest arrivals.', 'price' => 150000),
            array('id' => 2, 'name' => 'Grand Entrance', 'package_name' => 'VIP Entry', 'duration' => '30 minutes', 'description' => 'Dramatic saxophone entrance for couple or VIP guests.', 'price' => 100000),
            array('id' => 3, 'name' => 'Full Evening Set', 'package_name' => 'All Night', 'duration' => '4 hours', 'description' => 'Live saxophone throughout your reception with song requests.', 'price' => 400000),
        ),
    );
    return isset($all[$apiPath]) ? $all[$apiPath] : array();
}
