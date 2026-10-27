<?php
require_once __DIR__ . '/../includes/layout.php';

$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$validShape = preg_match('/^[a-f0-9]{64}$/', $token);
$tokenSafe = htmlspecialchars($token, ENT_QUOTES, 'UTF-8');

if (!$validShape) {
    $content = '
<section class="page-hero"><div><h1>Reset Password</h1><p>Choose a new admin password</p></div></section>
<section class="section">
  <div class="container" style="max-width:500px">
    <div class="form-card">
      <div class="alert alert-error">This reset link is invalid or has expired. Request a new one using your email.</div>
      <p style="margin-top:1rem"><a href="' . mk_url('/pages/forgot-password.php') . '">→ Request a new link</a></p>
      <p class="sub" style="margin-bottom:0"><a href="' . mk_url('/pages/admin.php') . '">← Back to Login</a></p>
    </div>
  </div>
</section>';
} else {
    $content = '
<section class="page-hero"><div><h1>Reset Password</h1><p>Choose a new admin password</p></div></section>
<section class="section">
  <div class="container" style="max-width:500px">
    <div class="form-card">
      <div id="resetAlert"></div>
      <form id="resetForm">
        <div class="form-group"><label>New Password</label><input type="password" name="new_password" minlength="6" required autocomplete="new-password" /></div>
        <div class="form-group"><label>Confirm Password</label><input type="password" name="confirm_password" minlength="6" required autocomplete="new-password" /></div>
        <button class="btn btn-gold" style="margin-top:1rem" type="submit">Save New Password</button>
      </form>
      <p style="margin-top:1rem"><a href="' . mk_url('/pages/admin.php') . '">← Back to Login</a></p>
    </div>
  </div>
</section>
<script>
document.getElementById("resetForm").addEventListener("submit", function(e){
  e.preventDefault();
  var fd = new FormData(e.target);
  var np = fd.get("new_password"), cp = fd.get("confirm_password");
  var alertBox = document.getElementById("resetAlert");
  function say(ok, msg){
    alertBox.className = "alert " + (ok ? "alert-success" : "alert-error");
    alertBox.textContent = msg;
  }
  if (np !== cp) { say(false, "Both passwords must match."); return; }
  if (np.length < 6) { say(false, "The password must be at least 6 characters."); return; }
  apiPost("admin/reset-password", { token: "' . $tokenSafe . '", new_password: np }).then(function(res){
    if (res.success) {
      say(true, "Done! Your password has been changed. Going to admin.php in 3 seconds...");
      setTimeout(function(){ window.location.href = "' . mk_url('/pages/admin.php') . '"; }, 3000);
    } else {
      say(false, res.message || "Something went wrong. Please try again.");
    }
  });
});
</script>';
}

mk_page('Reset Password', $content, '');
