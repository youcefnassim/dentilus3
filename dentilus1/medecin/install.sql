-- Install SQL pour l'espace médecin
CREATE DATABASE IF NOT EXISTS `dentilus` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `dentilus`;

-- Table des rendez-vous
CREATE TABLE IF NOT EXISTS `appointments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `date_appointment` DATE NOT NULL,
  `time_appointment` TIME NOT NULL,
  `motif` TEXT DEFAULT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table des utilisateurs (médecins / admin)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `must_change_password` TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Notes:
-- 1) Importez ce fichier via phpMyAdmin ou mysql CLI. Exemple (Windows/XAMPP) :
--    cd C:\\xampp\\mysql\\bin
--    .\\mysql -u root < "C:\\xampp\\htdocs\\dentilus\\medecin\\install.sql"
-- 2) Après import, exécutez `medecin/create_admin.php` une seule fois pour créer un compte admin, puis supprimez ce fichier pour des raisons de sécurité.

-- Optionnel : créer un compte admin initial (mot de passe = admin123)
-- NOTE: ce mot de passe doit être changé immédiatement après le premier login.
INSERT INTO `users` (`username`, `password_hash`, `must_change_password`) VALUES ('admin', SHA2('admin123', 256), 1)
ON DUPLICATE KEY UPDATE username = username;
