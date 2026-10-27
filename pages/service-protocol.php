<?php
require_once __DIR__ . '/../includes/layout.php';

$protocol = mk_api('service-protocols');
$p = isset($protocol['data']) ? $protocol['data'] : array();

$content = '
<section class="page-hero"><div><h1>Service and Protocol (MK)</h1><p>Professional event management standards and procedures</p></div></section>
<section class="section">
  <div class="container">
    <div class="section-head">
      <div class="eyebrow">Our Standards</div>
      <h2>Event Service Protocol</h2>
      <p>At MK Event Company, we follow strict protocols to ensure every event is executed with excellence and professionalism.</p>
    </div>
    <div class="grid-2">
      <div class="form-card">
        <h3 style="font-family:var(--font-display);color:var(--navy);margin-bottom:1rem">Our Service Standards</h3>
        <ul style="display:grid;gap:1rem">
          <li><strong>Punctuality:</strong> We arrive 2 hours before event time for setup and preparation.</li>
          <li><strong>Professional Attire:</strong> Our team wears appropriate formal attire for every event type.</li>
          <li><strong>Quality Equipment:</strong> We use professional-grade sound, lighting, and photography equipment.</li>
          <li><strong>Backup Plans:</strong> We always have backup equipment and personnel ready.</li>
          <li><strong>Client Communication:</strong> Regular updates before, during, and after events.</li>
        </ul>
      </div>
      <div class="form-card">
        <h3 style="font-family:var(--font-display);color:var(--navy);margin-bottom:1rem">Booking Protocol</h3>
        <ul style="display:grid;gap:1rem">
          <li><strong>Initial Consultation:</strong> Free consultation to understand your event needs.</li>
          <li><strong>Written Agreement:</strong> Detailed contract with services, timeline, and pricing.</li>
          <li><strong>50% Deposit:</strong> 50% deposit required to confirm booking.</li>
          <li><strong>Final Payment:</strong> Remaining balance due 7 days before event.</li>
          <li><strong>Event Day:</strong> Full team coordination and on-site management.</li>
        </ul>
      </div>
    </div>
  </div>
</section>
<section class="section">
  <div class="container">
    <div class="section-head">
      <div class="eyebrow">Protocol Services</div>
      <h2>Our Protocol Offerings</h2>
    </div>';
    
if (empty($p)) {
  $content .= '<p style="color:var(--muted)">No protocol services available yet.</p>';
} else {
  $content .= '<div class="grid-3">';
  foreach ($p as $item) {
    $photo = isset($item['photo']) ? htmlspecialchars($item['photo']) : '';
    $photoPath = $photo ? mk_url('/uploads/service-protocols/') . $photo : '';
    $content .= '<article class="service-card">
      <div class="media">' . ($photoPath ? '<img src="' . $photoPath . '" alt="' . htmlspecialchars($item['title']) . '" style="width:100%;height:100%;object-fit:cover" />' : '<div class="initial">' . strtoupper(substr($item['title'], 0, 1)) . '</div>') . '</div>
      <div class="divider"></div>
      <div class="body">
        <h3>' . htmlspecialchars($item['title']) . '</h3>
        <p>' . htmlspecialchars($item['description']) . '</p>
        <div class="price">' . number_format($item['price']) . ' RWF</div>
        <a class="btn btn-dark" href="' . mk_url('/pages/booking.php') . '?service=service_protocol&amp;id=' . $item['id'] . '">&#x1F4C5; Book / Order</a>
      </div>
    </article>';
  }
  $content .= '</div>';
}

$content .= '
</section>
<section class="section" style="background:var(--cream)">
  <div class="container">
    <div class="section-head">
      <div class="eyebrow">Safety & Quality</div>
      <h2>Our Commitment</h2>
    </div>
    <div class="grid-3">
      <div class="form-card" style="text-align:center">
        <h3 style="font-size:2rem;margin-bottom:.5rem"></h3>
        <h4 style="font-family:var(--font-display);color:var(--navy);margin-bottom:.5rem">Safety First</h4>
        <p>All equipment is regularly inspected and maintained for safe operation.</p>
      </div>
      <div class="form-card" style="text-align:center">
        <h3 style="font-size:2rem;margin-bottom:.5rem"></h3>
        <h4 style="font-family:var(--font-display);color:var(--navy);margin-bottom:.5rem">Quality Service</h4>
        <p>We maintain high standards across all our service offerings.</p>
      </div>
      <div class="form-card" style="text-align:center">
        <h3 style="font-size:2rem;margin-bottom:.5rem"></h3>
        <h4 style="font-family:var(--font-display);color:var(--navy);margin-bottom:.5rem">Customer Focus</h4>
        <p>Your satisfaction is our priority. We adapt to your specific needs.</p>
      </div>
    </div>
  </div>
</section>
<section class="cta-band">
  <h2>Ready to work with us?</h2>
  <p>Contact us to discuss your event requirements and receive a detailed proposal.</p>
  <a href="' . mk_url('/pages/contact.php') . '" class="btn btn-gold">Contact Us</a>
</section>';

mk_page('Service and Protocol (MK)', $content, 'service-protocol');
