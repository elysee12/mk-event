window.addEventListener('scroll', function () {
  var header = document.getElementById('siteHeader');
  if (!header) return;
  if (window.scrollY > 40) header.classList.add('scrolled');
  else if (!header.classList.contains('force-solid')) header.classList.remove('scrolled');
});

document.addEventListener('DOMContentLoaded', function () {
  var btn = document.getElementById('menuBtn');
  var nav = document.getElementById('navLinks');
  if (btn && nav) {
    btn.addEventListener('click', function () {
      nav.classList.toggle('open');
    });
  }

  var drops = document.querySelectorAll('.nav-drop');
  for (var i = 0; i < drops.length; i++) {
    (function (drop) {
      var trigger = drop.querySelector('.nav-drop-btn');
      if (!trigger) return;
      trigger.addEventListener('click', function (e) {
        e.preventDefault();
        drop.classList.toggle('open');
      });
    })(drops[i]);
  }

  var header = document.getElementById('siteHeader');
  if (header && !document.querySelector('.home-hero')) {
    header.classList.add('scrolled', 'force-solid');
  }

  var slider = document.getElementById('heroSlider');
  if (slider) {
    var slides = slider.querySelectorAll('.slide');
    var dots = slider.querySelectorAll('.dot');
    var currentSlide = 0;
    var slideInterval;

    function showSlide(index) {
      slides.forEach(function(s) { s.classList.remove('active'); });
      dots.forEach(function(d) { d.classList.remove('active'); });
      slides[index].classList.add('active');
      dots[index].classList.add('active');
      currentSlide = index;
    }

    function nextSlide() {
      var next = (currentSlide + 1) % slides.length;
      showSlide(next);
    }

    dots.forEach(function(dot, index) {
      dot.addEventListener('click', function () {
        clearInterval(slideInterval);
        showSlide(index);
        slideInterval = setInterval(nextSlide, 10000);
      });
    });

    slideInterval = setInterval(nextSlide, 10000);
  }
});

var API = (typeof MK_BASE !== 'undefined' ? MK_BASE : '/MK') + '/api';

// A PHP fatal returns an empty body, and r.json() on it throws an opaque
// SyntaxError - so turn any non-JSON reply into a message the admin can act on.
function apiJson(res) {
  return res.text().then(function (text) {
    try {
      return JSON.parse(text);
    } catch (e) {
      return {
        success: false,
        message: 'The server replied with an invalid response (HTTP ' + res.status + '). Check the server error log or contact your hosting provider.'
      };
    }
  });
}

function apiGet(path) {
  return fetch(API + '/index.php?path=' + path).then(apiJson);
}

function apiPost(path, body) {
  var options = {
    method: 'POST',
    body: body
  };
  
  // If body is FormData, let browser set Content-Type automatically
  if (!(body instanceof FormData)) {
    options.headers = { 'Content-Type': 'application/json' };
    options.body = JSON.stringify(body);
  }
  
  return fetch(API + '/index.php?path=' + path, options).then(apiJson);
}

function apiPut(path, body) {
  // PHP only parses a multipart body on POST, so a FormData update has to ride on
  // POST with a _method override or the server receives an empty payload.
  var isForm = (typeof FormData !== 'undefined') && (body instanceof FormData);
  var options;

  if (isForm) {
    body.append('_method', 'PUT');
    options = { method: 'POST', body: body };
  } else {
    options = {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body)
    };
  }

  return fetch(API + '/index.php?path=' + path, options).then(apiJson);
}

function apiDelete(path) {
  return fetch(API + '/index.php?path=' + path, { method: 'DELETE' }).then(apiJson);
}
