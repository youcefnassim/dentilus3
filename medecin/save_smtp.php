<?php
session_start();
require __DIR__ . '/db.php';
if (empty($_SESSION['medecin_user'])) {
    header('HTTP/1.1 403 Forbidden'); echo 'Forbidden'; exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: smtp_settings.php'); exit; }
// simple CSRF
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    die('Invalid CSRF token');
}

$allowed = ['SMTP_HOST','SMTP_USER','SMTP_PASS','SMTP_PORT','SMTP_SECURE','MAIL_FROM'];
$env = [];
$envFile = __DIR__ . '/.env';
// read existing env into array
$existingMap = [];
if (file_exists($envFile)) {
    $existing = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($existing as $line) {
        if (trim($line) === '' || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;
        list($k,$v) = explode('=', $line, 2);
        $existingMap[trim($k)] = $v;
    }
}

// server-side validation & build new env values
$errors = [];
foreach ($allowed as $k) {
    $v = isset($_POST[$k]) ? trim($_POST[$k]) : '';
    // sanitize newlines
    $v = str_replace(["\r","\n"], '', $v);

    if ($k === 'SMTP_PORT' && $v !== '' && !ctype_digit($v)) {
        $errors[] = 'SMTP_PORT doit être numérique';
    }
    if ($k === 'SMTP_SECURE' && $v !== '' && !in_array(strtolower($v), ['tls','ssl',''])) {
        $errors[] = 'SMTP_SECURE doit être vide, tls ou ssl';
    }
    if ($k === 'MAIL_FROM' && $v !== '' && !filter_var($v, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'MAIL_FROM non valide';
    }

    // preserve password if left empty
    if ($k === 'SMTP_PASS') {
        if ($v === '') {
            if (isset($existingMap['SMTP_PASS'])) {
                $env['SMTP_PASS'] = $existingMap['SMTP_PASS'];
            } else {
                $env['SMTP_PASS'] = '';
            }
            continue;
        }
    }

    $env[$k] = $v;
}

if (!empty($errors)) {
    // simple error response
    echo '<h3>Erreurs:</h3><ul>'; foreach ($errors as $e) { echo '<li>'.htmlspecialchars($e).'</li>'; } echo '</ul>';
    echo '<p><a href="smtp_settings.php">Retour</a></p>';
    exit;
}

// backup
if (file_exists($envFile)) {
    copy($envFile, $envFile . '.bak.' . time());
}

// preserve other keys (DB_*, ADMIN_EMAIL)
$preserve = ['DB_HOST','DB_NAME','DB_USER','DB_PASS','ADMIN_EMAIL'];
$lines = [];
foreach ($preserve as $k) {
    if (isset($existingMap[$k])) $lines[$k] = $existingMap[$k];
}

// set new values
foreach ($env as $k => $v) {
    $lines[$k] = $v;
}

$out = '';
foreach ($lines as $k => $v) {
    $out .= $k . '=' . $v . "\n";
}

// write atomically
$tmp = $envFile . '.tmp';
if (file_put_contents($tmp, $out) === false) {
    die('Erreur écriture fichier');
}
rename($tmp, $envFile);

// attempt to set file permissions to be owner-only if possible
@chmod($envFile, 0600);

// audit log (do not log secrets). Log who changed and which keys changed; indicate if password changed.
$logFile = __DIR__ . '/smtp_changes.log';
$actor = $_SESSION['medecin_user'] ?? 'unknown';
$now = date('c');
$changed = [];
foreach (['SMTP_HOST','SMTP_USER','SMTP_PORT','SMTP_SECURE','MAIL_FROM'] as $k) {
    $old = isset($existingMap[$k]) ? $existingMap[$k] : '';
    $new = isset($env[$k]) ? $env[$k] : '';
    if ($old !== $new) $changed[] = "$k: '".addslashes($old)."' => '".addslashes($new)."'";
}
// password change flag
$passChanged = false;
if ((isset($existingMap['SMTP_PASS']) ? $existingMap['SMTP_PASS'] : '') !== (isset($env['SMTP_PASS']) ? $env['SMTP_PASS'] : '')) {
    $passChanged = true;
}

$entry = "$now | $actor | changed: ";
$entry .= $changed ? implode('; ', $changed) : 'none';
$entry .= $passChanged ? '; SMTP_PASS:CHANGED' : '; SMTP_PASS:UNCHANGED';
$entry .= "\n";
file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);

// regenerate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(16));

header('Location: smtp_settings.php?saved=1');
exit;
