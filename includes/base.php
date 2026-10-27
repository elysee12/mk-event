<?php
/**
 * MK Event Company - Base URL override for deployment
 *
 * This file is auto-detected by includes/layout.php.
 *
 * LOCAL (XAMPP):
 *   The site lives at localhost/MK/ — MK_BASE should be '/MK'
 *   This is the default when the document root does NOT equal the project root.
 *
 * LIVE (mkevent.co.rw):
 *   The site lives at the domain root — change '/MK' to '' (empty string).
 *   Upload all files to the public_html (or www) folder of your hosting account.
 *
 * EXAMPLE for live server:
 *   define('MK_BASE', '');
 */
define('MK_BASE', '/MK');
