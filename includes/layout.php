<?php
/**
 * MK Event Company - shared layout helpers
 */
if (!defined('MK_ROOT')) {
    define('MK_ROOT', dirname(__DIR__));
}
require_once __DIR__ . '/db.php';

/**
 * Base URL helper — works on both localhost/MK/ and mkevent.co.rw/
 *
 * On localhost XAMPP the subfolder is /MK.
 * On a live server where this project is deployed at the domain root, set
 * the MK_BASE environment variable to '' (empty string) in the server config,
 * OR create a file  includes/base.php  with:
 *    define('MK_BASE', '');
 *
 * Fallback auto-detection uses SCRIPT_NAME to guess the subfolder so it
 * works on both environments without any manual edit.
 */
if (!defined('MK_BASE')) {
    // Check for an explicit override file first (set by deployment environment)
    $mkBaseFile = __DIR__ . '/base.php';
    if (file_exists($mkBaseFile)) {
        require_once $mkBaseFile;
    }
    if (!defined('MK_BASE')) {
        // Auto-detect: if document root equals MK_ROOT we are at the domain root
        $docRoot = realpath(isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '');
        $mkRoot  = realpath(MK_ROOT);
        if ($docRoot !== false && $mkRoot !== false && $docRoot === $mkRoot) {
            define('MK_BASE', '');
        } else {
            define('MK_BASE', '/MK');
        }
    }
}

/** Return a URL rooted at the site base (no trailing slash needed on $path) */
function mk_url($path = '') {
    return MK_BASE . $path;
}

