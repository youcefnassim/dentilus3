<?php
session_start();
require __DIR__ . '/db.php';
if (empty($_SESSION['medecin_user'])) {
    header('Location: login.php'); exit;
}
$user = $_SESSION['medecin_user'];

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

// load current env values if present
$envFile = __DIR__ . '/.env';
$values = [];
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (trim($line) === '' || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;
        list($k,$v) = explode('=', $line, 2);
        $values[trim($k)] = trim($v);
    }
}

function val($k, $d='') { global $values; return isset($values[$k]) ? $values[$k] : $d; }
?>
<!doctype html>
<html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Paramètres SMTP</title>
<link rel="stylesheet" href="../style.css"></head><body>
<div style="max-width:720px;margin:24px auto;padding:18px;border:1px solid #eee;border-radius:6px;">
    <h2>Paramètres SMTP</h2>
    <p>Utilisateur connecté : <?=htmlspecialchars($user)?></p>
    <?php if (!empty($_GET['saved'])): ?>
        <div style="padding:10px;background:#e8f7e8;border:1px solid #c6ecc6;margin-bottom:12px;">Sauvegarde enregistrée. Un fichier de sauvegarde a été créé si un .env existait.</div>
    <?php endif; ?>

    <form id="smtpForm" method="post" action="save_smtp.php">
        <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($_SESSION['csrf_token'])?>">
        <label>SMTP_HOST</label>
        <input name="SMTP_HOST" value="<?=htmlspecialchars(val('SMTP_HOST'))?>" placeholder="smtp.example.com" required>
        <label>SMTP_USER</label>
        <input name="SMTP_USER" value="<?=htmlspecialchars(val('SMTP_USER'))?>" placeholder="username@example.com">
    <label>SMTP_PASS (laisser vide pour conserver le mot de passe actuel)</label>
    <input name="SMTP_PASS" value="" placeholder="mot de passe" autocomplete="new-password">
        <label>SMTP_PORT</label>
        <input name="SMTP_PORT" value="<?=htmlspecialchars(val('SMTP_PORT', '587'))?>">
        <label>SMTP_SECURE</label>
        <input name="SMTP_SECURE" value="<?=htmlspecialchars(val('SMTP_SECURE', 'tls'))?>" placeholder="tls or ssl">
        <label>MAIL_FROM</label>
        <input name="MAIL_FROM" value="<?=htmlspecialchars(val('MAIL_FROM', 'no-reply@dentilus.local'))?>">
        <div style="margin-top:12px;"><button type="submit">Enregistrer</button> <a href="dashboard.php">Annuler</a></div>
    </form>
</div>
<script>
document.getElementById('smtpForm').addEventListener('submit', function(e){
    const host = this.querySelector('[name="SMTP_HOST"]').value.trim();
    const port = this.querySelector('[name="SMTP_PORT"]').value.trim();
    const secure = this.querySelector('[name="SMTP_SECURE"]').value.trim().toLowerCase();
    const mailfrom = this.querySelector('[name="MAIL_FROM"]').value.trim();
    if (!host) { alert('SMTP_HOST est requis'); e.preventDefault(); return false; }
    if (port && isNaN(port)) { alert('SMTP_PORT doit être un nombre'); e.preventDefault(); return false; }
    if (secure && !['tls','ssl',''].includes(secure)) { if(!confirm('Le champ SMTP_SECURE contient une valeur inhabituelle («'+secure+'»). Continuer?')){ e.preventDefault(); return false;} }
    if (mailfrom && !/^\S+@\S+\.\S+$/.test(mailfrom)) { if(!confirm('MAIL_FROM ne ressemble pas à une adresse e-mail valide. Continuer?')){ e.preventDefault(); return false;} }
});
</script>
</body></html>
