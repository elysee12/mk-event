<?php
require_once __DIR__ . '/../includes/layout.php';
$service = isset($_GET['service']) ? htmlspecialchars($_GET['service']) : 'mc';
$id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '';

$content = '
<section class="page-hero"><div><h1>Booking</h1><p>Reserve MK Event Company services for your date.</p></div></section>
<section class="section">
  <div class="container" style="max-width:820px">
    <form class="form-card" id="bookingForm">
      <div id="bookingAlert"></div>
      <div class="form-grid">
        <div class="form-group"><label>Full Name</label><input name="client_name" required /></div>
        <div class="form-group"><label>Email</label><input type="email" name="client_email" required /></div>
        <div class="form-group"><label>Phone</label><input name="client_phone" required /></div>
        <div class="form-group"><label>Service</label>
          <select name="service_type" id="serviceType">
            <option value="smoke_machine">Smoke Machine</option>
            <option value="fireworks">Fireworks</option>
            <option value="photobooth">360 Photobooth</option>
            <option value="mc">MC (MK)</option>
            <option value="abatahira">Abatahira (MK)</option>
            <option value="cakes">Cakes (MK)</option>
            <option value="photographers">Photographers (MK)</option>
            <option value="catering">Catering (MK)</option>
            <option value="decoration">Decoration (MK)</option>
            <option value="sonalization">Sonalization (MK)</option>
            <option value="itorero_band">Itorero & Band (MK)</option>
            <option value="service_protocol">Service and Protocol (MK)</option>
            <option value="other">Other / Full Event</option>
          </select>
        </div>
        <div class="form-group"><label>Event Date</label><input type="date" name="event_date" /></div>
        <div class="form-group"><label>Guests</label><input type="number" name="guests" min="0" /></div>
        <div class="form-group full"><label>Event Location</label><input name="event_location" /></div>
        <div class="form-group full"><label>Message</label><textarea name="message" rows="4"></textarea></div>
      </div>
      <input type="hidden" name="service_id" id="serviceId" value="' . $id . '" />
      <button class="btn btn-gold" style="margin-top:1.2rem" type="submit">Submit Booking</button>
    </form>
  </div>
</section>
<script>
document.getElementById("serviceType").value = "' . $service . '";
document.getElementById("bookingForm").addEventListener("submit", function(e){
  e.preventDefault();
  var fd = new FormData(e.target);
  var body = {};
  fd.forEach(function(v,k){ body[k]=v; });
  body.guests = body.guests ? Number(body.guests) : 0;
  body.service_id = body.service_id ? Number(body.service_id) : null;
  var alertBox = document.getElementById("bookingAlert");
  apiPost("bookings", body).then(function(res){
    alertBox.className = "alert " + (res.success ? "alert-success" : "alert-error");
    alertBox.textContent = res.message || (res.success ? "Booking submitted!" : "Failed");
    if(res.success) e.target.reset();
  }).catch(function(){
    alertBox.className = "alert alert-error";
    alertBox.textContent = "Could not reach API. Start Apache + MySQL.";
  });
});
</script>';

mk_page('Booking', $content, 'booking');
