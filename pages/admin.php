<?php
/**
 * MK Event Company - Admin Dashboard
 */
require_once __DIR__ . '/../includes/layout.php';
$base = MK_BASE;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin | MK Event Company</title>
  <link rel="icon" type="image/png" href="<?php echo $base; ?>/assets/logo.png" />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/style.css?v=<?php echo (int) @filemtime(__DIR__ . '/../assets/css/style.css'); ?>" />
</head>
<body>
<div id="app"></div>
<script>var MK_BASE = <?php echo json_encode($base); ?>;</script>
<script src="<?php echo $base; ?>/assets/js/main.js?v=<?php echo (int) @filemtime(__DIR__ . '/../assets/js/main.js'); ?>"></script>
<script>
(function(){
  var token = localStorage.getItem('mk_admin_token') || '';
  var admin = null;
  try { admin = JSON.parse(localStorage.getItem('mk_admin') || 'null'); } catch(e) {}
  var tab = 'dashboard';
  var root = document.getElementById('app');

  function el(html){ var d=document.createElement('div'); d.innerHTML=html; return d.firstChild; }

  function renderLogin(){
    root.innerHTML = '';
    var box = el('<div class="login-page"><form class="login-box"><h1>MK Admin</h1><p class="sub">Sign in to manage MK Event Company</p><div id="err"></div><div class="form-group" style="margin-bottom:.8rem"><label>Username</label><input id="user" value="admin" required /></div><div class="form-group" style="margin-bottom:1rem"><label>Password</label><input id="pass" type="password" value="password" required /></div><button class="btn btn-gold" style="width:100%" type="submit">Login</button><p class="sub" style="margin-top:1rem;margin-bottom:0"><a href="'+(MK_BASE||'')+'/pages/forgot-password.php'" style="color:var(--gold);text-decoration:none">Forgot Password?</a></p><p class="sub" style="margin-bottom:0">Demo: admin / password</p></form></div>');
    root.appendChild(box);
    box.querySelector('form').onsubmit = function(e){
      e.preventDefault();
      apiPost('admin/login', { username: document.getElementById('user').value, password: document.getElementById('pass').value })
        .then(function(res){
          if(res.success){
            localStorage.setItem('mk_admin_token', res.token);
            localStorage.setItem('mk_admin', JSON.stringify(res.admin));
            token = res.token; admin = res.admin; renderApp();
          } else {
            document.getElementById('err').innerHTML = '<div class="alert alert-error">'+(res.message||'Login failed')+'</div>';
          }
        });
    };
  }

  function renderApp(){
    root.innerHTML = '';
    var shell = el('<div class="admin-shell"><aside class="admin-side"><h2>MK Dashboard</h2><p style="padding:0 .5rem;margin-bottom:1rem;opacity:.7;font-size:.85rem">'+(admin&&admin.full_name?admin.full_name:'Administrator')+'</p><a href="#" data-tab="dashboard">Dashboard</a><a href="#" data-tab="bookings">Bookings</a><a href="#" data-tab="mcs">MCs</a><a href="#" data-tab="photographers">Photographers</a><a href="#" data-tab="catering">Catering</a><a href="#" data-tab="decorations">Decorations</a><a href="#" data-tab="cakes">Cakes</a><a href="#" data-tab="abatahira">Abatahira</a><a href="#" data-tab="itorero-bands">Itorero & Bands</a><a href="#" data-tab="extra-services">Extra Services</a><a href="#" data-tab="saxophone">Saxophone</a><a href="#" data-tab="sonalization">Sonalization</a><a href="#" data-tab="service-protocols">Service and Protocol</a><a href="#" data-tab="gallery">Gallery</a><a href="#" data-tab="testimonials">Testimonials</a><a href="#" data-tab="settings">Settings</a><a href="#" data-tab="messages">Messages</a><a href="#" data-tab="account">Account</a><a href="#" id="logout" style="margin-top:1.5rem">Logout</a><a href="'+MK_BASE+'/">&larr; Website</a></aside><main class="admin-main" id="adminMain"></main></div>');
    root.appendChild(shell);
    shell.querySelectorAll('[data-tab]').forEach(function(a){
      a.onclick = function(e){ e.preventDefault(); tab=a.getAttribute('data-tab'); loadTab(); };
    });
    document.getElementById('logout').onclick = function(e){
      e.preventDefault();
      localStorage.removeItem('mk_admin_token'); localStorage.removeItem('mk_admin'); token=''; renderLogin();
    };
    loadTab();
  }

  function loadTab(){
    var main = document.getElementById('adminMain');
    main.innerHTML = '<div class="loading">Loading...</div>';
    document.querySelectorAll('.admin-side a[data-tab]').forEach(function(a){
      a.classList.remove('active');
      if(a.getAttribute('data-tab')===tab) a.classList.add('active');
    });
    if(tab==='dashboard'){
      apiGet('stats').then(function(res){
        var s = res.data || {};
        var keys = [['Bookings',s.bookings],['Pending',s.pending_bookings],['MCs',s.mcs],['Cakes',s.cakes],['Photographers',s.photographers],['Catering',s.catering],['Decorations',s.decorations],['Abatahira',s.abatahira],['Itorero/Bands',s.itorero_bands],['Extra Services',s.extra_services],['Gallery',s.gallery],['Testimonials',s.testimonials],['Messages',s.messages]];
        var html = '<h2 style="font-family:var(--font-display);margin-bottom:1rem">Overview</h2><div class="stats-grid">';
        keys.forEach(function(k){ html += '<div class="stat-box"><span>'+k[0]+'</span><strong>'+(k[1]||0)+'</strong></div>'; });
        html += '</div>';
        main.innerHTML = html;
      });
    }
    if(tab==='bookings'){
      apiGet('bookings').then(function(res){
        var rows = (res.data||[]).map(function(b){
          return '<tr><td>'+b.client_name+'<br><small>'+b.client_phone+'</small></td><td>'+b.service_type+'</td><td>'+(b.event_date||'â€”')+'</td><td>'+b.status+'</td><td><select data-id="'+b.id+'"><option value="pending">pending</option><option value="confirmed">confirmed</option><option value="cancelled">cancelled</option><option value="completed">completed</option></select></td></tr>';
        }).join('');
        main.innerHTML = '<h2 style="font-family:var(--font-display);margin-bottom:1rem">Bookings</h2><div style="overflow-x:auto"><table class="admin-table"><thead><tr><th>Client</th><th>Service</th><th>Date</th><th>Status</th><th>Action</th></tr></thead><tbody>'+rows+'</tbody></table></div>';
        main.querySelectorAll('select[data-id]').forEach(function(sel){
          sel.value = (res.data||[]).filter(function(b){return String(b.id)===sel.getAttribute('data-id');})[0].status;
          sel.onchange = function(){ apiPut('bookings/'+sel.getAttribute('data-id'), {status: sel.value}).then(loadTab); };
        });
      });
    }
    if(tab==='mcs'){
      apiGet('mcs').then(function(res){
        var rows = (res.data||[]).map(function(m){
          return '<tr><td>'+m.name+'</td><td>'+(m.experience||'')+'</td><td>'+m.price+'</td><td><button class="btn btn-gold" data-edit="'+m.id+'">Edit</button> <button class="btn btn-dark" data-del="'+m.id+'">Delete</button></td></tr>';
        }).join('');
        main.innerHTML = '<h2 style="font-family:var(--font-display);margin-bottom:1rem">Manage MCs</h2><form class="form-card" id="mcForm" style="margin-bottom:1.5rem" enctype="multipart/form-data"><div class="form-grid"><div class="form-group"><label>Name</label><input name="name" required /></div><div class="form-group"><label>Experience</label><input name="experience" /></div><div class="form-group"><label>Phone</label><input name="phone" /></div><div class="form-group"><label>Email</label><input name="email" /></div><div class="form-group"><label>Price</label><input type="number" name="price" /></div><div class="form-group full"><label>Photo</label><input type="file" name="photo" accept="image/*" /></div><div class="form-group full"><label>Bio</label><textarea name="bio"></textarea></div></div><button class="btn btn-dark" style="margin-top:1rem">Add MC</button></form><table class="admin-table"><thead><tr><th>Name</th><th>Experience</th><th>Price</th><th>Action</th></tr></thead><tbody>'+rows+'</tbody></table>';
        document.getElementById('mcForm').onsubmit = function(e){
          e.preventDefault();
          var fd=new FormData(e.target);
          fd.append('status', 'active');
          var priceVal = fd.get('price');
          if(priceVal) fd.set('price', Number(priceVal));
          fd.delete('rating');
          fd.delete('reviews');
          console.log('FormData entries:');
          for(var pair of fd.entries()){
            console.log(pair[0], pair[1]);
          }
          apiPost('mcs', fd).then(function(res){
            console.log('Response:', res);
            if(res.success){
              var msg = 'MC added successfully!';
              if(res.data && res.data.photo){
                msg += ' Photo: ' + res.data.photo;
              }
              alert(msg);
              e.target.reset();
              loadTab();
            } else {
              alert('Error: ' + (res.message || 'Failed to add'));
            }
          }).catch(function(err){
            console.error('Error:', err);
            alert('Error: ' + err);
          });
        };
        main.querySelectorAll('[data-del]').forEach(function(btn){
          btn.onclick = function(){ apiDelete('mcs/'+btn.getAttribute('data-del')).then(loadTab); };
        });
        main.querySelectorAll('[data-edit]').forEach(function(btn){
          btn.onclick = function(){
            var id = btn.getAttribute('data-edit');
            apiGet('mcs/'+id).then(function(res){
              var m = res.data;
              document.getElementById('mcForm').querySelector('[name="name"]').value = m.name||'';
              document.getElementById('mcForm').querySelector('[name="experience"]').value = m.experience||'';
              document.getElementById('mcForm').querySelector('[name="phone"]').value = m.phone||'';
              document.getElementById('mcForm').querySelector('[name="email"]').value = m.email||'';
              document.getElementById('mcForm').querySelector('[name="price"]').value = m.price||'';
              document.getElementById('mcForm').querySelector('[name="bio"]').value = m.bio||'';
              if(m.photo){
                var photoHtml = '<div class="form-group full"><label>Current Photo</label><img src="'+(MK_BASE||'')+'/uploads/mcs/'+m.photo+'" style="max-width:200px;height:auto;margin-bottom:0.5rem" /><br><small>Leave blank to keep current photo</small></div>';
                document.getElementById('mcForm').querySelector('.form-grid').insertAdjacentHTML('beforeend', photoHtml);
              }
              document.getElementById('mcForm').querySelector('button').textContent = 'Update MC';
              document.getElementById('mcForm').onsubmit = function(e){
                e.preventDefault();
                var fd=new FormData(e.target);
                fd.append('status', 'active');
                fd.append('price', Number(fd.get('price')||0));
                fd.delete('rating');
                fd.delete('reviews');
                apiPut('mcs/'+id, fd).then(function(){
                  document.getElementById('mcForm').reset();
                  document.getElementById('mcForm').querySelector('button').textContent = 'Add MC';
                  loadTab();
                });
              };
            });
          };
        });
      });
    }
    if(tab==='cakes'){
      apiGet('cakes').then(function(res){
        var rows = (res.data||[]).map(function(c){
          return '<tr><td>'+c.name+'</td><td>'+c.cake_type+'</td><td>'+c.size+'</td><td>'+c.price+'</td><td><button class="btn btn-gold" data-edit="'+c.id+'">Edit</button> <button class="btn btn-dark" data-del="'+c.id+'">Delete</button></td></tr>';
        }).join('');
        main.innerHTML = '<h2 style="font-family:var(--font-display);margin-bottom:1rem">Manage Cakes</h2><form class="form-card" id="cakeForm" style="margin-bottom:1.5rem" enctype="multipart/form-data"><div class="form-grid"><div class="form-group"><label>Name</label><input name="name" required /></div><div class="form-group"><label>Type</label><input name="cake_type" /></div><div class="form-group"><label>Size</label><input name="size" /></div><div class="form-group"><label>Price</label><input type="number" name="price" /></div><div class="form-group full"><label>Photo</label><input type="file" name="photo" accept="image/*" /></div><div class="form-group full"><label>Description</label><textarea name="description"></textarea></div></div><button class="btn btn-dark" style="margin-top:1rem">Add Cake</button></form><table class="admin-table"><thead><tr><th>Name</th><th>Type</th><th>Size</th><th>Price</th><th>Action</th></tr></thead><tbody>'+rows+'</tbody></table>';
        document.getElementById('cakeForm').onsubmit = function(e){
          e.preventDefault();
          var fd=new FormData(e.target);
          fd.append('status', 'available');
          fd.append('custom_order', '1');
          fd.append('price', Number(fd.get('price')||0));
          apiPost('cakes', fd).then(function(res){
            if(res.success){
              e.target.reset();
              loadTab();
            } else {
              alert('Error: ' + (res.message || 'Failed to add'));
            }
          }).catch(function(err){
            alert('Error: ' + err);
          });
        };
        main.querySelectorAll('[data-del]').forEach(function(btn){
          btn.onclick = function(){ apiDelete('cakes/'+btn.getAttribute('data-del')).then(loadTab); };
        });
        main.querySelectorAll('[data-edit]').forEach(function(btn){
          btn.onclick = function(){
            var id = btn.getAttribute('data-edit');
            apiGet('cakes/'+id).then(function(res){
              var c = res.data;
              document.getElementById('cakeForm').querySelector('[name="name"]').value = c.name||'';
              document.getElementById('cakeForm').querySelector('[name="cake_type"]').value = c.cake_type||'';
              document.getElementById('cakeForm').querySelector('[name="size"]').value = c.size||'';
              document.getElementById('cakeForm').querySelector('[name="price"]').value = c.price||'';
              document.getElementById('cakeForm').querySelector('[name="description"]').value = c.description||'';
              if(c.photo){
                var photoHtml = '<div class="form-group full"><label>Current Photo</label><img src="'+(MK_BASE||'')+'/uploads/cakes/'+c.photo+'" style="max-width:200px;height:auto;margin-bottom:0.5rem" /><br><small>Leave blank to keep current photo</small></div>';
                document.getElementById('cakeForm').querySelector('.form-grid').insertAdjacentHTML('beforeend', photoHtml);
              }
              document.getElementById('cakeForm').querySelector('button').textContent = 'Update Cake';
              document.getElementById('cakeForm').onsubmit = function(e){
                e.preventDefault();
                var fd=new FormData(e.target);
                fd.append('status', 'available');
                fd.append('custom_order', '1');
                fd.append('price', Number(fd.get('price')||0));
                apiPut('cakes/'+id, fd).then(function(){
                  document.getElementById('cakeForm').reset();
                  document.getElementById('cakeForm').querySelector('button').textContent = 'Add Cake';
                  loadTab();
                });
              };
            });
          };
        });
      });
    }
    if(tab==='messages'){
      apiGet('contact').then(function(res){
        var rows = (res.data||[]).map(function(m){
          return '<tr><td>'+m.name+'</td><td>'+m.email+'</td><td>'+(m.subject||'')+'</td><td>'+m.message+'</td><td>'+(m.is_read=='0'||m.is_read==0?'<button class="btn btn-gold" data-read="'+m.id+'">Mark read</button>':'')+'</td></tr>';
        }).join('');
        main.innerHTML = '<h2 style="font-family:var(--font-display);margin-bottom:1rem">Contact Messages</h2><table class="admin-table"><thead><tr><th>Name</th><th>Email</th><th>Subject</th><th>Message</th><th>Action</th></tr></thead><tbody>'+rows+'</tbody></table>';
        main.querySelectorAll('[data-read]').forEach(function(btn){
          btn.onclick = function(){ apiPut('contact/'+btn.getAttribute('data-read'), {}).then(loadTab); };
        });
      });
    }
    if(tab==='photographers'){
      apiGet('photographers').then(function(res){
        var rows = (res.data||[]).map(function(p){
          return '<tr><td>'+p.name+'</td><td>'+(p.package_name||'')+'</td><td>'+p.price+'</td><td><button class="btn btn-gold" data-edit="'+p.id+'">Edit</button> <button class="btn btn-dark" data-del="'+p.id+'">Delete</button></td></tr>';
        }).join('');
        main.innerHTML = '<h2 style="font-family:var(--font-display);margin-bottom:1rem">Manage Photographers</h2><form class="form-card" id="photoForm" style="margin-bottom:1.5rem" enctype="multipart/form-data"><div class="form-grid"><div class="form-group"><label>Name</label><input name="name" required /></div><div class="form-group"><label>Package Name</label><input name="package_name" /></div><div class="form-group"><label>Phone</label><input name="phone" /></div><div class="form-group"><label>Price</label><input type="number" name="price" /></div><div class="form-group full"><label>Photo</label><input type="file" name="photo" accept="image/*" /></div><div class="form-group full"><label>Portfolio</label><textarea name="portfolio"></textarea></div><div class="form-group full"><label>Bio</label><textarea name="bio"></textarea></div></div><button class="btn btn-dark" style="margin-top:1rem">Add Photographer</button></form><table class="admin-table"><thead><tr><th>Name</th><th>Package</th><th>Price</th><th>Action</th></tr></thead><tbody>'+rows+'</tbody></table>';
        document.getElementById('photoForm').onsubmit = function(e){
          e.preventDefault();
          var fd=new FormData(e.target);
          fd.append('status', 'active');
          fd.append('price', Number(fd.get('price')||0));
          apiPost('photographers', fd).then(function(res){
            if(res.success){
              e.target.reset();
              loadTab();
            } else {
              alert('Error: ' + (res.message || 'Failed to add'));
            }
          }).catch(function(err){
            alert('Error: ' + err);
          });
        };
        main.querySelectorAll('[data-del]').forEach(function(btn){
          btn.onclick = function(){ apiDelete('photographers/'+btn.getAttribute('data-del')).then(loadTab); };
        });
        main.querySelectorAll('[data-edit]').forEach(function(btn){
          btn.onclick = function(){
            var id = btn.getAttribute('data-edit');
            apiGet('photographers/'+id).then(function(res){
              var p = res.data;
              document.getElementById('photoForm').querySelector('[name="name"]').value = p.name||'';
              document.getElementById('photoForm').querySelector('[name="package_name"]').value = p.package_name||'';
              document.getElementById('photoForm').querySelector('[name="phone"]').value = p.phone||'';
              document.getElementById('photoForm').querySelector('[name="price"]').value = p.price||'';
              document.getElementById('photoForm').querySelector('[name="portfolio"]').value = p.portfolio||'';
              document.getElementById('photoForm').querySelector('[name="bio"]').value = p.bio||'';
              if(p.photo){
                var photoHtml = '<div class="form-group full"><label>Current Photo</label><img src="'+(MK_BASE||'')+'/uploads/photographers/'+p.photo+'" style="max-width:200px;height:auto;margin-bottom:0.5rem" /><br><small>Leave blank to keep current photo</small></div>';
                document.getElementById('photoForm').querySelector('.form-grid').insertAdjacentHTML('beforeend', photoHtml);
              }
              document.getElementById('photoForm').querySelector('button').textContent = 'Update Photographer';
              document.getElementById('photoForm').onsubmit = function(e){
                e.preventDefault();
                var fd=new FormData(e.target);
                fd.append('status', 'active');
                fd.append('price', Number(fd.get('price')||0));
                apiPut('photographers/'+id, fd).then(function(res){
                  if(res.success){
                    document.getElementById('photoForm').reset();
                    document.getElementById('photoForm').querySelector('button').textContent = 'Add Photographer';
                    loadTab();
                  } else {
                    alert('Error: ' + (res.message || 'Failed to update'));
                  }
                }).catch(function(err){
                  alert('Error: ' + err);
                });
              };
            });
          };
        });
      });
    }
    if(tab==='catering'){
      apiGet('catering').then(function(res){
        var rows = (res.data||[]).map(function(c){
          return '<tr><td>'+c.package_name+'</td><td>'+c.people_count+'</td><td>'+c.price+'</td><td><button class="btn btn-gold" data-edit="'+c.id+'">Edit</button> <button class="btn btn-dark" data-del="'+c.id+'">Delete</button></td></tr>';
        }).join('');
        main.innerHTML = '<h2 style="font-family:var(--font-display);margin-bottom:1rem">Manage Catering</h2><form class="form-card" id="caterForm" style="margin-bottom:1.5rem" enctype="multipart/form-data"><div class="form-grid"><div class="form-group"><label>Package Name</label><input name="package_name" required /></div><div class="form-group"><label>People Count</label><input type="number" name="people_count" value="50" /></div><div class="form-group"><label>Price</label><input type="number" name="price" /></div><div class="form-group full"><label>Photo</label><input type="file" name="photo" accept="image/*" /></div><div class="form-group full"><label>Menu Items</label><textarea name="menu_items"></textarea></div><div class="form-group full"><label>Description</label><textarea name="description"></textarea></div></div><button class="btn btn-dark" style="margin-top:1rem">Add Package</button></form><table class="admin-table"><thead><tr><th>Package</th><th>People</th><th>Price</th><th>Action</th></tr></thead><tbody>'+rows+'</tbody></table>';
        document.getElementById('caterForm').onsubmit = function(e){
          e.preventDefault();
          var fd=new FormData(e.target);
          fd.append('status', 'available');
          fd.append('people_count', Number(fd.get('people_count')||50));
          fd.append('price', Number(fd.get('price')||0));
          apiPost('catering', fd).then(function(res){
            if(res.success){
              e.target.reset();
              loadTab();
            } else {
              alert('Error: ' + (res.message || 'Failed to add'));
            }
          }).catch(function(err){
            alert('Error: ' + err);
          });
        };
        main.querySelectorAll('[data-del]').forEach(function(btn){
          btn.onclick = function(){ apiDelete('catering/'+btn.getAttribute('data-del')).then(loadTab); };
        });
        main.querySelectorAll('[data-edit]').forEach(function(btn){
          btn.onclick = function(){
            var id = btn.getAttribute('data-edit');
            apiGet('catering/'+id).then(function(res){
              var c = res.data;
              document.getElementById('caterForm').querySelector('[name="package_name"]').value = c.package_name||'';
              document.getElementById('caterForm').querySelector('[name="people_count"]').value = c.people_count||'';
              document.getElementById('caterForm').querySelector('[name="price"]').value = c.price||'';
              document.getElementById('caterForm').querySelector('[name="menu_items"]').value = c.menu_items||'';
              document.getElementById('caterForm').querySelector('[name="description"]').value = c.description||'';
              if(c.photo){
                var photoHtml = '<div class="form-group full"><label>Current Photo</label><img src="'+(MK_BASE||'')+'/uploads/catering/'+c.photo+'" style="max-width:200px;height:auto;margin-bottom:0.5rem" /><br><small>Leave blank to keep current photo</small></div>';
                document.getElementById('caterForm').querySelector('.form-grid').insertAdjacentHTML('beforeend', photoHtml);
              }
              document.getElementById('caterForm').querySelector('button').textContent = 'Update Package';
              document.getElementById('caterForm').onsubmit = function(e){
                e.preventDefault();
                var fd=new FormData(e.target);
                fd.append('status', 'available');
                fd.append('people_count', Number(fd.get('people_count')||50));
                fd.append('price', Number(fd.get('price')||0));
                apiPut('catering/'+id, fd).then(function(res){
                  if(res.success){
                    document.getElementById('caterForm').reset();
                    document.getElementById('caterForm').querySelector('button').textContent = 'Add Package';
                    loadTab();
                  } else {
                    alert('Error: ' + (res.message || 'Failed to update'));
                  }
                }).catch(function(err){
                  alert('Error: ' + err);
                });
              };
            });
          };
        });
      });
    }
    if(tab==='decorations'){
      apiGet('decorations').then(function(res){
        var rows = (res.data||[]).map(function(d){
          return '<tr><td>'+d.title+'</td><td>'+d.category+'</td><td>'+(d.package_name||'')+'</td><td>'+d.price+'</td><td><button class="btn btn-gold" data-edit="'+d.id+'">Edit</button> <button class="btn btn-dark" data-del="'+d.id+'">Delete</button></td></tr>';
        }).join('');
        main.innerHTML = '<h2 style="font-family:var(--font-display);margin-bottom:1rem">Manage Decorations</h2><form class="form-card" id="decForm" style="margin-bottom:1.5rem" enctype="multipart/form-data"><div class="form-grid"><div class="form-group"><label>Title</label><input name="title" required /></div><div class="form-group"><label>Category</label><select name="category"><option value="wedding">Wedding</option><option value="birthday">Birthday</option><option value="introduction">Introduction</option><option value="corporate">Corporate</option><option value="other">Other</option></select></div><div class="form-group"><label>Package Name</label><input name="package_name" /></div><div class="form-group"><label>Price</label><input type="number" name="price" /></div><div class="form-group full"><label>Photo</label><input type="file" name="photo" accept="image/*" /></div><div class="form-group full"><label>Description</label><textarea name="description"></textarea></div></div><button class="btn btn-dark" style="margin-top:1rem">Add Decoration</button></form><table class="admin-table"><thead><tr><th>Title</th><th>Category</th><th>Package</th><th>Price</th><th>Action</th></tr></thead><tbody>'+rows+'</tbody></table>';
        document.getElementById('decForm').onsubmit = function(e){
          e.preventDefault();
          var fd=new FormData(e.target);
          fd.append('status', 'available');
          fd.append('price', Number(fd.get('price')||0));
          apiPost('decorations', fd).then(function(res){
            if(res.success){
              e.target.reset();
              loadTab();
            } else {
              alert('Error: ' + (res.message || 'Failed to add'));
            }
          }).catch(function(err){
            alert('Error: ' + err);
          });
        };
        main.querySelectorAll('[data-del]').forEach(function(btn){
          btn.onclick = function(){ apiDelete('decorations/'+btn.getAttribute('data-del')).then(loadTab); };
        });
        main.querySelectorAll('[data-edit]').forEach(function(btn){
          btn.onclick = function(){
            var id = btn.getAttribute('data-edit');
            apiGet('decorations/'+id).then(function(res){
              var d = res.data;
              document.getElementById('decForm').querySelector('[name="title"]').value = d.title||'';
              document.getElementById('decForm').querySelector('[name="category"]').value = d.category||'';
              document.getElementById('decForm').querySelector('[name="package_name"]').value = d.package_name||'';
              document.getElementById('decForm').querySelector('[name="price"]').value = d.price||'';
              document.getElementById('decForm').querySelector('[name="description"]').value = d.description||'';
              if(d.photo){
                var photoHtml = '<div class="form-group full"><label>Current Photo</label><img src="'+(MK_BASE||'')+'/uploads/decoration/'+d.photo+'" style="max-width:200px;height:auto;margin-bottom:0.5rem" /><br><small>Leave blank to keep current photo</small></div>';
                document.getElementById('decForm').querySelector('.form-grid').insertAdjacentHTML('beforeend', photoHtml);
              }
              document.getElementById('decForm').querySelector('button').textContent = 'Update Decoration';
              document.getElementById('decForm').onsubmit = function(e){
                e.preventDefault();
                var fd=new FormData(e.target);
                fd.append('status', 'available');
                fd.append('price', Number(fd.get('price')||0));
                apiPut('decorations/'+id, fd).then(function(res){
                  if(res.success){
                    document.getElementById('decForm').reset();
                    document.getElementById('decForm').querySelector('button').textContent = 'Add Decoration';
                    loadTab();
                  } else {
                    alert('Error: ' + (res.message || 'Failed to update'));
                  }
                }).catch(function(err){
                  alert('Error: ' + err);
                });
              };
            });
          };
        });
      });
    }
    if(tab==='abatahira'){
      apiGet('abatahira').then(function(res){
        var rows = (res.data||[]).map(function(a){
          return '<tr><td>'+a.name+'</td><td>'+(a.services||'')+'</td><td>'+a.price+'</td><td><button class="btn btn-gold" data-edit="'+a.id+'">Edit</button> <button class="btn btn-dark" data-del="'+a.id+'">Delete</button></td></tr>';
        }).join('');
        main.innerHTML = '<h2 style="font-family:var(--font-display);margin-bottom:1rem">Manage Abatahira</h2><form class="form-card" id="abaForm" style="margin-bottom:1.5rem" enctype="multipart/form-data"><div class="form-grid"><div class="form-group"><label>Name</label><input name="name" required /></div><div class="form-group"><label>Phone</label><input name="phone" /></div><div class="form-group"><label>Price</label><input type="number" name="price" /></div><div class="form-group full"><label>Photo</label><input type="file" name="photo" accept="image/*" /></div><div class="form-group full"><label>Services</label><textarea name="services"></textarea></div><div class="form-group full"><label>Bio</label><textarea name="bio"></textarea></div></div><button class="btn btn-dark" style="margin-top:1rem">Add Abatahira</button></form><table class="admin-table"><thead><tr><th>Name</th><th>Services</th><th>Price</th><th>Action</th></tr></thead><tbody>'+rows+'</tbody></table>';
        document.getElementById('abaForm').onsubmit = function(e){
          e.preventDefault();
          var fd=new FormData(e.target);
          fd.append('status', 'active');
          fd.append('price', Number(fd.get('price')||0));
          apiPost('abatahira', fd).then(loadTab);
        };
        main.querySelectorAll('[data-del]').forEach(function(btn){
          btn.onclick = function(){ apiDelete('abatahira/'+btn.getAttribute('data-del')).then(loadTab); };
        });
        main.querySelectorAll('[data-edit]').forEach(function(btn){
          btn.onclick = function(){
            var id = btn.getAttribute('data-edit');
            apiGet('abatahira/'+id).then(function(res){
              var a = res.data;
              document.getElementById('abaForm').querySelector('[name="name"]').value = a.name||'';
              document.getElementById('abaForm').querySelector('[name="phone"]').value = a.phone||'';
              document.getElementById('abaForm').querySelector('[name="price"]').value = a.price||'';
              document.getElementById('abaForm').querySelector('[name="services"]').value = a.services||'';
              document.getElementById('abaForm').querySelector('[name="bio"]').value = a.bio||'';
              if(a.photo){
                var photoHtml = '<div class="form-group full"><label>Current Photo</label><img src="'+(MK_BASE||'')+'/uploads/abatahira/'+a.photo+'" style="max-width:200px;height:auto;margin-bottom:0.5rem" /><br><small>Leave blank to keep current photo</small></div>';
                document.getElementById('abaForm').querySelector('.form-grid').insertAdjacentHTML('beforeend', photoHtml);
              }
              document.getElementById('abaForm').querySelector('button').textContent = 'Update Abatahira';
              document.getElementById('abaForm').onsubmit = function(e){
                e.preventDefault();
                var fd=new FormData(e.target);
                fd.append('status', 'active');
                fd.append('price', Number(fd.get('price')||0));
                apiPut('abatahira/'+id, fd).then(function(){
                  document.getElementById('abaForm').reset();
                  document.getElementById('abaForm').querySelector('button').textContent = 'Add Abatahira';
                  loadTab();
                });
              };
            });
          };
        });
      });
    }
    if(tab==='itorero-bands'){
      apiGet('itorero-bands').then(function(res){
        var rows = (res.data||[]).map(function(i){
          return '<tr><td>'+i.name+'</td><td>'+i.group_type+'</td><td>'+(i.package_name||'')+'</td><td>'+i.price+'</td><td><button class="btn btn-gold" data-edit="'+i.id+'">Edit</button> <button class="btn btn-dark" data-del="'+i.id+'">Delete</button></td></tr>';
        }).join('');
        main.innerHTML = '<h2 style="font-family:var(--font-display);margin-bottom:1rem">Manage Itorero & Bands</h2><form class="form-card" id="bandForm" style="margin-bottom:1.5rem" enctype="multipart/form-data"><div class="form-grid"><div class="form-group"><label>Name</label><input name="name" required /></div><div class="form-group"><label>Type</label><select name="group_type"><option value="itorero">Itorero</option><option value="band">Band</option><option value="music_group">Music Group</option></select></div><div class="form-group"><label>Package Name</label><input name="package_name" /></div><div class="form-group"><label>Phone</label><input name="phone" /></div><div class="form-group"><label>Price</label><input type="number" name="price" /></div><div class="form-group full"><label>Photo</label><input type="file" name="photo" accept="image/*" /></div><div class="form-group full"><label>Bio</label><textarea name="bio"></textarea></div></div><button class="btn btn-dark" style="margin-top:1rem">Add Group</button></form><table class="admin-table"><thead><tr><th>Name</th><th>Type</th><th>Package</th><th>Price</th><th>Action</th></tr></thead><tbody>'+rows+'</tbody></table>';
        document.getElementById('bandForm').onsubmit = function(e){
          e.preventDefault();
          var fd=new FormData(e.target);
          fd.append('status', 'active');
          fd.append('price', Number(fd.get('price')||0));
          apiPost('itorero-bands', fd).then(loadTab);
        };
        main.querySelectorAll('[data-del]').forEach(function(btn){
          btn.onclick = function(){ apiDelete('itorero-bands/'+btn.getAttribute('data-del')).then(loadTab); };
        });
        main.querySelectorAll('[data-edit]').forEach(function(btn){
          btn.onclick = function(){
            var id = btn.getAttribute('data-edit');
            apiGet('itorero-bands/'+id).then(function(res){
              var i = res.data;
              document.getElementById('bandForm').querySelector('[name="name"]').value = i.name||'';
              document.getElementById('bandForm').querySelector('[name="group_type"]').value = i.group_type||'';
              document.getElementById('bandForm').querySelector('[name="package_name"]').value = i.package_name||'';
              document.getElementById('bandForm').querySelector('[name="phone"]').value = i.phone||'';
              document.getElementById('bandForm').querySelector('[name="price"]').value = i.price||'';
              document.getElementById('bandForm').querySelector('[name="bio"]').value = i.bio||'';
              if(i.photo){
                var photoHtml = '<div class="form-group full"><label>Current Photo</label><img src="'+(MK_BASE||'')+'/uploads/itorero/'+i.photo+'" style="max-width:200px;height:auto;margin-bottom:0.5rem" /><br><small>Leave blank to keep current photo</small></div>';
                document.getElementById('bandForm').querySelector('.form-grid').insertAdjacentHTML('beforeend', photoHtml);
              }
              document.getElementById('bandForm').querySelector('button').textContent = 'Update Group';
              document.getElementById('bandForm').onsubmit = function(e){
                e.preventDefault();
                var fd=new FormData(e.target);
                fd.append('status', 'active');
                fd.append('price', Number(fd.get('price')||0));
                apiPut('itorero-bands/'+id, fd).then(function(){
                  document.getElementById('bandForm').reset();
                  document.getElementById('bandForm').querySelector('button').textContent = 'Add Group';
                  loadTab();
                });
              };
            });
          };
        });
      });
    }
    if(tab==='extra-services'){
      apiGet('extra-services').then(function(res){
        var rows = (res.data||[]).map(function(e){
          return '<tr><td>'+e.name+'</td><td>'+e.category+'</td><td>'+(e.package_name||'')+'</td><td>'+e.price+'</td><td><button class="btn btn-gold" data-edit="'+e.id+'">Edit</button> <button class="btn btn-dark" data-del="'+e.id+'">Delete</button></td></tr>';
        }).join('');
        main.innerHTML = '<h2 style="font-family:var(--font-display);margin-bottom:1rem">Manage Extra Services</h2><form class="form-card" id="extraForm" style="margin-bottom:1.5rem" enctype="multipart/form-data"><div class="form-grid"><div class="form-group"><label>Name</label><input name="name" required /></div><div class="form-group"><label>Category</label><select name="category"><option value="smoke_machine">Smoke Machine</option><option value="fireworks">Fireworks</option><option value="photobooth">Photobooth</option><option value="saxophone">Saxophone</option></select></div><div class="form-group"><label>Package Name</label><input name="package_name" /></div><div class="form-group"><label>Duration</label><input name="duration" /></div><div class="form-group"><label>Price</label><input type="number" name="price" /></div><div class="form-group full"><label>Photo</label><input type="file" name="photo" accept="image/*" /></div><div class="form-group full"><label>Description</label><textarea name="description"></textarea></div></div><button class="btn btn-dark" style="margin-top:1rem">Add Service</button></form><table class="admin-table"><thead><tr><th>Name</th><th>Category</th><th>Package</th><th>Price</th><th>Action</th></tr></thead><tbody>'+rows+'</tbody></table>';
        document.getElementById('extraForm').onsubmit = function(e){
          e.preventDefault();
          var fd=new FormData(e.target);
          fd.append('status', 'available');
          fd.append('price', Number(fd.get('price')||0));
          apiPost('extra-services', fd).then(function(res){
            if(res.success){
              e.target.reset();
              loadTab();
            } else {
              alert('Error: ' + (res.message || 'Failed to add'));
            }
          }).catch(function(err){
            alert('Error: ' + err);
          });
        };
        main.querySelectorAll('[data-del]').forEach(function(btn){
          btn.onclick = function(){ apiDelete('extra-services/'+btn.getAttribute('data-del')).then(loadTab); };
        });
        main.querySelectorAll('[data-edit]').forEach(function(btn){
          btn.onclick = function(){
            var id = btn.getAttribute('data-edit');
            apiGet('extra-services/'+id).then(function(res){
              var e = res.data;
              document.getElementById('extraForm').querySelector('[name="name"]').value = e.name||'';
              document.getElementById('extraForm').querySelector('[name="category"]').value = e.category||'';
              document.getElementById('extraForm').querySelector('[name="package_name"]').value = e.package_name||'';
              document.getElementById('extraForm').querySelector('[name="duration"]').value = e.duration||'';
              document.getElementById('extraForm').querySelector('[name="price"]').value = e.price||'';
              document.getElementById('extraForm').querySelector('[name="description"]').value = e.description||'';
              if(e.photo){
                var photoHtml = '<div class="form-group full"><label>Current Photo</label><img src="'+(MK_BASE||'')+'/uploads/extras/'+e.photo+'" style="max-width:200px;height:auto;margin-bottom:0.5rem" /><br><small>Leave blank to keep current photo</small></div>';
                document.getElementById('extraForm').querySelector('.form-grid').insertAdjacentHTML('beforeend', photoHtml);
              }
              document.getElementById('extraForm').querySelector('button').textContent = 'Update Service';
              document.getElementById('extraForm').onsubmit = function(e){
                e.preventDefault();
                var fd=new FormData(e.target);
                fd.append('status', 'available');
                fd.append('price', Number(fd.get('price')||0));
                apiPut('extra-services/'+id, fd).then(function(){
                  document.getElementById('extraForm').reset();
                  document.getElementById('extraForm').querySelector('button').textContent = 'Add Service';
                  loadTab();
                });
              };
            });
          };
        });
      });
    }
    if(tab==='saxophone'){
      apiGet('extra-services').then(function(res){
        var saxData = (res.data||[]).filter(function(e){ return e.category==='saxophone'; });
        var rows = saxData.map(function(e){
          return '<tr><td>'+e.name+'</td><td>'+(e.package_name||'')+'</td><td>'+(e.duration||'')+'</td><td>'+e.price+'</td><td><button class="btn btn-gold" data-edit="'+e.id+'">Edit</button> <button class="btn btn-dark" data-del="'+e.id+'">Delete</button></td></tr>';
        }).join('');
        main.innerHTML = '<h2 style="font-family:var(--font-display);margin-bottom:1rem">Manage Saxophone</h2><form class="form-card" id="saxForm" style="margin-bottom:1.5rem" enctype="multipart/form-data"><div class="form-grid"><div class="form-group"><label>Name</label><input name="name" required /></div><div class="form-group"><label>Package Name</label><input name="package_name" /></div><div class="form-group"><label>Duration</label><input name="duration" /></div><div class="form-group"><label>Price</label><input type="number" name="price" /></div><div class="form-group full"><label>Photo</label><input type="file" name="photo" accept="image/*" /></div><div class="form-group full"><label>Description</label><textarea name="description"></textarea></div></div><button class="btn btn-dark" style="margin-top:1rem">Add Saxophone</button></form><table class="admin-table"><thead><tr><th>Name</th><th>Package</th><th>Duration</th><th>Price</th><th>Action</th></tr></thead><tbody>'+rows+'</tbody></table>';
        document.getElementById('saxForm').onsubmit = function(e){
          e.preventDefault();
          var fd=new FormData(e.target);
          fd.append('status', 'available');
          fd.append('category', 'saxophone');
          fd.append('price', Number(fd.get('price')||0));
          apiPost('extra-services', fd).then(function(res){
            if(res.success){
              e.target.reset();
              loadTab();
            } else {
              alert('Error: ' + (res.message || 'Failed to add'));
            }
          }).catch(function(err){
            alert('Error: ' + err);
          });
        };
        main.querySelectorAll('[data-del]').forEach(function(btn){
          btn.onclick = function(){ apiDelete('extra-services/'+btn.getAttribute('data-del')).then(loadTab); };
        });
        main.querySelectorAll('[data-edit]').forEach(function(btn){
          btn.onclick = function(){
            var id = btn.getAttribute('data-edit');
            apiGet('extra-services/'+id).then(function(res){
              var e = res.data;
              document.getElementById('saxForm').querySelector('[name="name"]').value = e.name||'';
              document.getElementById('saxForm').querySelector('[name="package_name"]').value = e.package_name||'';
              document.getElementById('saxForm').querySelector('[name="duration"]').value = e.duration||'';
              document.getElementById('saxForm').querySelector('[name="price"]').value = e.price||'';
              document.getElementById('saxForm').querySelector('[name="description"]').value = e.description||'';
              if(e.photo){
                var photoHtml = '<div class="form-group full"><label>Current Photo</label><img src="'+(MK_BASE||'')+'/uploads/extras/'+e.photo+'" style="max-width:200px;height:auto;margin-bottom:0.5rem" /><br><small>Leave blank to keep current photo</small></div>';
                document.getElementById('saxForm').querySelector('.form-grid').insertAdjacentHTML('beforeend', photoHtml);
              }
              document.getElementById('saxForm').querySelector('button').textContent = 'Update Saxophone';
              document.getElementById('saxForm').onsubmit = function(e){
                e.preventDefault();
                var fd=new FormData(e.target);
                fd.append('status', 'available');
                fd.append('category', 'saxophone');
                fd.append('price', Number(fd.get('price')||0));
                apiPut('extra-services/'+id, fd).then(function(){
                  document.getElementById('saxForm').reset();
                  document.getElementById('saxForm').querySelector('button').textContent = 'Add Saxophone';
                  loadTab();
                });
              };
            });
          };
        });
      });
    }
    if(tab==='sonalization'){
      apiGet('sonalization').then(function(res){
        var rows = (res.data||[]).map(function(s){
          return '<tr><td>'+s.title+'</td><td>'+(s.product_type||'')+'</td><td>'+s.price+'</td><td><button class="btn btn-gold" data-edit="'+s.id+'">Edit</button> <button class="btn btn-dark" data-del="'+s.id+'">Delete</button></td></tr>';
        }).join('');
        main.innerHTML = '<h2 style="font-family:var(--font-display);margin-bottom:1rem">Manage Sonalization</h2><form class="form-card" id="sonForm" style="margin-bottom:1.5rem" enctype="multipart/form-data"><div class="form-grid"><div class="form-group"><label>Title</label><input name="title" required /></div><div class="form-group"><label>Product Type</label><input name="product_type" /></div><div class="form-group"><label>Price</label><input type="number" name="price" /></div><div class="form-group full"><label>Photo</label><input type="file" name="photo" accept="image/*" /></div><div class="form-group full"><label>Description</label><textarea name="description"></textarea></div></div><button class="btn btn-dark" style="margin-top:1rem">Add Product</button></form><table class="admin-table"><thead><tr><th>Title</th><th>Type</th><th>Price</th><th>Action</th></tr></thead><tbody>'+rows+'</tbody></table>';
        document.getElementById('sonForm').onsubmit = function(e){
          e.preventDefault();
          var fd=new FormData(e.target);
          fd.append('status', 'available');
          fd.append('price', Number(fd.get('price')||0));
          apiPost('sonalization', fd).then(loadTab);
        };
        main.querySelectorAll('[data-del]').forEach(function(btn){
          btn.onclick = function(){ apiDelete('sonalization/'+btn.getAttribute('data-del')).then(loadTab); };
        });
        main.querySelectorAll('[data-edit]').forEach(function(btn){
          btn.onclick = function(){
            var id = btn.getAttribute('data-edit');
            apiGet('sonalization/'+id).then(function(res){
              var s = res.data;
              document.getElementById('sonForm').querySelector('[name="title"]').value = s.title||'';
              document.getElementById('sonForm').querySelector('[name="product_type"]').value = s.product_type||'';
              document.getElementById('sonForm').querySelector('[name="price"]').value = s.price||'';
              document.getElementById('sonForm').querySelector('[name="description"]').value = s.description||'';
              if(s.photo){
                var photoHtml = '<div class="form-group full"><label>Current Photo</label><img src="'+(MK_BASE||'')+'/uploads/sonalization/'+s.photo+'" style="max-width:200px;height:auto;margin-bottom:0.5rem" /><br><small>Leave blank to keep current photo</small></div>';
                document.getElementById('sonForm').querySelector('.form-grid').insertAdjacentHTML('beforeend', photoHtml);
              }
              document.getElementById('sonForm').querySelector('button').textContent = 'Update Product';
              document.getElementById('sonForm').onsubmit = function(e){
                e.preventDefault();
                var fd=new FormData(e.target);
                fd.append('status', 'available');
                fd.append('price', Number(fd.get('price')||0));
                apiPut('sonalization/'+id, fd).then(function(){
                  document.getElementById('sonForm').reset();
                  document.getElementById('sonForm').querySelector('button').textContent = 'Add Product';
                  loadTab();
                });
              };
            });
          };
        });
      });
    }
    if(tab==='service-protocols'){
      apiGet('service-protocols').then(function(res){
        var rows = (res.data||[]).map(function(s){
          return '<tr><td>'+s.title+'</td><td>'+s.price+'</td><td>'+s.status+'</td><td><button class="btn btn-gold" data-edit="'+s.id+'">Edit</button> <button class="btn btn-dark" data-del="'+s.id+'">Delete</button></td></tr>';
        }).join('');
        main.innerHTML = '<h2 style="font-family:var(--font-display);margin-bottom:1rem">Manage Service and Protocol</h2><form class="form-card" id="protoForm" style="margin-bottom:1.5rem" enctype="multipart/form-data"><div class="form-grid"><div class="form-group"><label>Title</label><input name="title" required /></div><div class="form-group"><label>Price</label><input type="number" name="price" /></div><div class="form-group"><label>Status</label><select name="status"><option value="available">Available</option><option value="unavailable">Unavailable</option></select></div><div class="form-group full"><label>Photo</label><input type="file" name="photo" accept="image/*" /></div><div class="form-group full"><label>Description</label><textarea name="description"></textarea></div></div><button class="btn btn-dark" style="margin-top:1rem">Add Protocol</button> <button type="button" class="btn btn-gold" style="margin-top:1rem;display:none" id="cancelEdit">Cancel</button></form><table class="admin-table"><thead><tr><th>Title</th><th>Price</th><th>Status</th><th>Action</th></tr></thead><tbody>'+rows+'</tbody></table>';
        document.getElementById('protoForm').onsubmit = function(e){
          e.preventDefault();
          var fd=new FormData(e.target);
          fd.append('status', fd.get('status')||'available');
          fd.append('price', Number(fd.get('price')||0));
          apiPost('service-protocols', fd).then(function(res){
            if(res.success){
              document.getElementById('protoForm').reset();
              document.getElementById('cancelEdit').style.display = 'none';
              loadTab();
            } else {
              alert('Error: ' + (res.message || 'Failed to save'));
            }
          });
        };
        document.getElementById('cancelEdit').onclick = function(){
          document.getElementById('protoForm').reset();
          document.getElementById('protoForm').querySelector('button[type="submit"]').textContent = 'Add Protocol';
          document.getElementById('cancelEdit').style.display = 'none';
        };
        main.querySelectorAll('[data-del]').forEach(function(btn){
          btn.onclick = function(){ 
            if(confirm('Are you sure you want to delete this protocol?')){
              apiDelete('service-protocols/'+btn.getAttribute('data-del')).then(function(res){
                if(res.success){
                  loadTab();
                } else {
                  alert('Error: ' + (res.message || 'Failed to delete'));
                }
              });
            }
          };
        });
        main.querySelectorAll('[data-edit]').forEach(function(btn){
          btn.onclick = function(){
            var id = btn.getAttribute('data-edit');
            apiGet('service-protocols/'+id).then(function(res){
              var s = res.data;
              document.getElementById('protoForm').querySelector('[name="title"]').value = s.title||'';
              document.getElementById('protoForm').querySelector('[name="price"]').value = s.price||'';
              document.getElementById('protoForm').querySelector('[name="status"]').value = s.status||'available';
              document.getElementById('protoForm').querySelector('[name="description"]').value = s.description||'';
              if(s.photo){
                var photoHtml = '<div class="form-group full"><label>Current Photo</label><img src="'+(MK_BASE||'')+'/uploads/service-protocols/'+s.photo+'" style="max-width:200px;height:auto;margin-bottom:0.5rem" /><br><small>Leave blank to keep current photo</small></div>';
                document.getElementById('protoForm').querySelector('.form-grid').insertAdjacentHTML('beforeend', photoHtml);
              }
              document.getElementById('protoForm').querySelector('button[type="submit"]').textContent = 'Update Protocol';
              document.getElementById('cancelEdit').style.display = 'inline-block';
              document.getElementById('protoForm').onsubmit = function(e){
                e.preventDefault();
                var fd=new FormData(e.target);
                fd.append('status', fd.get('status')||'available');
                fd.append('price', Number(fd.get('price')||0));
                apiPut('service-protocols/'+id, fd).then(function(res){
                  if(res.success){
                    document.getElementById('protoForm').reset();
                    document.getElementById('protoForm').querySelector('button[type="submit"]').textContent = 'Add Protocol';
                    document.getElementById('cancelEdit').style.display = 'none';
                    loadTab();
                  } else {
                    alert('Error: ' + (res.message || 'Failed to update'));
                  }
                });
              };
            });
          };
        });
      });
    }
    if(tab==='gallery'){
      apiGet('gallery').then(function(res){
        var rows = (res.data||[]).map(function(g){
          return '<tr><td>'+(g.title||'')+'</td><td>'+g.category+'</td><td>'+g.image+'</td><td><button class="btn btn-gold" data-edit="'+g.id+'">Edit</button> <button class="btn btn-dark" data-del="'+g.id+'">Delete</button></td></tr>';
        }).join('');
        main.innerHTML = '<h2 style="font-family:var(--font-display);margin-bottom:1rem">Manage Gallery</h2><form class="form-card" id="galForm" style="margin-bottom:1.5rem" enctype="multipart/form-data"><div class="form-grid"><div class="form-group"><label>Title</label><input name="title" /></div><div class="form-group"><label>Category</label><input name="category" value="events" /></div><div class="form-group full"><label>Photo</label><input type="file" name="photo" accept="image/*" /></div></div><button class="btn btn-dark" style="margin-top:1rem">Add Photo</button></form><table class="admin-table"><thead><tr><th>Title</th><th>Category</th><th>Image</th><th>Action</th></tr></thead><tbody>'+rows+'</tbody></table>';
        document.getElementById('galForm').onsubmit = function(e){
          e.preventDefault();
          var fd=new FormData(e.target);
          apiPost('gallery', fd).then(loadTab);
        };
        main.querySelectorAll('[data-del]').forEach(function(btn){
          btn.onclick = function(){ apiDelete('gallery/'+btn.getAttribute('data-del')).then(loadTab); };
        });
        main.querySelectorAll('[data-edit]').forEach(function(btn){
          btn.onclick = function(){
            var id = btn.getAttribute('data-edit');
            apiGet('gallery/'+id).then(function(res){
              var g = res.data;
              document.getElementById('galForm').querySelector('[name="title"]').value = g.title||'';
              document.getElementById('galForm').querySelector('[name="category"]').value = g.category||'';
              if(g.image){
                var photoHtml = '<div class="form-group full"><label>Current Photo</label><img src="'+(MK_BASE||'')+'/uploads/gallery/'+g.image+'" style="max-width:200px;height:auto;margin-bottom:0.5rem" /><br><small>Leave blank to keep current photo</small></div>';
                document.getElementById('galForm').querySelector('.form-grid').insertAdjacentHTML('beforeend', photoHtml);
              }
              document.getElementById('galForm').querySelector('button').textContent = 'Update Photo';
              document.getElementById('galForm').onsubmit = function(e){
                e.preventDefault();
                var fd=new FormData(e.target);
                apiPut('gallery/'+id, fd).then(function(){
                  document.getElementById('galForm').reset();
                  document.getElementById('galForm').querySelector('button').textContent = 'Add Photo';
                  loadTab();
                });
              };
            });
          };
        });
      });
    }
    if(tab==='testimonials'){
      apiGet('testimonials').then(function(res){
        var rows = (res.data||[]).map(function(t){
          return '<tr><td>'+t.client_name+'</td><td>'+(t.event_type||'')+'</td><td>'+t.rating+'</td><td>'+t.status+'</td><td><button class="btn btn-gold" data-edit="'+t.id+'">Edit</button> <button class="btn btn-dark" data-del="'+t.id+'">Delete</button></td></tr>';
        }).join('');
        main.innerHTML = '<h2 style="font-family:var(--font-display);margin-bottom:1rem">Manage Testimonials</h2><form class="form-card" id="testForm" style="margin-bottom:1.5rem"><div class="form-grid"><div class="form-group"><label>Client Name</label><input name="client_name" required /></div><div class="form-group"><label>Event Type</label><input name="event_type" /></div><div class="form-group"><label>Rating</label><select name="rating"><option value="5">5 Stars</option><option value="4">4 Stars</option><option value="3">3 Stars</option><option value="2">2 Stars</option><option value="1">1 Star</option></select></div><div class="form-group"><label>Status</label><select name="status"><option value="approved">Approved</option><option value="pending">Pending</option></select></div><div class="form-group full"><label>Message</label><textarea name="message" required></textarea></div></div><button class="btn btn-dark" style="margin-top:1rem">Add Testimonial</button></form><table class="admin-table"><thead><tr><th>Client</th><th>Event</th><th>Rating</th><th>Status</th><th>Action</th></tr></thead><tbody>'+rows+'</tbody></table>';
        document.getElementById('testForm').onsubmit = function(e){
          e.preventDefault();
          var fd=new FormData(e.target), body={};
          fd.forEach(function(v,k){ body[k]=v; });
          body.rating = Number(body.rating||5);
          apiPost('testimonials', body).then(loadTab);
        };
        main.querySelectorAll('[data-del]').forEach(function(btn){
          btn.onclick = function(){ apiDelete('testimonials/'+btn.getAttribute('data-del')).then(loadTab); };
        });
        main.querySelectorAll('[data-edit]').forEach(function(btn){
          btn.onclick = function(){
            var id = btn.getAttribute('data-edit');
            apiGet('testimonials/'+id).then(function(res){
              var t = res.data;
              document.getElementById('testForm').querySelector('[name="client_name"]').value = t.client_name||'';
              document.getElementById('testForm').querySelector('[name="event_type"]').value = t.event_type||'';
              document.getElementById('testForm').querySelector('[name="rating"]').value = t.rating||'';
              document.getElementById('testForm').querySelector('[name="status"]').value = t.status||'';
              document.getElementById('testForm').querySelector('[name="message"]').value = t.message||'';
              document.getElementById('testForm').querySelector('button').textContent = 'Update Testimonial';
              document.getElementById('testForm').onsubmit = function(e){
                e.preventDefault();
                var fd=new FormData(e.target), body={};
                fd.forEach(function(v,k){ body[k]=v; });
                body.rating = Number(body.rating||5);
                apiPut('testimonials/'+id, body).then(function(){
                  document.getElementById('testForm').reset();
                  document.getElementById('testForm').querySelector('button').textContent = 'Add Testimonial';
                  loadTab();
                });
              };
            });
          };
        });
      });
    }
    if(tab==='settings'){
      apiGet('settings').then(function(res){
        var s = res.data || {};
        main.innerHTML = '<h2 style="font-family:var(--font-display);margin-bottom:1rem">Site Settings</h2><form class="form-card" id="setForm"><div class="form-grid"><div class="form-group"><label>Site Name</label><input name="site_name" value="'+(s.site_name||'').replace(/"/g, '&quot;')+'" /></div><div class="form-group"><label>Tagline</label><input name="tagline" value="'+(s.tagline||'').replace(/"/g, '&quot;')+'" /></div><div class="form-group"><label>Phone</label><input name="phone" value="'+(s.phone||'').replace(/"/g, '&quot;')+'" /></div><div class="form-group"><label>Email</label><input name="email" value="'+(s.email||'').replace(/"/g, '&quot;')+'" /></div><div class="form-group"><label>Facebook</label><input name="facebook" value="'+(s.facebook||'').replace(/"/g, '&quot;')+'" /></div><div class="form-group"><label>Instagram</label><input name="instagram" value="'+(s.instagram||'').replace(/"/g, '&quot;')+'" /></div><div class="form-group"><label>Twitter</label><input name="twitter" value="'+(s.twitter||'').replace(/"/g, '&quot;')+'" /></div><div class="form-group"><label>TikTok</label><input name="tiktok" value="'+(s.tiktok||'').replace(/"/g, '&quot;')+'" /></div><div class="form-group"><label>WhatsApp</label><input name="whatsapp" value="'+(s.whatsapp||'').replace(/"/g, '&quot;')+'" /></div><div class="form-group full"><label>Address</label><textarea name="address">'+(s.address||'').replace(/</g, '&lt;').replace(/>/g, '&gt;')+'</textarea></div><div class="form-group full"><label>About Text</label><textarea name="about_text">'+(s.about_text||'').replace(/</g, '&lt;').replace(/>/g, '&gt;')+'</textarea></div></div><button class="btn btn-dark" style="margin-top:1rem">Save Settings</button></form>';
        document.getElementById('setForm').onsubmit = function(e){
          e.preventDefault();
          var fd=new FormData(e.target), body={};
          fd.forEach(function(v,k){ body[k]=v; });
          apiPut('settings', body).then(function(){ alert('Settings saved!'); });
        };
      });
    }
    if(tab==='account'){
      main.innerHTML = '<h2 style="font-family:var(--font-display);margin-bottom:1rem">Account Settings</h2><form class="form-card" id="accountForm"><div class="form-grid"><div class="form-group"><label>Full Name</label><input type="text" name="full_name" value="'+(admin.full_name||'')+'" /></div><div class="form-group"><label>Email</label><input type="email" name="email" value="'+(admin.email||'')+'" /></div><div class="form-group"><label>Username</label><input type="text" name="username" value="'+(admin.username||'')+'" /></div></div><div style="margin-top:2rem;padding-top:2rem;border-top:1px solid rgba(10,22,40,.1)"><h3 style="margin-bottom:1rem">Change Password</h3><div class="form-grid"><div class="form-group"><label>Current Password</label><input type="password" name="current_password" /></div><div class="form-group"><label>New Password</label><input type="password" name="new_password" /></div></div></div><button class="btn btn-dark" style="margin-top:1.5rem">Save Account Settings</button></form>';
      document.getElementById('accountForm').onsubmit = function(e){
        e.preventDefault();
        var fd=new FormData(e.target), body={};
        fd.forEach(function(v,k){ body[k]=v; });
        body.admin_id = admin.id;
        apiPost('admin/update-account', body).then(function(res){
          if(res.success){
            alert('Account updated successfully!');
            localStorage.removeItem('mk_admin_token'); localStorage.removeItem('mk_admin'); token=''; renderLogin();
          } else {
            alert('Error: ' + (res.message || 'Failed'));
          }
        });
      };
    }
  }

  if(token) renderApp(); else renderLogin();
})();
</script>
</body>
</html>


