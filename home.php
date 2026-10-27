<?php
require_once __DIR__ . '/includes/layout.php';

$settings = mk_api('settings');
$gallery = mk_api('gallery');
$testimonials = mk_api('testimonials');
$s = isset($settings['data']) ? $settings['data'] : array();
$g = isset($gallery['data']) ? $gallery['data'] : array();
$t = isset($testimonials['data']) ? $testimonials['data'] : array();
$tagline = isset($s['tagline']) ? $s['tagline'] : 'We Make Your Events Memorable';
$about = isset($s['about_text']) ? $s['about_text'] : 'MK Event Company is a full-service events partner in Rwanda.';

$services = array(
  array(mk_url('/pages/mc.php'), '', 'MC (MK)', 'Professional hosts for weddings, galas and launches.'),
  array(mk_url('/pages/abatahira.php'), '', 'Abatahira (MK)', 'Traditional dance and cultural ceremony teams.'),
  array(mk_url('/pages/cakes.php'), '', 'Cakes (MK)', 'Custom cakes for weddings, birthdays and introductions.'),
  array(mk_url('/pages/photographers.php'), '', 'Photographers (MK)', 'Portfolios, packages and story-led coverage.'),
  array(mk_url('/pages/catering.php'), '', 'Catering (MK)', 'Menus and packages sized to your guest list.'),
  array(mk_url('/pages/decoration.php'), '', 'Decoration (MK)', 'Wedding, birthday, introduction and corporate décor.'),
  array(mk_url('/pages/sonalization.php'), '', 'Sonalization (MK)', 'Invitations, name designs and custom event kits.'),
  array(mk_url('/pages/itorero-band.php'), '', 'Itorero & Band (MK)', 'Live culture, bands and ceremony music.'),
  array(mk_url('/pages/service-protocol.php'), '', 'Service and Protocol', 'Our standards, procedures, and commitment to excellence.'),
);

$tiles = '';
foreach ($services as $sv) {
  $tiles .= '<a href="' . $sv[0] . '" class="service-tile"><div class="icon">' . $sv[1] . '</div><h3>' . $sv[2] . '</h3><p>' . $sv[3] . '</p></a>';
}

$gal = '';
if (empty($g)) {
  $g = array(
    array('title' => 'Wedding Night'), array('title' => 'Introduction'),
    array('title' => 'Birthday'), array('title' => 'Corporate'),
    array('title' => 'Cakes'), array('title' => 'Decoration'),
    array('title' => 'Live Band'), array('title' => 'Catering'),
  );
}
foreach (array_slice($g, 0, 8) as $item) {
  $title = isset($item['title']) ? htmlspecialchars($item['title']) : 'MK Event';
  $image = isset($item['image']) ? basename($item['image']) : '';
  $hasImage = $image !== '' && is_file(__DIR__ . '/uploads/gallery/' . $image);
  $gal .= '<div class="gallery-item">'
        . ($hasImage ? '<img src="' . mk_url('/uploads/gallery/') . htmlspecialchars($image) . '" alt="' . $title . '" loading="lazy" />' : '')
        . '<span>' . $title . '</span></div>';
}

$tests = '';
if (empty($t)) {
  $t = array(
    array('client_name' => 'Diane & Patrick', 'rating' => 5, 'message' => 'Flawless wedding coordination.', 'event_type' => 'Wedding'),
    array('client_name' => 'Boss Ltd', 'rating' => 5, 'message' => 'Premium corporate gala delivery.', 'event_type' => 'Corporate'),
    array('client_name' => 'Uwase Family', 'rating' => 5, 'message' => 'Beautiful introduction ceremony.', 'event_type' => 'Introduction'),
  );
}
foreach ($t as $item) {
  $stars = str_repeat('★', isset($item['rating']) ? (int)$item['rating'] : 5);
  $tests .= '<article class="testimonial"><div class="stars">' . $stars . '</div><p>“' . htmlspecialchars($item['message']) . '”</p><strong>' . htmlspecialchars($item['client_name']) . '</strong><small>' . htmlspecialchars($item['event_type']) . '</small></article>';
}

