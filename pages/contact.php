<?php
require_once __DIR__ . '/../includes/layout.php';
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

$content = '
<section class="page-hero"><div><h1>Contact</h1><p>Reach MK Event Company — we respond quickly.</p></div></section>
<section class="section">
  <div class="container grid-2">
    <div>
      <div class="section-head" style="text-align:left;margin-bottom:1.5rem">
        <div class="eyebrow">Get in touch</div>
        <h2>Let us plan with you</h2>
      </div>
      <p style="color:var(--muted);margin-bottom:1.5rem">Call, email or send a message. We help with full events or single services.</p>
      <ul style="display:grid;gap:.8rem">
        <li><strong>Location:</strong> ' . htmlspecialchars($address) . '</li>
        <li><strong>Phone Number:</strong> <a href="tel:' . preg_replace('/[^0-9+]/', '', $phone) . '" style="color:var(--navy);text-decoration:none">' . htmlspecialchars($phone) . '</a></li>
        <li><strong>Email:</strong> <a href="mailto:' . htmlspecialchars($email) . '" style="color:var(--navy);text-decoration:none">' . htmlspecialchars($email) . '</a></li>
        <li><strong>WhatsApp:</strong> <a href="https://wa.me/' . preg_replace('/[^0-9]/', '', $whatsapp) . '" target="_blank" style="color:var(--navy);text-decoration:none">' . htmlspecialchars($whatsapp) . '</a></li>
      </ul>
      <div style="margin-top:2rem">
        <h3 style="font-family:var(--font-display);font-size:1.3rem;color:var(--navy);margin-bottom:1rem">Follow Us</h3>
        <div style="display:flex;gap:1rem;flex-wrap:wrap">
          <a href="' . htmlspecialchars($facebook) . '" target="_blank" style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;background:#1877f2;color:var(--white);border-radius:50%;text-decoration:none;font-size:1.2rem">f</a>
          <a href="' . htmlspecialchars($twitter) . '" target="_blank" style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;background:#1da1f2;color:var(--white);border-radius:50%;text-decoration:none;font-size:1.2rem">𝕏</a>
          <a href="' . htmlspecialchars($instagram) . '" target="_blank" style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;background:linear-gradient(45deg,#f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%);color:var(--white);border-radius:50%;text-decoration:none;font-size:1.2rem;padding:0">
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
          <a href="' . htmlspecialchars($tiktok) . '" target="_blank" style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;background:#000;color:var(--white);border-radius:50%;text-decoration:none;font-size:1.2rem;padding:0">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="12" cy="12" r="12" fill="#000"/>
              <path d="M12.5 4.5v9.5c0 1.5-1 2.5-2.5 2.5s-2.5-1-2.5-2.5 1-2.5 2.5-2.5h.5v-3h-.5c-3 0-5.5 2.5-5.5 5.5s2.5 5.5 5.5 5.5 5.5-2.5 5.5-5.5v-6.5c1 1 2.5 1.5 4 1.5v-3c-1.5 0-3-1-4-2.5z" fill="white"/>
              <path d="M12.5 4.5v9.5c0 1.5-1 2.5-2.5 2.5s-2.5-1-2.5-2.5 1-2.5 2.5-2.5h.5v-3h-.5c-3 0-5.5 2.5-5.5 5.5s2.5 5.5 5.5 5.5 5.5-2.5 5.5-5.5v-6.5c1 1 2.5 1.5 4 1.5v-3c-1.5 0-3-1-4-2.5z" fill="#ff0050" fill-opacity="0.3" transform="translate(1,1)"/>
            </svg>
          </a>
        </div>
      </div>
    </div>
    <form class="form-card" id="contactForm">
      <div id="contactAlert"></div>
      <div class="form-grid">
        <div class="form-group"><label>Name</label><input name="name" required /></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" required /></div>
        <div class="form-group"><label>Phone</label><input name="phone" /></div>
        <div class="form-group"><label>Subject</label><input name="subject" /></div>
        <div class="form-group full"><label>Message</label><textarea name="message" rows="5" required></textarea></div>
      </div>
      <button class="btn btn-gold" style="margin-top:1rem" type="submit">Send Message</button>
    </form>
  </div>
</section>
<script>
document.getElementById("contactForm").addEventListener("submit", function(e){
  e.preventDefault();
  var fd = new FormData(e.target);
  var body = {};
  fd.forEach(function(v,k){ body[k]=v; });
  var alertBox = document.getElementById("contactAlert");
  apiPost("contact", body).then(function(res){
    alertBox.className = "alert " + (res.success ? "alert-success" : "alert-error");
    alertBox.textContent = res.message || (res.success ? "Message sent!" : "Failed");
    if(res.success) e.target.reset();
  });
});
</script>';

mk_page('Contact', $content, 'contact');
