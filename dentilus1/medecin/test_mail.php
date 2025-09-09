<?php
// Test d'envoi de mail via medecin/mailer.php
require __DIR__ . '/mailer.php';

$to = $ADMIN_EMAIL ?? 'test@example.com';
$subject = 'Test d\'envoi - Dentilus';
$body = "Ceci est un test d'envoi depuis medecin/test_mail.php\nDate: " . date('c');

$ok = sendNotification($to, $subject, $body);
if ($ok) {
    echo "OK: email envoyé à {$to}\n";
} else {
    echo "ERREUR: l'envoi a échoué. Vérifiez la configuration SMTP ou les logs.\n";
}