function mk_page($title, $content, $active = '') {
    $year = date('Y');
    $isHome = ($active === 'home');
    $headerClass = $isHome ? 'site-header' : 'site-header scrolled';
    $settings = mk_api('settings');
    $s = isset($settings['data']) ? $settings['data'] : array();
    $phone = isset($s['phone']) ? $s['phone'] : '+250 788 000 000';
    $email = isset($s['email']) ? $s['email'] : 'info@mkevent.rw';
    $address = isset($s['address']) ? $s['address'] : 'Kigali, Rwanda';
    $whatsapp = isset($s['whatsapp']) ? $s['whatsapp'] : '+2507880094497';
    $facebook = isset($s['facebook']) ? $s['facebook'] : 'https://facebook.com/mkevent';
    $instagram = isset($s['instagram']) ? $s['instagram'] : 'https://instagram.com/mkevent';
    $twitter = isset($s['twitter']) ? $s['twitter'] : 'https://twitter.com/mkevent';
    $tiktok = isset($s['tiktok']) ? $s['tiktok'] : 'https://tiktok.com/@mkevent';
    $cssVer = (string) @filemtime(__DIR__ . '/../assets/css/style.css');
    $jsVer = (string) @filemtime(__DIR__ . '/../assets/js/main.js');
    $base = MK_BASE;
    echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>' . htmlspecialchars($title) . ' | MK Event Company</title>
  <link rel="icon" type="image/png" href="' . $base . '/assets/logo.png" />
  <link rel="shortcut icon" type="image/png" href="' . $base . '/assets/logo.png" />
  <link rel="apple-touch-icon" href="' . $base . '/assets/logo.png" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="' . $base . '/assets/css/style.css?v=' . $cssVer . '" />
</head>
<body>
<header class="' . $headerClass . '" id="siteHeader">
  <div class="container nav-wrap">
    <a href="' . $base . '/" class="logo">
      <img src="' . $base . '/assets/logo.png" alt="MK Event Company" class="logo-img" />
      <div class="logo-text"><strong>MK Event</strong><span>Company</span></div>
    </a>
    <button class="menu-btn" id="menuBtn" type="button">Menu</button>
    <nav class="nav-links" id="navLinks">
      <a href="' . $base . '/" class="' . ($active==='home'?'active':'') . '">Home</a>
      <div class="nav-drop' . (in_array($active, array('smoke','fireworks','photobooth','saxophone'), true) ? ' active' : '') . '">
        <button class="nav-drop-btn" type="button">Add Service</button>
        <div class="nav-drop-menu">
          <a href="' . $base . '/pages/smoke-machine.php" class="' . ($active==='smoke'?'active':'') . '">Smoke Machine</a>
          <a href="' . $base . '/pages/fireworks.php" class="' . ($active==='fireworks'?'active':'') . '">Fireworks</a>
          <a href="' . $base . '/pages/photobooth.php" class="' . ($active==='photobooth'?'active':'') . '">360 Photobooth</a>
          <a href="' . $base . '/pages/saxophone.php" class="' . ($active==='saxophone'?'active':'') . '">Saxophone</a>
        </div>
      </div>
      <a href="' . $base . '/pages/mc.php" class="' . ($active==='mc'?'active':'') . '">MC</a>
      <a href="' . $base . '/pages/abatahira.php" class="' . ($active==='abatahira'?'active':'') . '">Abatahira</a>
      <a href="' . $base . '/pages/cakes.php" class="' . ($active==='cakes'?'active':'') . '">Cakes</a>
      <a href="' . $base . '/pages/photographers.php" class="' . ($active==='photographers'?'active':'') . '">Photographers</a>
      <a href="' . $base . '/pages/catering.php" class="' . ($active==='catering'?'active':'') . '">Catering</a>
      <a href="' . $base . '/pages/decoration.php" class="' . ($active==='decoration'?'active':'') . '">Decoration</a>
      <a href="' . $base . '/pages/sonalization.php" class="' . ($active==='sonalization'?'active':'') . '">Sonalization</a>
      <a href="' . $base . '/pages/itorero-band.php" class="' . ($active==='itorero'?'active':'') . '">Itorero & Band</a>
      <a href="' . $base . '/pages/service-protocol.php" class="' . ($active==='service-protocol'?'active':'') . '">Service and Protocol</a>
      <a href="' . $base . '/pages/gallery.php" class="' . ($active==='gallery'?'active':'') . '">Gallery</a>
      <a href="' . $base . '/pages/contact.php" class="' . ($active==='contact'?'active':'') . '">Contact</a>
      <a href="' . $base . '/pages/booking.php" class="nav-cta">Book Now</a>
    </nav>
  </div>
</header>
<main>' . $content . '</main>
<footer class="site-footer">
  <div class="container footer-grid">
    <div class="footer-brand">
      <div class="logo">
        <img src="' . $base . '/assets/logo.png" alt="MK Event Company" class="logo-img" />
        <div class="logo-text"><strong>MK Event</strong><span>Company</span></div>
      </div>
      <p>We make your events memorable — MCs, décor, cakes, catering, photography, live culture, smoke, fireworks and 360 photobooths on one stage.</p>
    </div>
    <div><h4>Services</h4><ul>
      <li><a href="' . $base . '/pages/smoke-machine.php">Smoke Machine</a></li>
      <li><a href="' . $base . '/pages/fireworks.php">Fireworks</a></li>
      <li><a href="' . $base . '/pages/photobooth.php">360 Photobooth</a></li>
      <li><a href="' . $base . '/pages/saxophone.php">Saxophone</a></li>
      <li><a href="' . $base . '/pages/mc.php">MC (MK)</a></li>
    </ul></div>
    <div><h4>Explore</h4><ul>
      <li><a href="' . $base . '/pages/gallery.php">Gallery</a></li>
      <li><a href="' . $base . '/pages/booking.php">Booking</a></li>
      <li><a href="' . $base . '/pages/contact.php">Contact</a></li>
      <li><a href="' . $base . '/pages/admin.php">Admin</a></li>
    </ul></div>
    <div><h4>Contact</h4><ul style="display:flex;flex-direction:column;gap:.8rem">
      <li style="display:flex;align-items:center;gap:.5rem"><span style="color:var(--gold)">&#x1F4CD;</span> <span style="color:rgba(247,243,234,.82)">' . htmlspecialchars($address) . '</span></li>
      <li style="display:flex;align-items:center;gap:.5rem"><span style="color:var(--gold)">&#x260E;</span> <a href="tel:' . preg_replace('/[^0-9+]/', '', $phone) . '" style="color:rgba(247,243,234,.82);text-decoration:none">' . htmlspecialchars($phone) . '</a></li>
      <li style="display:flex;align-items:center;gap:.5rem"><span style="color:var(--gold)">&#x2709;</span> <a href="mailto:' . htmlspecialchars($email) . '" style="color:rgba(247,243,234,.82);text-decoration:none">' . htmlspecialchars($email) . '</a></li>
      <li style="display:flex;align-items:center;gap:.5rem">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect width="24" height="24" rx="6" fill="#25D366"/>
          <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.013-.606.074-.151.084-.149.149-.247.348-.099.198-.099.371-.075.544.024.174.247.447.423.645.176.198.49.645.847 1.077.357.432.714.848 1.077 1.077.357.224.594.447.847.544.254.099.458.054.644-.06.186-.113.371-.322.534-.532.163-.209.271-.248.458-.371.187-.124.374-.248.544-.371.17-.124.284-.248.544-.496.26-.248.099-.496.074-.746.074-.25 0-.997-.037-1.992-.927-1.236-1.014-2.072-2.279-2.072-2.279-.297-.496.297-.496.297-.496.248-.248.621-.621.795-.874.174-.253.458-.621.458-.621 0 0 .544-.894 2.022-.894 1.478 0 2.597.746 2.597.746.248.424.347.967.347 1.385 0 .418-.149.894-.149.894 0 0 .895 1.49 2.17 2.322 1.275.832 2.533.586 2.795.458.262-.128.895-.621.895-.621 0 0 .374-.374.544-.847.17-.474.085-.874.085-.874 0 0-.074-.149-.347-.423-.273-.273-1.457-.723-2.072-.967-.615-.244-.997-.371-1.335-.371-.338 0-.67.099-.997.297-.327.198-.644.496-.644.496z" fill="white"/>
        </svg>
        <a href="https://wa.me/' . preg_replace('/[^0-9]/', '', $whatsapp) . '" target="_blank" style="color:rgba(247,243,234,.82);text-decoration:none">' . htmlspecialchars($whatsapp) . '</a>
      </li>
    </ul></div>
    <div class="footer-social">
      <h4>FOLLOW US</h4>
      <div class="social-links">
        <a href="' . htmlspecialchars($facebook) . '" target="_blank" aria-label="Facebook">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="12" fill="#1877f2"/>
            <text x="12" y="17" text-anchor="middle" fill="white" font-size="14" font-weight="bold">f</text>
          </svg>
        </a>
        <a href="' . htmlspecialchars($twitter) . '" target="_blank" aria-label="Twitter">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="12" fill="#000"/>
            <text x="12" y="17" text-anchor="middle" fill="white" font-size="14" font-weight="bold">𝕏</text>
          </svg>
        </a>
        <a href="' . htmlspecialchars($instagram) . '" target="_blank" aria-label="Instagram">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="24" height="24" rx="6" fill="url(#instagram-gradient)"/>
            <circle cx="12" cy="12" r="5" fill="white"/>
            <circle cx="19" cy="5" r="2" fill="white"/>
            <defs>
              <linearGradient id="instagram-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#f09433"/>
                <stop offset="25%" style="stop-color:#e6683c"/>
                <stop offset="50%" style="stop-color:#dc2743"/>
                <stop offset="75%" style="stop-color:#cc2366"/>
                <stop offset="100%" style="stop-color:#bc1888"/>
              </linearGradient>
            </defs>
          </svg>
        </a>
        <a href="' . htmlspecialchars($tiktok) . '" target="_blank" aria-label="TikTok">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="12" fill="#000"/>
            <path d="M12.5 4.5v9.5c0 1.5-1 2.5-2.5 2.5s-2.5-1-2.5-2.5 1-2.5 2.5-2.5h.5v-3h-.5c-3 0-5.5 2.5-5.5 5.5s2.5 5.5 5.5 5.5 5.5-2.5 5.5-5.5v-6.5c1 1 2.5 1.5 4 1.5v-3c-1.5 0-3-1-4-2.5z" fill="white"/>
            <path d="M12.5 4.5v9.5c0 1.5-1 2.5-2.5 2.5s-2.5-1-2.5-2.5 1-2.5 2.5-2.5h.5v-3h-.5c-3 0-5.5 2.5-5.5 5.5s2.5 5.5 5.5 5.5 5.5-2.5 5.5-5.5v-6.5c1 1 2.5 1.5 4 1.5v-3c-1.5 0-3-1-4-2.5z" fill="#ff0050" fill-opacity="0.3" transform="translate(1,1)"/>
          </svg>
        </a>
      </div>
    </div>
  </div>
  <div class="container footer-bottom">
    <span>&copy; ' . $year . ' MK Event Company. All rights reserved.</span>
    <span>We Make Your Events Memorable</span>
  </div>
</footer>
<script src="' . $base . '/assets/js/main.js?v=' . $jsVer . '"></script>
<script>var MK_BASE = ' . json_encode($base) . ';</script>
</body>
</html>';
}

