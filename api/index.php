<?php
// This XAMPP build runs PHP 5.4.7, so keep everything 5.4-compatible
// (no random_bytes, password_hash, hash_equals or array_column).
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Logs must live outside the webroot on Windows (XAMPP).
// On Linux VPS they go to the system temp directory or /var/log if writable.
if (PHP_OS_FAMILY === 'Windows' || strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $mkLogDir = 'C:\\xampp\\mk-logs';
    if (!is_dir($mkLogDir)) {
        @mkdir($mkLogDir, 0777, true);
    }
    if (!is_dir($mkLogDir) || !is_writable($mkLogDir)) {
        $mkLogDir = sys_get_temp_dir();
    }
} else {
    // Linux VPS: write logs next to the project, outside webroot if possible
    $mkLogDir = dirname(__DIR__) . '/mk-logs';
    if (!is_dir($mkLogDir)) {
        @mkdir($mkLogDir, 0755, true);
    }
    if (!is_dir($mkLogDir) || !is_writable($mkLogDir)) {
        $mkLogDir = sys_get_temp_dir();
    }
}
define('MK_LOG_DIR', $mkLogDir);
ini_set('error_log', MK_LOG_DIR . DIRECTORY_SEPARATOR . 'MK-api-error.log');

// Without this, a fatal error returns an empty HTTP 500 and leaves no trace.
register_shutdown_function(function () {
    $e = error_get_last();
    if ($e && in_array($e['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR), true)) {
        error_log('FATAL: ' . $e['message'] . ' in ' . $e['file'] . ':' . $e['line']);
    }
});

// Every stored photo is center-cropped to this exact size (2:3 portrait).
define('MK_PHOTO_WIDTH', 1024);
define('MK_PHOTO_HEIGHT', 1536);

// Build the base URL from the actual request so it works on both localhost and mkevent.co.rw
// without any manual change needed.
$_mkScheme = (
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
) ? 'https' : 'http';
$_mkHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
define('MK_RESET_BASE_URL', $_mkScheme . '://' . $_mkHost);
define('MK_RESET_TOKEN_TTL', 3600);
define('MK_MAIL_LOG', MK_LOG_DIR . DIRECTORY_SEPARATOR . 'MK-mail.log');

try {
    require_once __DIR__ . '/config/db.php';
} catch (Exception $e) {
    error_log('Config load failed: ' . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(array('success' => false, 'message' => 'The API configuration could not be loaded.'));
    exit;
}

$path = isset($_GET['path']) ? $_GET['path'] : (isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/'));
$path = preg_replace('#^(/MK)?/api/?#', '', $path);
$path = trim($path, '/');

$method = $_SERVER['REQUEST_METHOD'];
$input = getInput();

// PHP cannot parse a multipart body on PUT, so file updates arrive as POST + _method.
$override = isset($_POST['_method']) ? $_POST['_method'] : (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']) ? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : null);
if ($override !== null) {
    $override = strtoupper(trim($override));
    if (in_array($override, array('PUT', 'PATCH', 'DELETE'), true)) {
        $method = $override;
    }
}

try {
    $db = getDB();
} catch (Exception $e) {
    error_log('DB Connection Error: ' . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(array('success' => false, 'message' => 'Database connection failed'));
    exit;
}

// Wrap entire API logic in try-catch
try {
    if ($path === 'admin/login' && $method === 'POST') {
        $username = arr_get($input, 'username');
        $password = arr_get($input, 'password');
        jsonResponse(authAdmin($db, $username, $password));
    }

    if ($path === 'admin/change-password' && $method === 'POST') {
        $currentPassword = arr_get($input, 'current_password');
        $newPassword = arr_get($input, 'new_password');
        $adminId = arr_get($input, 'admin_id');
        
        try {
            $stmt = $db->prepare("SELECT password FROM admins WHERE id = ?");
            $stmt->execute(array($adminId));
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$admin) {
                jsonResponse(array('success' => false, 'message' => 'Admin not found'), 404);
            }
            
            if ($admin['password'] !== $currentPassword) {
                jsonResponse(array('success' => false, 'message' => 'Current password is incorrect'), 400);
            }
            
            $stmt = $db->prepare("UPDATE admins SET password = ? WHERE id = ?");
            $stmt->execute(array($newPassword, $adminId));
            
            jsonResponse(array('success' => true, 'message' => 'Password changed successfully'));
        } catch (PDOException $e) {
            serverError('admin/change-password', $e);
        }
    }

    if ($path === 'admin/change-username' && $method === 'POST') {
        $currentUsername = arr_get($input, 'current_username');
        $newUsername = arr_get($input, 'new_username');
        $adminId = arr_get($input, 'admin_id');
        
        try {
            $stmt = $db->prepare("SELECT username FROM admins WHERE id = ?");
            $stmt->execute(array($adminId));
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$admin) {
                jsonResponse(array('success' => false, 'message' => 'Admin not found'), 404);
            }
            
            if ($admin['username'] !== $currentUsername) {
                jsonResponse(array('success' => false, 'message' => 'Current username is incorrect'), 400);
            }
            
            $stmt = $db->prepare("SELECT id FROM admins WHERE username = ? AND id != ?");
            $stmt->execute(array($newUsername, $adminId));
            if ($stmt->fetch()) {
                jsonResponse(array('success' => false, 'message' => 'Username already exists'), 400);
            }
            
            $stmt = $db->prepare("UPDATE admins SET username = ? WHERE id = ?");
            $stmt->execute(array($newUsername, $adminId));
            
            jsonResponse(array('success' => true, 'message' => 'Username changed successfully'));
        } catch (PDOException $e) {
            serverError('admin/change-username', $e);
        }
    }

    if ($path === 'admin/forgot-password' && $method === 'POST') {
        $email = trim((string) arr_get($input, 'email'));

        $answer = array('success' => true, 'message' => 'If that email is registered, a password reset link has just been sent. Check your inbox and your Spam folder.');

        try {
            $stmt = $db->prepare("SELECT id, username, email, full_name FROM admins WHERE email = ? LIMIT 1");
            $stmt->execute(array($email));
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            // Same reply either way, so this page cannot be used to list admin accounts.
            if (!$admin) {
                error_log('forgot-password: request for unknown email');
                jsonResponse($answer);
            }

            $token = mkRandomToken(32);
            $expiry = date('Y-m-d H:i:s', time() + MK_RESET_TOKEN_TTL);

            $stmt = $db->prepare("UPDATE admins SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
            $stmt->execute(array($token, $expiry, $admin['id']));

            $link = MK_RESET_BASE_URL . '/pages/reset-password.php?token=' . rawurlencode($token);
            $minutes = (int) (MK_RESET_TOKEN_TTL / 60);

            $name = (isset($admin['full_name']) && $admin['full_name'] !== '') ? $admin['full_name'] : $admin['username'];
            $body = "Hello " . $name . ",\n\n"
                . "A password reset was requested for the MK Event Company admin account '" . $admin['username'] . "'.\n\n"
                . "Open this link to choose a new password:\n"
                . $link . "\n\n"
                . "The link expires in " . $minutes . " minutes and can only be used once.\n"
                . "If you did not request this, ignore the email - your password stays unchanged.\n";

            $mail = mkSendMail($admin['email'], 'MK Event Company - Password reset', $body, $admin['email']);

            if (!$mail['ok']) {
                // The token is already stored, so fixing the transport and retrying works.
                jsonResponse(array(
                    'success' => false,
                    'message' => 'Reset link created, but the email was NOT sent: ' . $mail['error']
                        . ' Check your server sendmail/SMTP configuration and try again.'
                ), 502);
            }

            jsonResponse($answer);
        } catch (PDOException $e) {
            error_log('forgot-password failed: ' . $e->getMessage());
            jsonResponse(array('success' => false, 'message' => 'Could not prepare the reset link. Please try again.'), 500);
        }
    }

    if ($path === 'admin/reset-password' && $method === 'POST') {
        $token = (string) arr_get($input, 'token');
        $newPassword = (string) arr_get($input, 'new_password');

        if ($token === '' || strlen($token) !== 64 || !preg_match('/^[a-f0-9]{64}$/', $token)) {
            jsonResponse(array('success' => false, 'message' => 'This reset link is invalid or has expired. Request a new one.'), 400);
        }
        if (strlen($newPassword) < 6) {
            jsonResponse(array('success' => false, 'message' => 'The new password must be at least 6 characters.'), 400);
        }

        try {
            $stmt = $db->prepare("SELECT id, reset_token_expiry FROM admins WHERE reset_token = ? LIMIT 1");
            $stmt->execute(array($token));
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$admin) {
                jsonResponse(array('success' => false, 'message' => 'Invalid or expired token'), 400);
            }
            
            if (strtotime($admin['reset_token_expiry']) < time()) {
                jsonResponse(array('success' => false, 'message' => 'Token has expired'), 400);
            }
            
            $stmt = $db->prepare("UPDATE admins SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
            $stmt->execute(array($newPassword, $admin['id']));
            
            jsonResponse(array('success' => true, 'message' => 'Password reset successfully'));
        } catch (PDOException $e) {
            serverError('admin/reset-password', $e);
        }
    }

    if ($path === 'admin/update-account' && $method === 'POST') {
        $adminId = arr_get($input, 'admin_id');
        $fullName = arr_get($input, 'full_name');
        $email = arr_get($input, 'email');
        $username = arr_get($input, 'username');
        $currentPassword = arr_get($input, 'current_password');
        $newPassword = arr_get($input, 'new_password');
        
        try {
            $stmt = $db->prepare("SELECT * FROM admins WHERE id = ?");
            $stmt->execute(array($adminId));
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$admin) {
                jsonResponse(array('success' => false, 'message' => 'Admin not found'), 404);
            }
            
            if ($currentPassword && $newPassword) {
                if ($admin['password'] !== $currentPassword) {
                    jsonResponse(array('success' => false, 'message' => 'Current password is incorrect'), 400);
                }
            }
            
            $sets = array();
            $values = array();
            
            if ($fullName) {
                $sets[] = "full_name = ?";
                $values[] = $fullName;
            }
            if ($email) {
                $stmt = $db->prepare("SELECT id FROM admins WHERE email = ? AND id != ?");
                $stmt->execute(array($email, $adminId));
                if ($stmt->fetch()) {
                    jsonResponse(array('success' => false, 'message' => 'Email already exists'), 400);
                }
                $sets[] = "email = ?";
                $values[] = $email;
            }
            if ($username) {
                $stmt = $db->prepare("SELECT id FROM admins WHERE username = ? AND id != ?");
                $stmt->execute(array($username, $adminId));
                if ($stmt->fetch()) {
                    jsonResponse(array('success' => false, 'message' => 'Username already exists'), 400);
                }
                $sets[] = "username = ?";
                $values[] = $username;
            }
            if ($newPassword) {
                $sets[] = "password = ?";
                $values[] = $newPassword;
            }
            
            if (empty($sets)) {
                jsonResponse(array('success' => false, 'message' => 'No changes to save'), 400);
            }
            
            $values[] = $adminId;
            $sql = "UPDATE admins SET " . implode(', ', $sets) . " WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute($values);
            
            jsonResponse(array('success' => true, 'message' => 'Account updated successfully'));
        } catch (PDOException $e) {
            serverError('admin/update-account', $e);
        }
    }

    if ($path === 'stats' && $method === 'GET') {
        jsonResponse(getStats($db));
    }

    $tableRoutes = array(
        'mcs' => 'mcs',
        'cakes' => 'cakes',
        'photographers' => 'photographers',
        'catering' => 'catering',
        'decorations' => 'decorations',
        'abatahira' => 'abatahira',
        'itorero-bands' => 'itorero_bands',
        'extra-services' => 'extra_services',
        'gallery' => 'gallery',
        'testimonials' => 'testimonials',
        'sonalization' => 'sonalization',
        'service-protocols' => 'service_protocols',
        'bookings' => 'bookings',
        'settings' => 'settings',
    );

    foreach ($tableRoutes as $route => $table) {
        if (strpos($path, $route) === 0) {
            if ($table === 'settings') {
                if ($method === 'GET') {
                    $row = $db->query('SELECT * FROM settings LIMIT 1')->fetch();
                    jsonResponse(array('success' => true, 'data' => $row));
                }
                if ($method === 'PUT') {
                    unset($input['id']);
                    $input = onlyTableColumns($db, 'settings', $input);
                    if (empty($input)) {
                        jsonResponse(array('success' => false, 'message' => 'No field to update was received.'), 400);
                    }
                    $sets = array();
                    foreach (array_keys($input) as $col) {
                        $sets[] = "`$col` = ?";
                    }
                    $sql = "UPDATE settings SET " . implode(', ', $sets) . " WHERE id = 1";
                    try {
                        $stmt = $db->prepare($sql);
                        $stmt->execute(array_values($input));
                        jsonResponse(array('success' => true, 'message' => 'Settings saved successfully'));
                    } catch (PDOException $e) {
                        error_log('Settings Update Error: ' . $e->getMessage());
                        jsonResponse(array('success' => false, 'message' => 'The server could not save the settings. Check the API error log.'), 500);
                    }
                }
            } else {
                handleTable($db, $table, $method, $input, $path);
            }
            exit;
        }
    }

    if ($path === 'contact' && $method === 'POST') {
        try {
            $stmt = $db->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute(array(
                arr_get($input, 'name'),
                arr_get($input, 'email'),
                arr_get($input, 'phone'),
                arr_get($input, 'subject'),
                arr_get($input, 'message')
            ));
            jsonResponse(array('success' => true, 'message' => 'Message sent successfully'));
        } catch (PDOException $e) {
            serverError('contact POST', $e);
        }
    }

    if ($path === 'contact' && $method === 'GET') {
        $rows = $db->query("SELECT * FROM contact_messages ORDER BY id DESC")->fetchAll();
        jsonResponse(array('success' => true, 'data' => $rows));
    }

    if (preg_match('#^contact/(\d+)$#', $path, $matches) && $method === 'PUT') {
        $id = intval($matches[1]);
        try {
            $stmt = $db->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
            $stmt->execute(array($id));
            jsonResponse(array('success' => true));
        } catch (PDOException $e) {
            serverError('contact read flag', $e);
        }
    }

    jsonResponse(array('success' => false, 'message' => 'Not found'), 404);

} catch (Exception $e) {
    error_log('API Error: ' . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(array('success' => false, 'message' => 'The server could not complete this request. Check the API error log.'));
    exit;
}

/**
 * PDO messages quote the SQL they ran, so they belong in the log only -
 * exposing them hands out table and column names for free.
 */
function serverError($context, Exception $e) {
    error_log($context . ' failed: ' . $e->getMessage());
    jsonResponse(array(
        'success' => false,
        'message' => 'The server could not complete this request. Check the API error log at: ' . MK_LOG_DIR . '/MK-api-error.log'
    ), 500);
}

// Handle file uploads
function uploadErrorMessage($code) {
    $map = array(
        UPLOAD_ERR_INI_SIZE => 'The photo is larger than the PHP upload_max_filesize limit. Resize it or ask your host to raise the limit.',
        UPLOAD_ERR_FORM_SIZE => 'The photo is larger than the maximum this form allows.',
        UPLOAD_ERR_PARTIAL => 'The photo was only partially uploaded. Please try again.',
        UPLOAD_ERR_NO_TMP_DIR => 'The server has no temporary directory to receive the photo.',
        UPLOAD_ERR_CANT_WRITE => 'The server could not write the photo to disk.',
        UPLOAD_ERR_EXTENSION => 'The upload was stopped by a PHP extension.',
    );
    return isset($map[$code]) ? $map[$code] : ('Upload failed (code ' . $code . ').');
}

/**
 * @return array array('filename' => string|null, 'error' => string|null)
 *         filename null + error null means no file was submitted, which is a valid update.
 */
function handleFileUpload($uploadDir, $fileInputName = 'photo') {
    error_log('handleFileUpload called - dir: ' . $uploadDir . ', input: ' . $fileInputName);

    if (!isset($_FILES[$fileInputName])) {
        return array('filename' => null, 'error' => null);
    }

    $file = $_FILES[$fileInputName];

    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        error_log('No file chosen for: ' . $fileInputName);
        return array('filename' => null, 'error' => null);
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        error_log('File upload error: ' . $file['error']);
        return array('filename' => null, 'error' => uploadErrorMessage($file['error']));
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        error_log('is_uploaded_file failed for: ' . $fileInputName);
        return array('filename' => null, 'error' => 'The server did not accept the upload. Please try again.');
    }

    return storeNormalizedPhoto($file['tmp_name'], $uploadDir);
}

function returnBytes($val) {
    $val = trim((string)$val);
    if ($val === '' || $val === '-1') {
        return -1;
    }
    $unit = strtolower(substr($val, -1));
    $num = (int)$val;
    if ($unit === 'g') return $num * 1024 * 1024 * 1024;
    if ($unit === 'm') return $num * 1024 * 1024;
    if ($unit === 'k') return $num * 1024;
    return $num;
}

/**
 * Center-crop an uploaded image to 2:3 and resample it to exactly
 * MK_PHOTO_WIDTH x MK_PHOTO_HEIGHT. The file written to disk is freshly
 * re-encoded by GD, so no original bytes ever reach uploads/.
 *
 * @return array array('filename' => string|null, 'error' => string|null)
 */
function storeNormalizedPhoto($tmpPath, $uploadDir) {
    $info = @getimagesize($tmpPath);
    if (!$info || empty($info[0]) || empty($info[1])) {
        error_log('storeNormalizedPhoto: unreadable image at ' . $tmpPath);
        return array('filename' => null, 'error' => 'This file is not a readable image. Use a JPG, PNG or GIF photo.');
    }

    // Format comes from the decoded image, never from the client-supplied filename.
    $formats = array(
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG  => 'png',
        IMAGETYPE_GIF  => 'gif',
    );
    // IMAGETYPE_WEBP only exists from PHP 7.3; this XAMPP build is 5.4.7.
    if (defined('IMAGETYPE_WEBP')) {
        $formats[IMAGETYPE_WEBP] = 'webp';
    }
    if (!isset($formats[$info[2]])) {
        error_log('storeNormalizedPhoto: unsupported image type ' . $info[2]);
        return array('filename' => null, 'error' => 'That image type is not allowed. Use a JPG, PNG or GIF photo.');
    }
    $ext = $formats[$info[2]];

    // GD is an optional extension: without it the calls below would be an uncaught
    // fatal and the browser would only see an empty response body. Save the verified
    // original instead of failing, so uploads keep working until GD is loaded.
    if (!function_exists('imagecreatefromstring') || !function_exists('imagecopyresampled')) {
        error_log('storeNormalizedPhoto: PHP GD is not enabled, storing the original photo unscaled');
        return storeOriginalPhoto($tmpPath, $uploadDir, $ext, $info[3]);
    }

    $srcW = $info[0];
    $srcH = $info[1];
    error_log('storeNormalizedPhoto source: ' . $srcW . 'x' . $srcH . ' type=' . $ext);

    // GD keeps roughly 5 bytes per pixel in memory, so a large photo can blow the default limit.
    $needed = ($srcW * $srcH * 5) + (12 * 1024 * 1024);
    $limit = returnBytes(ini_get('memory_limit'));
    if ($limit > 0 && $needed > $limit) {
        if ($needed > 512 * 1024 * 1024) {
            return array('filename' => null, 'error' => 'The photo is too large (' . $srcW . ' x ' . $srcH . ' px). Please use a smaller photo.');
        }
        ini_set('memory_limit', (string)((int)ceil($needed / 1048576) + 32) . 'M');
        error_log('memory_limit raised to ' . ini_get('memory_limit') . ' for ' . $srcW . 'x' . $srcH);
    }

    $src = @imagecreatefromstring(file_get_contents($tmpPath));
    if (!$src) {
        return array('filename' => null, 'error' => 'PHP GD could not process this photo.');
    }

    $targetRatio = MK_PHOTO_WIDTH / MK_PHOTO_HEIGHT;
    $srcRatio = $srcW / $srcH;

    if ($srcRatio > $targetRatio) {
        // Source is wider than 2:3, so trim the left and right edges.
        $cropW = (int)round($srcH * $targetRatio);
        $cropH = $srcH;
        $cropX = (int)(($srcW - $cropW) / 2);
        $cropY = 0;
    } else {
        // Source is taller than 2:3, so trim the top and bottom.
        $cropW = $srcW;
        $cropH = (int)round($srcW / $targetRatio);
        $cropX = 0;
        $cropY = (int)(($srcH - $cropH) / 2);
    }

    $dst = imagecreatetruecolor(MK_PHOTO_WIDTH, MK_PHOTO_HEIGHT);

    if ($ext !== 'jpg') {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagefilledrectangle($dst, 0, 0, MK_PHOTO_WIDTH, MK_PHOTO_HEIGHT, imagecolorallocatealpha($dst, 0, 0, 0, 127));
        imagealphablending($dst, true);
    }

    imagecopyresampled(
        $dst, $src,
        0, 0,
        $cropX, $cropY,
        MK_PHOTO_WIDTH, MK_PHOTO_HEIGHT,
        $cropW, $cropH
    );

    $targetDir = __DIR__ . '/../uploads/' . $uploadDir;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
        error_log('Created directory: ' . $targetDir);
    }

    $filename = uniqid() . '_' . time() . '.' . $ext;
    $targetPath = $targetDir . '/' . $filename;

    if ($ext === 'png') {
        $written = imagepng($dst, $targetPath, 6);
    } elseif ($ext === 'gif') {
        $written = imagegif($dst, $targetPath);
    } elseif ($ext === 'webp') {
        $written = imagewebp($dst, $targetPath, 88);
    } else {
        $written = imagejpeg($dst, $targetPath, 88);
    }

    imagedestroy($dst);
    imagedestroy($src);

    if (!$written) {
        error_log('storeNormalizedPhoto: encode/write failed at ' . $targetPath);
        return array('filename' => null, 'error' => 'The server could not write the photo into uploads/' . $uploadDir . '/. Check that folder\'s permissions.');
    }

    error_log('Photo stored normalized: ' . $filename . ' (' . MK_PHOTO_WIDTH . 'x' . MK_PHOTO_HEIGHT . ')');
    return array('filename' => $filename, 'error' => null);
}

/**
 * GD-free fallback: keep the uploaded bytes as they are. The type still comes from
 * getimagesize(), so only a real image is written and it always gets a .jpg/.png/.gif
 * extension rather than the client-supplied one. Cards and tiles crop it to 2:3 in CSS.
 *
 * @param string $dims getimagesize() "WIDTHxHEIGHT" string, used for the size guard
 * @return array array('filename' => string|null, 'error' => string|null)
 */
function storeOriginalPhoto($tmpPath, $uploadDir, $ext, $dims) {
    $size = @filesize($tmpPath);
    if ($size === false) {
        return array('filename' => null, 'error' => 'The server could not read the uploaded photo. Please try again.');
    }
    // Nothing is re-encoded here, so the original bytes are what visitors download.
    $maxBytes = 5 * 1024 * 1024;
    if ($size > $maxBytes) {
        return array('filename' => null, 'error' => 'The photo is ' . round($size / 1048576, 1) . ' MB. Without PHP GD the site cannot shrink photos, so please use one under 5 MB.');
    }

    $targetDir = __DIR__ . '/../uploads/' . $uploadDir;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
        error_log('Created directory: ' . $targetDir);
    }

    $filename = uniqid() . '_' . time() . '.' . $ext;
    $targetPath = $targetDir . '/' . $filename;

    if (!move_uploaded_file($tmpPath, $targetPath)) {
        error_log('storeOriginalPhoto: move_uploaded_file failed to ' . $targetPath);
        return array('filename' => null, 'error' => 'The server could not write the photo into uploads/' . $uploadDir . '/. Check that folder\'s permissions.');
    }
    @chmod($targetPath, 0644);

    error_log('Photo stored unscaled (' . $dims . ', ' . round($size / 1024) . ' KB) because GD is unavailable: ' . $filename);
    return array('filename' => $filename, 'error' => null);
}

/**
 * PHP 5.4 has no random_bytes() and this build has no openssl extension,
 * so mcrypt's /dev/urandom source is the available CSPRNG.
 */
function mkRandomToken($bytes = 32) {
    if (function_exists('random_bytes')) {
        return bin2hex(random_bytes($bytes));
    }
    if (function_exists('mcrypt_create_iv')) {
        $raw = @mcrypt_create_iv($bytes, MCRYPT_DEV_URANDOM);
        if ($raw !== false && strlen($raw) === $bytes) {
            return bin2hex($raw);
        }
    }
    $seed = '';
    while (strlen($seed) < $bytes) {
        $seed .= sha1(uniqid(mt_rand(), true) . microtime(true) . mt_rand(), true);
    }
    error_log('mkRandomToken: no CSPRNG available, used weak fallback');
    return substr(bin2hex($seed), 0, $bytes * 2);
}

/**
 * Sends mail through PHP's configured transport (XAMPP: sendmail.exe or mailtodisk)
 * and records every attempt, because the local transport reports success even when
 * nothing leaves the machine.
 */
function mkSendMail($to, $subject, $bodyText, $fromEmail = null) {
    $to = preg_replace('/[\r\n]/', '', (string) $to);
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return array('ok' => false, 'error' => 'Invalid recipient address.');
    }

    // Gmail rejects a message whose From differs from the authenticated account,
    // so the admin's own address is used as the sender.
    $from = preg_replace('/[\r\n]/', '', (string) ($fromEmail !== null && $fromEmail !== '' ? $fromEmail : $to));
    if (!filter_var($from, FILTER_VALIDATE_EMAIL)) {
        $from = $to;
    }

    $headers = implode("\r\n", array(
        'From: MK Event Company <' . $from . '>',
        'Reply-To: ' . $from,
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
        'X-Mailer: MK-Event-Company',
    ));
    $subject = preg_replace('/[\r\n]/', '', (string) $subject);

    $ok = @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '=?=', (string) $bodyText, $headers);
    mkMailLog($to, $subject, (string) $bodyText, $ok);

    return array('ok' => (bool) $ok, 'error' => $ok ? null : 'PHP mail() returned false.');
}