$content = '
<section class="home-hero">
  <div class="hero-slider" id="heroSlider">
    <div class="slide active" style="background-image: url(' . mk_url('/uploads/slider/slide1.jpg') . ')"></div>
    <div class="slide" style="background-image: url(' . mk_url('/uploads/slider/slide2.jpg') . ')"></div>
    <div class="slide" style="background-image: url(' . mk_url('/uploads/slider/slide3.jpg') . ')"></div>
    <div class="slider-dots">
      <span class="dot active" data-slide="0"></span>
      <span class="dot" data-slide="1"></span>
      <span class="dot" data-slide="2"></span>
    </div>
  </div>
  <div class="container hero-content">
    <h1 class="brand-hero"><span>Event Company</span>MK Event</h1>
    <h2>' . htmlspecialchars($tagline) . '</h2>
    <p>One trusted partner for MCs, Abatahira, cakes, photography, catering, decoration, sonalization, and live Itorero & bands across Rwanda.</p>
    <div class="hero-actions">
      <a href="' . mk_url('/pages/booking.php') . '" class="btn btn-gold">Book Your Event</a>
      <a href="' . mk_url('/pages/gallery.php') . '" class="btn btn-outline">View Gallery</a>
    </div>
  </div>
</section>

<section class="section extras-stage" id="addons">
  <div class="container">
    <div class="section-head">
      <div class="eyebrow">Add Service</div>
      <h2>Turn the night electric</h2>
      <p>Smoke, fireworks and a 360 photobooth — the wow moments guests never forget.</p>
    </div>
    <div class="extras-grid">
      <a class="extra-card extra-smoke" href="' . mk_url('/pages/smoke-machine.php') . '">
        <span class="extra-tag">Add-on</span>
        <div class="extra-art" aria-hidden="true">
          <span class="mist m1"></span><span class="mist m2"></span><span class="mist m3"></span>
        </div>
        <h3>Smoke Machine</h3>
        <p>Cinematic fog for grand entries, first dances and a dance floor that looks like a music video.</p>
        <span class="extra-cta">View packages</span>
      </a>
      <a class="extra-card extra-fire" href="' . mk_url('/pages/fireworks.php') . '">
        <span class="extra-tag">Add-on</span>
        <div class="extra-art" aria-hidden="true">
          <span class="spark s1"></span><span class="spark s2"></span><span class="spark s3"></span><span class="spark s4"></span>
        </div>
        <h3>Fireworks</h3>
        <p>Indoor-safe fountains, sparkler send-offs and a licensed night-sky finale over your celebration.</p>
        <span class="extra-cta">View packages</span>
      </a>
      <a class="extra-card extra-booth" href="' . mk_url('/pages/photobooth.php') . '">
        <span class="extra-tag">Add-on</span>
        <div class="extra-art" aria-hidden="true">
          <span class="ring"></span><span class="ring-core">360&deg;</span>
        </div>
        <h3>360 Photobooth</h3>
        <p>Slow-motion spins, LED glow and instant sharing — the station everyone lines up for.</p>
        <span class="extra-cta">View packages</span>
      </a>
      <a class="extra-card extra-sax" href="' . mk_url('/pages/saxophone.php') . '">
        <span class="extra-tag">Add-on</span>
        <div class="extra-art" aria-hidden="true">
          <span class="note n1">&#9834;</span><span class="note n2">&#9835;</span><span class="note n3">&#9834;</span>
        </div>
        <h3>Saxophone</h3>
        <p>Live saxophone performances for cocktail hours, grand entrances and ambient elegance.</p>
        <span class="extra-cta">View packages</span>
      </a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container about-split">
    <div class="about-visual"></div>
    <div class="about-copy">
      <div class="eyebrow">About MK Event Company</div>
      <h2>Celebrations designed with culture, craft and calm coordination.</h2>
      <p>' . htmlspecialchars($about) . '</p>
      <a href="' . mk_url('/pages/contact.php') . '" class="btn btn-dark">Talk to Us</a>
    </div>
  </div>
</section>

<section class="section services-preview">
  <div class="container">
    <div class="section-head">
      <div class="eyebrow">Services</div>
      <h2>Everything your event needs</h2>
      <p>Explore our MK service houses — each ready to book for your date.</p>
    </div>
    <div class="grid-3">' . $tiles . '</div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head"><div class="eyebrow">Gallery</div><h2>Moments we created</h2></div>
    <div class="gallery-grid">' . $gal . '</div>
  </div>
</section>

<section class="section" style="background:var(--cream)">
  <div class="container">
    <div class="section-head"><div class="eyebrow">Testimonials</div><h2>Loved by our clients</h2></div>
    <div class="grid-3">' . $tests . '</div>
  </div>
</section>

<section class="cta-band">
  <h2>Ready to plan with MK Event Company?</h2>
  <p>Tell us your date — we will assemble the right team for your celebration.</p>
  <a href="' . mk_url('/pages/booking.php') . '" class="btn btn-gold">Start Booking</a>
</section>';

mk_page('Home', $content, 'home');
