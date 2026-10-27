# MK Event Company

Full-service event company website — MCs, Abatahira, Cakes, Photographers, Catering, Decoration, Sonalization, Itorero & Band, Smoke Machine, Fireworks, 360 Photobooth, Saxophone.

---

## Run locally (XAMPP)

1. Start **Apache** and **MySQL** in XAMPP Control Panel
2. Open **phpMyAdmin** → create a database named `MK`
3. Import `database/mk.sql` into that database
4. Open: **http://localhost/MK/**
5. Admin panel: **http://localhost/MK/pages/admin.php** — login `admin` / `password`

---

## Deploy to live server (mkevent.co.rw)

### The ONE file you change when moving to a VPS

**`includes/base.php`** — change one line:

```php
// BEFORE (localhost/XAMPP):
define('MK_BASE', '/MK');

// AFTER (live VPS at domain root):
define('MK_BASE', '');
```

That's it. Every URL in the site (navigation, images, API calls, favicons) reads from `MK_BASE`.

---

### Step-by-step VPS deployment

#### 1. Upload files

Upload the entire `MK/` folder contents to your server's web root (e.g. `/var/www/html/` or `/home/mkevent/public_html/`). The `index.php` should be directly inside that folder.

If using **cPanel File Manager** or **FTP (FileZilla)**:
- Connect to your host
- Navigate to `public_html/`
- Upload all files from `c:\xampp\htdocs\MK\` into `public_html/`

#### 2. Set MK_BASE to empty

Edit `includes/base.php` on the server:

```php
define('MK_BASE', '');
```

#### 3. Create the database

In **cPanel → MySQL Databases**:
1. Create a new database, e.g. `mkevent_mk`
2. Create a MySQL user, e.g. `mkevent_user` with a strong password
3. Add the user to the database with **All Privileges**

Import the schema: **phpMyAdmin → mkevent_mk → Import → select `database/mk.sql`**

#### 4. Update database credentials

Edit **`api/config/db.php`**:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'mkevent_user');   // your cPanel MySQL username
define('DB_PASS', 'YourStrongPass'); // your MySQL password
define('DB_NAME', 'mkevent_mk');     // your database name
```

Edit **`includes/db.php`** (same credentials — used by PHP pages directly):

```php
$conn = new PDO('mysql:host=localhost;dbname=mkevent_mk;charset=utf8', 'mkevent_user', 'YourStrongPass', ...
```

#### 5. Set folder permissions

The `uploads/` folder and all its subfolders need write permission:

```bash
chmod -R 755 uploads/
```

Or in cPanel File Manager: right-click `uploads/` → Permissions → set to `755`.

#### 6. Set up the .htaccess (Apache)

The `api/.htaccess` already exists. Make sure **mod_rewrite** is enabled on your server (it is on most shared hosts).

If the site root needs clean URLs, add an `.htaccess` to the web root:

```apache
Options -Indexes
DirectoryIndex index.php
```

#### 7. Change the admin password

Log in to the admin panel at `https://mkevent.co.rw/pages/admin.php`, go to **Account** tab, and change the default password immediately.

#### 8. First-time setup helper

Visit `https://mkevent.co.rw/create-service-protocols-table.php` once to create the `service_protocols` table if it doesn't exist, then delete that file from the server.

---

### Two-file checklist for VPS

| File | What to change |
|------|---------------|
| `includes/base.php` | `define('MK_BASE', '');` |
| `api/config/db.php` | DB_USER, DB_PASS, DB_NAME |
| `includes/db.php` | Same DB credentials (host, name, user, pass in PDO string) |

> **Note:** `includes/db.php` and `api/config/db.php` both connect to the database. They need matching credentials.

---

## Pages

| Page | Local URL | Live URL |
|------|-----------|----------|
| Home | http://localhost/MK/ | https://mkevent.co.rw/ |
| MC | http://localhost/MK/pages/mc.php | https://mkevent.co.rw/pages/mc.php |
| Abatahira | http://localhost/MK/pages/abatahira.php | https://mkevent.co.rw/pages/abatahira.php |
| Cakes | http://localhost/MK/pages/cakes.php | https://mkevent.co.rw/pages/cakes.php |
| Photographers | http://localhost/MK/pages/photographers.php | https://mkevent.co.rw/pages/photographers.php |
| Catering | http://localhost/MK/pages/catering.php | https://mkevent.co.rw/pages/catering.php |
| Decoration | http://localhost/MK/pages/decoration.php | https://mkevent.co.rw/pages/decoration.php |
| Sonalization | http://localhost/MK/pages/sonalization.php | https://mkevent.co.rw/pages/sonalization.php |
| Itorero & Band | http://localhost/MK/pages/itorero-band.php | https://mkevent.co.rw/pages/itorero-band.php |
| Service & Protocol | http://localhost/MK/pages/service-protocol.php | https://mkevent.co.rw/pages/service-protocol.php |
| Smoke Machine | http://localhost/MK/pages/smoke-machine.php | https://mkevent.co.rw/pages/smoke-machine.php |
| Fireworks | http://localhost/MK/pages/fireworks.php | https://mkevent.co.rw/pages/fireworks.php |
| 360 Photobooth | http://localhost/MK/pages/photobooth.php | https://mkevent.co.rw/pages/photobooth.php |
| Saxophone | http://localhost/MK/pages/saxophone.php | https://mkevent.co.rw/pages/saxophone.php |
| Booking | http://localhost/MK/pages/booking.php | https://mkevent.co.rw/pages/booking.php |
| Gallery | http://localhost/MK/pages/gallery.php | https://mkevent.co.rw/pages/gallery.php |
| Contact | http://localhost/MK/pages/contact.php | https://mkevent.co.rw/pages/contact.php |
| Admin | http://localhost/MK/pages/admin.php | https://mkevent.co.rw/pages/admin.php |

---

## Project structure

```
MK/
├── index.php               Entry point (loads home.php)
├── home.php                Home page content
├── includes/
│   ├── layout.php          Shared HTML layout, header, footer, nav
│   ├── db.php              PHP database helper (mk_api, mk_db)
│   └── base.php            ← CHANGE THIS for live deployment (MK_BASE)
├── api/
│   ├── index.php           REST API (all CRUD for admin)
│   ├── .htaccess           URL routing for API
│   └── config/
│       └── db.php          ← CHANGE THIS for live DB credentials
├── pages/                  All front-end pages
├── assets/
│   ├── css/style.css       All styles
│   ├── js/main.js          Navigation + API helpers
│   └── logo.png            Site logo & browser tab favicon
├── uploads/                All media uploads (photos, slider, gallery)
│   ├── slider/             Hero slider images (slide1.jpg, slide2.jpg, slide3.jpg)
│   ├── gallery/
│   ├── mcs/
│   └── ...
└── database/
    ├── mk.sql              Full schema + seed data
    └── mk_extras.sql       Extra tables
```

---

## Stack

- **PHP** (compatible with PHP 5.4+)
- **MySQL** database: `MK`
- **Vanilla JS** (no framework dependencies)
- **CSS** custom design (navy + gold theme)
- No build step required — works directly on any Apache + PHP + MySQL server

---

## Default admin credentials

```
Username: admin
Password: password
```

**Change the password immediately after first login on the live server.**