function mkMailLog($to, $subject, $body, $ok) {
    $line = sprintf(
        "[%s] %s To: %s Subject: %s\n%s\n%s\n",
        date('Y-m-d H:i:s'),
        $ok ? 'SENT' : 'FAILED',
        $to,
        $subject,
        $body,
        str_repeat('-', 70)
    );

    if (is_dir(dirname(MK_MAIL_LOG)) && is_writable(dirname(MK_MAIL_LOG))) {
        @file_put_contents(MK_MAIL_LOG, $line, FILE_APPEND | LOCK_EX);
    }
    error_log('MK mail (' . ($ok ? 'sent' : 'failed') . ') to ' . $to . ': ' . $subject);
}

function authAdmin($db, $username, $password) {
    $stmt = $db->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
    $stmt->execute(array($username));
    $admin = $stmt->fetch();
    if ($admin && $password === $admin['password']) {
        unset($admin['password'], $admin['reset_token'], $admin['reset_token_expiry']);
        return array('success' => true, 'admin' => $admin, 'token' => base64_encode($admin['id'] . ':' . time()));
    }
    return array('success' => false, 'message' => 'Invalid credentials');
}

function getStats($db) {
    $stats = array();
    $stats['bookings'] = $db->query('SELECT COUNT(*) FROM bookings')->fetchColumn();
    $stats['pending_bookings'] = $db->query('SELECT COUNT(*) FROM bookings WHERE status = "pending"')->fetchColumn();
    $stats['mcs'] = $db->query('SELECT COUNT(*) FROM mcs')->fetchColumn();
    $stats['cakes'] = $db->query('SELECT COUNT(*) FROM cakes')->fetchColumn();
    $stats['photographers'] = $db->query('SELECT COUNT(*) FROM photographers')->fetchColumn();
    $stats['catering'] = $db->query('SELECT COUNT(*) FROM catering')->fetchColumn();
    $stats['decorations'] = $db->query('SELECT COUNT(*) FROM decorations')->fetchColumn();
    $stats['messages'] = $db->query('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0')->fetchColumn();
    $stats['abatahira'] = $db->query('SELECT COUNT(*) FROM abatahira')->fetchColumn();
    $stats['itorero_bands'] = $db->query('SELECT COUNT(*) FROM itorero_bands')->fetchColumn();
    $stats['extra_services'] = $db->query('SELECT COUNT(*) FROM extra_services')->fetchColumn();
    $stats['gallery'] = $db->query('SELECT COUNT(*) FROM gallery')->fetchColumn();
    $stats['testimonials'] = $db->query('SELECT COUNT(*) FROM testimonials')->fetchColumn();
    $stats['sonalization'] = $db->query('SELECT COUNT(*) FROM sonalization')->fetchColumn();
    return array('success' => true, 'data' => $stats);
}

