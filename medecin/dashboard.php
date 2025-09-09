<?php
session_start();
require __DIR__.'/db.php';
if (empty($_SESSION['medecin_user'])) {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['medecin_user'];

$stmt = $pdo->query("SELECT id, name, phone, email, date_appointment AS date, time_appointment AS time, motif, status FROM appointments ORDER BY date_appointment DESC, time_appointment DESC LIMIT 200");
$appointments = $stmt->fetchAll();
?>
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Dashboard Médecin</title>
<link rel="stylesheet" href="../style.css">
</head>
<body>
<div style="padding:18px;max-width:1000px;margin:18px auto;">
    <h2>Dashboard — <?=htmlspecialchars($user)?></h2>
    <p><a href="../index.html">Retour au site</a> | <a href="logout.php">Se déconnecter</a></p>
    <p style="color:#b00">Si c'est votre première connexion, vous pouvez changer votre mot de passe via le lien "Changer le mot de passe".</p>
    <p>
        <a href="change_password.php">Changer le mot de passe</a>
        | <a href="smtp_settings.php">Paramètres SMTP</a>
    </p>

    <h3>Rendez-vous récents</h3>
    <table border="1" cellpadding="8" style="border-collapse:collapse;width:100%">
        <thead><tr><th>ID</th><th>Nom</th><th>Téléphone</th><th>Email</th><th>Date</th><th>Heure</th><th>Motif</th><th>Statut</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($appointments as $a): ?>
            <tr id="row-<?=htmlspecialchars($a['id'])?>">
                <td><?=htmlspecialchars($a['id'])?></td>
                <td><?=htmlspecialchars($a['name'])?></td>
                <td><?=htmlspecialchars($a['phone'])?></td>
                <td><?=htmlspecialchars($a['email'])?></td>
                <td><?=htmlspecialchars($a['date'])?></td>
                <td><?=htmlspecialchars($a['time'])?></td>
                <td><?=htmlspecialchars($a['motif'])?></td>
                <td class="status-cell"><?=htmlspecialchars($a['status'])?></td>
                <td>
                    <?php if($a['status'] !== 'confirmed'): ?>
                        <button class="action-btn" data-id="<?=htmlspecialchars($a['id'])?>" data-status="confirmed">Confirmer</button>
                    <?php endif; ?>
                    <?php if($a['status'] !== 'cancelled'): ?>
                        <button class="action-btn" data-id="<?=htmlspecialchars($a['id'])?>" data-status="cancelled">Annuler</button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <script>
    async function updateStatus(id, status){
        try{
            const fd = new FormData();
            fd.append('action','update_status');
            fd.append('id', id);
            fd.append('status', status);
            const res = await fetch('api/appointments.php', {method:'POST', body: fd});
            const json = await res.json();
            if(json.success){
                const row = document.getElementById('row-'+id);
                if(row){ row.querySelector('.status-cell').textContent = status; }
            } else {
                alert(json.error || 'Erreur serveur');
            }
        } catch(e){ console.error(e); alert('Erreur réseau'); }
    }

    document.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('click', function(){
            const id = this.getAttribute('data-id');
            const status = this.getAttribute('data-status');
            if(status === 'cancelled' && !confirm('Confirmer l\'annulation?')) return;
            updateStatus(id, status);
        });
    });
    </script>
</div>
</body>
</html>