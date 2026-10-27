<?php
require_once __DIR__ . '/../includes/layout.php';

$content = '
<section class="page-hero"><div><h1>Forgot Password</h1><p>Reset your admin password</p></div></section>
<section class="section">
  <div class="container" style="max-width:500px">
    <div class="form-card">
      <div id="forgotAlert"></div>
      <form id="forgotForm">
        <div class="form-group"><label>Email</label><input type="email" name="email" required placeholder="admin email" /></div>
        <button class="btn btn-gold" style="margin-top:1rem" type="submit" id="forgotBtn">Send Reset Link</button>
      </form>
      <p class="muted" style="margin-top:1rem;font-size:.88rem">The link is sent to the admin email. Check the Spam folder too. It expires in 1 hour.</p>
      <p style="margin-top:1rem"><a href="' . mk_url('/pages/admin.php') . '">&larr; Back to Login</a></p>
    </div>
  </div>
</section>
<script>
document.getElementById("forgotForm").addEventListener("submit", function(e){
  e.preventDefault();
  var fd = new FormData(e.target);
  var body = {};
  fd.forEach(function(v,k){ body[k]=v; });
  var alertBox = document.getElementById("forgotAlert");
  var btn = document.getElementById("forgotBtn");
  btn.disabled = true;
  btn.textContent = "Sending...";
  function done(){ btn.disabled = false; btn.textContent = "Send Reset Link"; }
  apiPost("admin/forgot-password", body).then(function(res){
    alertBox.className = "alert " + (res.success ? "alert-success" : "alert-error");
    alertBox.textContent = res.message || (res.success ? "Reset link sent!" : "Failed");
    if(res.success) e.target.reset();
    done();
  }).catch(function(){
    alertBox.className = "alert alert-error";
    alertBox.textContent = "The server could not be reached. Please try again.";
    done();
  });
});
</script>';

mk_page('Forgot Password', $content, '');
