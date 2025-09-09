<?php
session_start();
require __DIR__ . '/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
    $stmt = $pdo->prepare('SELECT id, username, password_hash, must_change_password FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user) {
            $stored = $user['password_hash'];
            $ok = false;
            // si hash bcrypt
            if (password_get_info($stored)['algo'] !== 0) {
                $ok = password_verify($password, $stored);
            } else {
                // essayer SHA256 legacy
                if (hash('sha256', $password) === $stored) {
                    $ok = true;
                    // migrer vers bcrypt
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $u = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
                    $u->execute([$newHash, $user['id']]);
                }
            }

            if ($ok) {
                $_SESSION['medecin_user'] = $user['username'];
                // if must change password, redirect
                if (!empty($user['must_change_password'])){
                    header('Location: change_password.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit;
            } else {
                $error = 'Identifiants invalides';
            }
        } else {
            $error = 'Identifiants invalides';
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login Espace Médecin</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div style="max-width:420px;margin:40px auto;padding:20px;border:1px solid #eee;border-radius:6px;">
    <h2>Espace Médecin</h2>
    <?php if($error): ?>
        <div style="color:#b00020"><?=htmlspecialchars($error)?></div>
    <?php endif; ?>
    <form method="post">
        <label>Utilisateur</label>
        <input name="username" required>
        <label>Mot de passe</label>
        <input name="password" type="password" required>
        <button type="submit">Se connecter</button>
    </form>
    <p style="margin-top:10px"><a href="dashboard.php">Aller au dashboard (si connecté)</a></p>
</div>
</body>
</html>
