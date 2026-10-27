<?php
/**
 * MK Event Company - Root entry
 * Shows the PHP website. If a React production build exists, that is used instead.
 */
$reactIndex = __DIR__ . '/frontend/dist/index.html';
if (file_exists($reactIndex)) {
    readfile($reactIndex);
    exit;
}
require __DIR__ . '/home.php';
