<?php
// Load .env if present
require_once __DIR__ . '/config.php';

// Configuration de connexion à la base de données — modifiez via .env ou ici
$DB_HOST = env('DB_HOST', '127.0.0.1');
$DB_NAME = env('DB_NAME', 'dentilus');
$DB_USER = env('DB_USER', 'root');
$DB_PASS = env('DB_PASS', '');

// Email administrateur pour notifications (changer en production)
$ADMIN_EMAIL = env('ADMIN_EMAIL', 'contact@dentilus.fr');

// SMTP / PHPMailer configuration (optionnel) - laissez vides pour utiliser mail() en fallback
$SMTP_HOST = env('SMTP_HOST', '');
$SMTP_USER = env('SMTP_USER', '');
$SMTP_PASS = env('SMTP_PASS', '');
$SMTP_PORT = env('SMTP_PORT', 587);
$SMTP_SECURE = env('SMTP_SECURE', 'tls'); // 'ssl' or 'tls'
$MAIL_FROM = env('MAIL_FROM', 'no-reply@dentilus.local');

try {
    $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    // Si inclusion via navigateur, afficher erreur basique
    if (php_sapi_name() !== 'cli') {
        echo '<pre>Erreur connexion BDD: '.htmlspecialchars($e->getMessage()).'</pre>';
    }
    // pour les scripts API, on pourra attraper cette erreur aussi
    throw $e;
}

?>