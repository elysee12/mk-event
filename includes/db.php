<?php
/**
 * Direct DB access for PHP pages (PHP 5.4 compatible)
 */
function mk_db() {
    static $conn = null;
    if ($conn === null) {
        try {
            $conn = new PDO('mysql:host=localhost;dbname=MK;charset=utf8', 'root', '', array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ));
        } catch (PDOException $e) {
            $conn = false;
        }
    }
    return $conn;
}

function mk_api($path) {
    $db = mk_db();
    if (!$db) return array('success' => false, 'data' => array());

    $path = trim($path, '/');
    $map = array(
        'settings' => 'settings',
        'mcs' => 'mcs',
        'abatahira' => 'abatahira',
        'cakes' => 'cakes',
        'photographers' => 'photographers',
        'catering' => 'catering',
        'decorations' => 'decorations',
        'sonalization' => 'sonalization',
        'itorero-bands' => 'itorero_bands',
        'extra-services' => 'extra_services',
        'service-protocols' => 'service_protocols',
        'gallery' => 'gallery',
        'testimonials' => 'testimonials',
        'bookings' => 'bookings',
        'contact' => 'contact_messages',
        'stats' => null,
    );

    $extras = array(
        'smoke-machines' => 'smoke_machine',
        'fireworks' => 'fireworks',
        'photobooths' => 'photobooth',
    );
    if (isset($extras[$path])) {
        try {
            $stmt = $db->prepare("SELECT * FROM extra_services WHERE category = ? ORDER BY id DESC");
            $stmt->execute(array($extras[$path]));
            return array('success' => true, 'data' => $stmt->fetchAll());
        } catch (PDOException $e) {
            return array('success' => false, 'data' => array());
        }
    }

    if ($path === 'settings') {
        return mk_api_rows($db, 'SELECT * FROM settings LIMIT 1', true);
    }

    if ($path === 'testimonials') {
        return mk_api_rows($db, "SELECT * FROM testimonials WHERE status='approved' ORDER BY id DESC");
    }

    if (isset($map[$path]) && $map[$path]) {
        $table = $map[$path];
        return mk_api_rows($db, "SELECT * FROM `$table` ORDER BY id DESC");
    }

    return array('success' => false, 'data' => array());
}

/**
 * These pages render before any API exists, so a missing or broken table must
 * show the empty state instead of a fatal error with a stack trace.
 *
 * @param bool $single return the first row itself rather than a list
 */
function mk_api_rows($db, $sql, $single = false) {
    try {
        $rows = $db->query($sql)->fetchAll();
        return array('success' => true, 'data' => $single ? (isset($rows[0]) ? $rows[0] : null) : $rows);
    } catch (PDOException $e) {
        error_log('mk_api query failed: ' . $e->getMessage());
        return array('success' => false, 'data' => array());
    }
}
