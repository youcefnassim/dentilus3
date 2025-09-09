<?php
header('Content-Type: application/json; charset=utf-8');
// Simple API pour créer, lister et mettre à jour les rendez-vous
require __DIR__ . '/../db.php';
require __DIR__ . '/mailer.php';

// Helper pour renvoyer erreur JSON
function json_err($msg, $code = 400){ http_response_code($code); echo json_encode(['error'=>$msg]); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Accepte form-data ou JSON
    $action = $_POST['action'] ?? null;

    // Création de RDV
    if (!$action) {
        $name = $_POST['name'] ?? null;
        $phone = $_POST['phone'] ?? null;
        $email = $_POST['email'] ?? null;
        $date = $_POST['date'] ?? null;
        $time = $_POST['time'] ?? null;
        $motif = $_POST['motif'] ?? null;

        if (!$name || !$phone || !$date || !$time) {
            json_err('Champs requis manquants', 400);
        }
        // validation serveur
        if (!preg_match('/^[0-9+\s().-]{6,20}$/', $phone)) json_err('Téléphone invalide', 400);
        $dtime = DateTime::createFromFormat('Y-m-d', $date);
        if (!$dtime) json_err('Date invalide', 400);
        $tobj = DateTime::createFromFormat('H:i', $time);
        if (!$tobj) json_err('Heure invalide', 400);
        // prevent past dates
        $now = new DateTime('now');
        $dtFull = DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $time);
        if ($dtFull && $dtFull < $now) json_err('La date/heure ne peut pas être dans le passé', 400);
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) json_err('Email invalide', 400);
        if ($motif && mb_strlen($motif) > 2000) json_err('Motif trop long', 400);

        $stmt = $pdo->prepare('INSERT INTO appointments (name, phone, email, date_appointment, time_appointment, motif, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
        try {
            $stmt->execute([$name, $phone, $email, $date, $time, $motif, 'pending']);
            $id = $pdo->lastInsertId();

            // Envoi d'email de notification au médecin/admin (si configuré)
            if (isset($ADMIN_EMAIL) && filter_var($ADMIN_EMAIL, FILTER_VALIDATE_EMAIL)) {
                $subject = "Nouveau rendez-vous - {$name} ({$date} {$time})";
                $message = "Un nouveau rendez-vous a été pris:\n\n" .
                    "Nom: {$name}\nTéléphone: {$phone}\nEmail: {$email}\nDate: {$date}\nHeure: {$time}\nMotif: {$motif}\n\nConnectez-vous à l'espace médecin pour gérer les rendez-vous.";
                // use mailer wrapper
                @sendNotification($ADMIN_EMAIL, $subject, $message);
            }

            echo json_encode(['success' => true, 'id' => $id]);
        } catch (Exception $e) {
            json_err('Erreur insertion: '.$e->getMessage(), 500);
        }
        exit;
    }

    // Mise à jour de statut depuis dashboard
    if ($action === 'update_status'){
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $status = $_POST['status'] ?? '';
        if (!$id || !$status) json_err('ID ou status manquant', 400);
        $allowed = ['pending','confirmed','cancelled','completed'];
        if (!in_array($status, $allowed)) json_err('Status invalide', 400);
        $u = $pdo->prepare('UPDATE appointments SET status = ? WHERE id = ?');
        try{
            $u->execute([$status, $id]);
            echo json_encode(['success' => true]);
        }catch(Exception $e){ json_err('Erreur update: '.$e->getMessage(), 500); }
        exit;
    }

    json_err('Action non reconnue', 400);
}

// GET -> liste
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query('SELECT id, name, phone, email, date_appointment AS date, time_appointment AS time, motif, status, created_at FROM appointments ORDER BY date_appointment, time_appointment');
    $rows = $stmt->fetchAll();
    echo json_encode(['success' => true, 'appointments' => $rows]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Méthode non autorisée']);

?>