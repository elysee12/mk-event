<?php
/**
 * MK Event Company - Database Configuration (PHP 5.4+)
 */
error_reporting(0);
ini_set('display_errors', 0);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

define('DB_HOST', 'localhost');
define('DB_USER', 'root');   // Live VPS: change to your cPanel/MySQL username, e.g. 'mkevent_db'
define('DB_PASS', '');       // Live VPS: set your database password here
define('DB_NAME', 'MK');     // Live VPS: your cPanel database name, e.g. 'mkevent_mk'

function getDB() {
    static $conn = null;
    if ($conn === null) {
        try {
            $conn = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
                DB_USER,
                DB_PASS,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                )
            );
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(array('success' => false, 'message' => 'Database connection failed'));
            exit;
        }
    }
    return $conn;
}

function jsonResponse($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function getInput() {
    $data = null;

    // PHP fills $_POST for urlencoded and multipart bodies (POST only), so prefer it.
    if (!empty($_POST) && is_array($_POST)) {
        $data = $_POST;
    } else {
        $raw = file_get_contents('php://input');
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }
    }

    if (!is_array($data)) {
        $data = array();
    }

    // The method override is routing information, never a column.
    unset($data['_method']);

    return $data;
}

function arr_get($arr, $key, $default = null) {
    return isset($arr[$key]) ? $arr[$key] : $default;
}
