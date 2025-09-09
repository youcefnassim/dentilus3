<?php
// Test database connection for Laragon setup
echo "<h2>Test de connexion à la base de données</h2>";

try {
    require __DIR__ . '/db.php';
    echo "<p style='color: green;'>✓ Connexion à la base de données réussie!</p>";
    
    // Test if tables exist
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    if (empty($tables)) {
        echo "<p style='color: orange;'>⚠ Base de données connectée mais aucune table trouvée. Importez install.sql</p>";
    } else {
        echo "<p style='color: green;'>✓ Tables trouvées: " . implode(', ', $tables) . "</p>";
    }
    
    // Test user table if it exists
    if (in_array('users', $tables)) {
        $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        echo "<p style='color: blue;'>ℹ Nombre d'utilisateurs: $userCount</p>";
    }
    
    // Test appointments table if it exists
    if (in_array('appointments', $tables)) {
        $appointmentCount = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
        echo "<p style='color: blue;'>ℹ Nombre de rendez-vous: $appointmentCount</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Erreur de connexion: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<h3>Vérifications à faire:</h3>";
    echo "<ul>";
    echo "<li>Laragon est-il démarré?</li>";
    echo "<li>MySQL fonctionne-t-il sur le port 3306?</li>";
    echo "<li>Le fichier .env existe-t-il dans le dossier medecin?</li>";
    echo "<li>La base de données 'dentilus' a-t-elle été créée?</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<h3>Configuration actuelle:</h3>";
echo "<ul>";
echo "<li>Host: " . (defined('DB_HOST') ? DB_HOST : env('DB_HOST', '127.0.0.1')) . "</li>";
echo "<li>Database: " . (defined('DB_NAME') ? DB_NAME : env('DB_NAME', 'dentilus')) . "</li>";
echo "<li>User: " . (defined('DB_USER') ? DB_USER : env('DB_USER', 'root')) . "</li>";
echo "<li>Port: 3306 (MySQL par défaut)</li>";
echo "</ul>";

echo "<p><strong>Accès via Nginx:</strong> <a href='http://localhost:8080/dentilus1/medecin/'>http://localhost:8080/dentilus1/medecin/</a></p>";
?>
