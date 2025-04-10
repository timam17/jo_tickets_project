<?php
include_once '../include/header.php';

$access_token = $_SESSION['token'] ?? null; // Renommer $token en $access_token
$error_message = null; // Renommer $error en $error_message

if (!$access_token) {
    $error_message = "Token manquant. Veuillez vous connecter."; // Modifier le message
} else {
    // Récupérer l'ID de l'utilisateur
    $user_id = $_SESSION['user_id'];

    // Requête pour récupérer les tickets
    $ch = curl_init('http://localhost:3000/my-tickets'); // URL modifiée pour l'API
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $access_token
    ]);
    $response = curl_exec($ch);
    if ($response === false) {
        $error_message = "Erreur lors de la récupération des tickets: " . curl_error($ch); // Message d'erreur modifié
    }
    curl_close($ch);

    if (!$error_message) {
        $tickets_list = json_decode($response, true); // Renommer $tickets en $tickets_list
        if (json_last_error() !== JSON_ERROR_NONE) {
            $error_message = "Erreur lors du traitement de la réponse JSON.";
        }
    }
}
?>

<div class="container mt-5">
    <h2 class="text-light mb-4">Mes Tickets</h2>

    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div> <!-- Modifier l'affichage d'erreur -->
    <?php else: ?>
        <?php if (empty($tickets_list)): ?>
            <div class="alert alert-info">Vous n'avez aucun ticket pour le moment.</div> <!-- Message modifié -->
        <?php else: ?>
            <div class="row gy-4">
                <?php
                    // Tri des tickets par date de début
                    usort($tickets_list, function($a, $b) {
                        $dateA = strtotime($a['start']);
                        $dateB = strtotime($b['start']);
                        return $dateA <=> $dateB;
                    });
                ?>

                <?php foreach ($tickets_list as $ticket): ?>
                    <div class="col-12">
                        <div class="ticket-card rounded-3 shadow">
                            <!-- Informations du ticket -->
                            <div class="ticket-info p-3">
                                <div class="info-box">Stade : <?= htmlspecialchars($ticket['stadium'] ?? 'Inconnu') ?></div> <!-- Renommé l'info "Non défini" en "Inconnu" -->
                                <div class="info-box"><?= htmlspecialchars($ticket['home_team'] ?? 'Équipe A') ?> vs <?= htmlspecialchars($ticket['away_team'] ?? 'Équipe B') ?></div>
                                <div class="info-box">Date : <?= date('d/m/Y H:i', strtotime($ticket['start'] ?? '')) ?></div>
                                <div class="d-flex gap-2">
                                    <div class="info-box">Catégorie : <?= htmlspecialchars($ticket['category'] ?? 'Inconnu') ?></div>
                                    <div class="info-box">Statut : <?= isset($ticket['used']) ? ($ticket['used'] ? 'Utilisé' : 'Non utilisé') : 'Non défini' ?></div>
                                </div>
                            </div>

                            <!-- QR Code -->
                            <div class="ticket-qr d-flex justify-content-center align-items-center">
                                <?php if (!empty($ticket['qr_code'])): ?>
                                    <img src="<?= htmlspecialchars($ticket['qr_code']) ?>" alt="QR Code" class="img-fluid" />
                                <?php else: ?>
                                    <span>QR Code non disponible</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <a href="index.php" class="btn btn-secondary mt-4">Retour à l'accueil</a> <!-- Message modifié -->
</div>

<style>
    .ticket-card {
        background-color: #21232b;
        border: 1px solid #444;
        display: flex;
        min-height: 220px;
        margin-bottom: 15px;
    }

    .ticket-info {
        flex: 1;
        background-color: #333;
        color: white;
    }

    .ticket-qr {
        width: 180px;
        background-color: #444;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .ticket-qr img {
        width: 130px;
        height: 130px;
        object-fit: contain;
    }

    .info-box {
        background-color: #444;
        border: 1px solid #555;
        border-radius: 8px;
        padding: 10px 15px;
        font-size: 15px;
        margin-bottom: 5px;
    }

    @media (max-width: 768px) {
        .ticket-card {
            flex-direction: column;
        }

        .ticket-qr {
            width: 100%;
            padding: 10px;
        }
    }
</style>