function tableColumns($db, $table) {
    static $cache = array();
    if (!isset($cache[$table])) {
        $cache[$table] = array();
        try {
            foreach ($db->query("SHOW COLUMNS FROM `$table`") as $col) {
                $cache[$table][] = $col['Field'];
            }
        } catch (PDOException $e) {
            error_log('tableColumns failed for ' . $table . ': ' . $e->getMessage());
        }
    }
    return $cache[$table];
}

/**
 * Column names are pasted straight into SQL, so anything the table does not have is
 * dropped here. Forms also post leftovers such as an empty "photo" file field.
 */
function onlyTableColumns($db, $table, $input) {
    $cols = tableColumns($db, $table);
    if (empty($cols)) {
        return $input;
    }
    $clean = array();
    foreach ($input as $key => $value) {
        if (in_array($key, $cols, true)) {
            $clean[$key] = $value;
        } else {
            error_log('dropped non-column field "' . $key . '" for table ' . $table);
        }
    }
    return $clean;
}

function handleTable($db, $table, $method, $input, $path) {
    $parts = explode('/', $path);
    $id = isset($parts[1]) ? intval($parts[1]) : null;
    
    // Map tables to upload directories and column names
    $uploadConfig = array(
        'mcs' => array('dir' => 'mcs', 'column' => 'photo'),
        'photographers' => array('dir' => 'photographers', 'column' => 'photo'),
        'cakes' => array('dir' => 'cakes', 'column' => 'photo'),
        'catering' => array('dir' => 'catering', 'column' => 'photo'),
        'decorations' => array('dir' => 'decoration', 'column' => 'photo'),
        'abatahira' => array('dir' => 'abatahira', 'column' => 'photo'),
        'itorero_bands' => array('dir' => 'itorero', 'column' => 'photo'),
        'extra_services' => array('dir' => 'extras', 'column' => 'photo'),
        'sonalization' => array('dir' => 'sonalization', 'column' => 'photo'),
        'service_protocols' => array('dir' => 'service-protocols', 'column' => 'photo'),
        'gallery' => array('dir' => 'gallery', 'column' => 'image', 'required' => true),
        'testimonials' => array('dir' => 'testimonials', 'column' => 'photo')
    );
    $config = isset($uploadConfig[$table]) ? $uploadConfig[$table] : null;
    $uploadDir = $config ? $config['dir'] : null;
    $photoColumn = $config ? $config['column'] : 'photo';
    $photoRequired = $config && !empty($config['required']);

    if ($method === 'GET') {
        if ($id) {
            $stmt = $db->prepare("SELECT * FROM `$table` WHERE id = ? LIMIT 1");
            $stmt->execute(array($id));
            $row = $stmt->fetch();
            if ($row) {
                jsonResponse(array('success' => true, 'data' => $row));
            } else {
                jsonResponse(array('success' => false, 'message' => 'Not found'), 404);
            }
        } else {
            $rows = $db->query("SELECT * FROM `$table` ORDER BY id DESC")->fetchAll();
            jsonResponse(array('success' => true, 'data' => $rows));
        }
    }

    if ($method === 'POST') {
        unset($input['id']);
        
        error_log('POST to table: ' . $table);
        error_log('Upload dir: ' . ($uploadDir ? $uploadDir : 'none'));
        error_log('Files: ' . json_encode($_FILES));
        
        // Handle file upload
        if ($uploadDir) {
            $upload = handleFileUpload($uploadDir);
            if ($upload['error']) {
                jsonResponse(array('success' => false, 'message' => $upload['error']), 400);
            }
            if ($upload['filename']) {
                $input[$photoColumn] = $upload['filename'];
                error_log('Photo filename: ' . $upload['filename']);
            }
        }
        
        $input = onlyTableColumns($db, $table, $input);

        if ($uploadDir && $photoRequired && empty($input[$photoColumn])) {
            jsonResponse(array('success' => false, 'message' => 'Choose a photo before adding this item.'), 400);
        }
        if (empty($input)) {
            jsonResponse(array('success' => false, 'message' => 'No field to save was received.'), 400);
        }

        $columns = array_keys($input);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $placeholders) . ")";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute(array_values($input));
            error_log('Saved item, photo column: ' . (isset($input[$photoColumn]) ? $input[$photoColumn] : 'not set'));
            jsonResponse(array('success' => true, 'data' => array('id' => $db->lastInsertId(), 'photo' => isset($input[$photoColumn]) ? $input[$photoColumn] : null)));
        } catch (PDOException $e) {
            error_log('POST ' . $table . ' failed: ' . $e->getMessage());
            jsonResponse(array('success' => false, 'message' => 'The server could not save this item. Check the API error log.'), 500);
        }
    }

    if ($method === 'PUT' && $id) {
        unset($input['id']);
        
        // Handle file upload
        if ($uploadDir) {
            $upload = handleFileUpload($uploadDir);
            if ($upload['error']) {
                jsonResponse(array('success' => false, 'message' => $upload['error']), 400);
            }
            if ($upload['filename']) {
                $input[$photoColumn] = $upload['filename'];
            }
        }
        
        $input = onlyTableColumns($db, $table, $input);
        if (empty($input)) {
            jsonResponse(array('success' => false, 'message' => 'No field to update was received.'), 400);
        }

        $sets = array();
        foreach (array_keys($input) as $col) {
            $sets[] = "`$col` = ?";
        }
        $sql = "UPDATE `$table` SET " . implode(', ', $sets) . " WHERE id = ?";
        try {
            $stmt = $db->prepare($sql);
            $values = array_values($input);
            $values[] = $id;
            $stmt->execute($values);
            jsonResponse(array('success' => true));
        } catch (PDOException $e) {
            error_log('PUT ' . $table . '/' . $id . ' failed: ' . $e->getMessage());
            jsonResponse(array('success' => false, 'message' => 'The server could not update this item. Check the API error log.'), 500);
        }
    }

    if ($method === 'DELETE' && $id) {
        try {
            $stmt = $db->prepare("DELETE FROM `$table` WHERE id = ?");
            $stmt->execute(array($id));
            jsonResponse(array('success' => true));
        } catch (PDOException $e) {
            serverError('DELETE ' . $table . '/' . $id, $e);
        }
    }

    jsonResponse(array('success' => false, 'message' => 'Method not allowed'), 405);
}