function mk_price($n) {
    return number_format((float)$n, 0, '.', ',') . ' RWF';
}

function mk_cards($items, $map) {
    if (empty($items)) return '<div class="empty">No items available yet.</div>';
    $html = '<div class="grid-3">';
    foreach ($items as $item) {
        $title = htmlspecialchars(isset($item[$map['title']]) ? $item[$map['title']] : 'MK');
        $meta = htmlspecialchars(isset($map['meta']) ? (is_callable($map['meta']) ? $map['meta']($item) : (isset($item[$map['meta']]) ? $item[$map['meta']] : '')) : '');
        $desc = htmlspecialchars(isset($item[$map['desc']]) ? $item[$map['desc']] : '');
        $price = isset($item[$map['price']]) ? mk_price($item[$map['price']]) : '';
        $initial = isset($map['initial']) ? $map['initial'] : 'MK';
        $service = isset($map['service']) ? $map['service'] : 'other';
        $id = isset($item['id']) ? $item['id'] : '';
        
        // Check for photo
        $photo = isset($item['photo']) ? $item['photo'] : null;
        $photoHtml = '';
        if ($photo) {
            // Determine upload path based on service
            $uploadPaths = array(
                'mc' => 'mcs',
                'photographers' => 'photographers',
                'cakes' => 'cakes',
                'catering' => 'catering',
                'decoration' => 'decoration',
                'abatahira' => 'abatahira',
                'itorero_band' => 'itorero',
                'smoke_machine' => 'extras',
                'fireworks' => 'extras',
                'photobooth' => 'extras',
                'saxophone' => 'extras',
                'sonalization' => 'sonalization',
            );
            $path = isset($uploadPaths[$service]) ? $uploadPaths[$service] : 'extras';
            $photoHtml = '<img src="' . MK_BASE . '/uploads/' . $path . '/' . htmlspecialchars($photo) . '" alt="' . $title . '" style="width:100%;height:100%;object-fit:cover" />';
        } else {
            $photoHtml = $initial;
        }
        
        // Generate random rating (4.0-5.0) and reviews for demo
        $rating = isset($item['rating']) ? $item['rating'] : (4.0 + (rand(0, 10) / 10));
        $reviews = isset($item['reviews']) ? $item['reviews'] : rand(50, 300);
        $stars = str_repeat('&#9733;', floor($rating));
        
        $html .= '<article class="service-card">
          <div class="media">' . $photoHtml . '</div>
          <div class="divider"></div>
          <div class="body">
            <h3>' . $title . '</h3>
            <div class="meta">' . $meta . '</div>
            <div class="rating">' . $stars . ' <span class="rating-text">(' . $reviews . ' reviews)</span></div>
            <p>' . $desc . '</p>
            <div class="price">' . $price . '</div>
            <a class="btn btn-dark" href="' . MK_BASE . '/pages/booking.php?service=' . urlencode($service) . '&amp;id=' . urlencode($id) . '">&#x1F4C5; Book / Order</a>
          </div>
        </article>';
    }
    $html .= '</div>';
    return $html;
}
