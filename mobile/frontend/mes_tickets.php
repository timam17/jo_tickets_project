<?php
include_once '../include/header.php';

$token = $_SESSION['token'] ?? null;
$error = null;

if (!$token) {
    $error = "Token d'accès manquant. Veuillez vous connecter.";
} else {
    // Récupération des tickets de l'utilisateur connecté via une requête API ou base de données
    $user_id = $_SESSION['user_id']; // Assurez-vous que l'ID de l'utilisateur est stocké dans la session

    // Requête pour récupérer les tickets de l'utilisateur
    $ch = curl_init('http://localhost:3000/my-tickets'); // Remplacez par votre API si nécessaire
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token
    ]);
    $response = curl_exec($ch);
    if ($response === false) {
        $error = "Erreur lors de la récupération des tickets: " . curl_error($ch);
    }
    curl_close($ch);

    if (!$error) {
        $tickets = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $error = "Erreur lors du décodage de la réponse JSON.";
        }
    }
}

?>

<div class="container" style="margin-top: 50px;">">
    <h2 class="text-white mb-4">Mes Tickets</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php else: ?>
        <?php if (empty($tickets)): ?>
            <div class="alert alert-info">Vous n'avez aucun ticket pour l'instant.</div>
        <?php else: ?>
            <div class="row gy-4">
                <?php
                    // Tri des tickets par date
                    usort($tickets, function($a, $b) {
                        $dateA = strtotime($a['start']);
                        $dateB = strtotime($b['start']);
                        return $dateA <=> $dateB;
                    });
                ?>

                <?php foreach ($tickets as $ticket): ?>
                    <div class="col-12">
                        <div class="ticket-card d-flex rounded-4 overflow-hidden">
                            <!-- Partie gauche : infos -->
                            <div class="ticket-info p-3 d-flex flex-column justify-content-center gap-2">
                                <div class="info-box">Stade : <?= htmlspecialchars($ticket['stadium'] ?? 'Non défini') ?></div>
                                <div class="info-box"><?= htmlspecialchars($ticket['home_team'] ?? 'Équipe 1') ?> vs <?= htmlspecialchars($ticket['away_team'] ?? 'Équipe 2') ?></div>
                                <div class="info-box">Date : <?= date('d/m/Y H:i', strtotime($ticket['start'] ?? '')) ?></div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <div class="info-box small-box">Catégorie : <?= htmlspecialchars($ticket['category'] ?? 'Non défini') ?></div>
                                    <div class="info-box small-box">Statut : <?= isset($ticket['used']) ? ($ticket['used'] ? 'Utilisé' : 'Non utilisé') : 'Non défini' ?></div>
                                </div>
                            </div>

                            <!-- Partie droite : QR code -->
                            <div class="ticket-qr d-flex align-items-center justify-content-center">
                                <?php if (!empty($ticket['qr_code'])): ?>
                                    <img src="<?= htmlspecialchars($ticket['qr_code']) ?>" alt="QR Code" />
                                <?php else: ?>
                                    <span>QR Code non dispo</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <a href="index.php" class="btn btn-secondary mt-4">Retour à la liste</a>
</div>

<style>
    .ticket-card {
    background-color: #1f2235;
    border: 1px solid #ccc;
    display: flex;
    min-height: 200px;
}

.ticket-info {
    flex: 1;
    background-color: #2c2e3e;
    color: white;
}

.ticket-qr {
    width: 200px;
    background-color: #22325e;
}

.ticket-qr img {
    width: 150px;
    height: 150px;
    object-fit: contain;
}

.info-box {
    background-color: #2c2e3e;
    border: 1px solid #ccc;
    border-radius: 10px;
    padding: 8px 12px;
    font-size: 14px;
}

.small-box {
    flex: 1;
    text-align: center;
}

@media (max-width: 768px) {
    .ticket-card {
        flex-direction: column;
    }

    .ticket-qr {
        width: 100%;
        justify-content: center;
        padding: 1rem 0;
    }
}


    .qr-code img {
        width: 100%;
        height: auto;
        max-width: 150px; /* Contrôle la taille du QR code */
        object-fit: contain;
    }
    @media (max-width: 768px) {
        .d-flex {
            flex-direction: column; /* Pour une disposition verticale sur les petits écrans */
        }
        .qr-code {
            margin-top: 10px;
        }
    }
</style>
