<?php
// Script d'assistance pour créer un utilisateur admin initial.
// Exécutez ce fichier une seule fois depuis le navigateur puis supprimez-le pour des raisons de sécurité.
require __DIR__.'/db.php';

$username = 'admin';
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

try{
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(100) UNIQUE, password_hash VARCHAR(255), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
    $stmt = $pdo->prepare('INSERT IGNORE INTO users (username, password_hash) VALUES (?, ?)');
    $stmt->execute([$username, $hash]);
    echo "Admin créé: user=$username password=$password — supprimez ce fichier maintenant.";
}catch(Exception $e){
    echo 'Erreur: '.$e->getMessage();
}
