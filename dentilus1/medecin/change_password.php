<?php
session_start();
require __DIR__ . '/db.php';

if (empty($_SESSION['medecin_user'])) {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['medecin_user'];
$msg = '';
// handle form
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    if (!$password || strlen($password) < 6) {
        $msg = 'Le mot de passe doit contenir au moins 6 caractères.';
    } elseif ($password !== $confirm) {
        $msg = 'Les deux mots de passe ne correspondent pas.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $u = $pdo->prepare('UPDATE users SET password_hash = ?, must_change_password = 0 WHERE username = ?');
        $u->execute([$hash, $user]);
        $msg = 'Mot de passe mis à jour. Vous êtes redirigé...';
        header('Refresh:2; url=dashboard.php');
    }
}
?>
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Changer le mot de passe</title>
<link rel="stylesheet" href="../style.css">
</head>
<body>
<div style="max-width:420px;margin:40px auto;padding:20px;border:1px solid #eee;border-radius:6px;">
    <h2>Changer le mot de passe</h2>
    <?php if($msg) echo '<div style="color:green">'.htmlspecialchars($msg).'</div>'; ?>
    <form method="post">
        <label>Nouveau mot de passe</label>
        <input name="password" type="password" required>
        <label>Confirmer</label>
        <input name="confirm" type="password" required>
        <button type="submit">Enregistrer</button>
    </form>
</div>
</body>
</html>
