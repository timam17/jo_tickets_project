<?php
include_once '../include/header.php';

// Assurez-vous que le token est disponible
$token = $_SESSION['token'] ?? null;
$matches = [];

if ($token) {
    $url = 'http://localhost:3000/events';
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        $matches = json_decode($response, true);
    } else {
        // Token invalide ou expiré, redirige l'utilisateur vers la page de connexion
        header('Location: login.php');
        exit();
    }
} else {
    // Token manquant, redirige l'utilisateur vers la page de connexion
    header('Location: login.php');
    exit();
}
?>

<div class="container" style="margin-top: 50px;">
    <h2 class="text-white mb-4">Liste des Matchs</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="row gy-4">
        <?php foreach ($matches as $match): ?>
            <?php
                $stadium = $match['stadium'] ?? 'Stade inconnu';
                $start = isset($match['start']) ? date('d/m/Y H:i', strtotime($match['start'])) : 'Date/heure inconnue';
                $teamHome = $match['team_home'] ?? 'Équipe 1';
                $teamAway = $match['team_away'] ?? 'Équipe 2';
                $score = $match['score'] ?? null;
            ?>
            <div class="col-md-6">
                <div class="d-flex bg-dark text-white rounded-4 border border-light p-3 justify-content-between align-items-center" style="position: relative;">
                    <div class="w-100 pe-4">
                        <div class="d-flex justify-content-between mb-2">
                            <div class="border rounded p-2"><?= htmlspecialchars($stadium) ?></div>
                            <div class="border rounded p-2"><?= htmlspecialchars($start) ?></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="border rounded p-2"><?= htmlspecialchars($teamHome) ?> vs <?= htmlspecialchars($teamAway) ?></div>
                            <div class="border rounded p-2"><?= htmlspecialchars($score ?? '-') ?></div>
                        </div>
                    </div>

                    <!-- Affichage du bouton seulement si le score n'est pas défini -->
                    <?php if ($score === null): ?>
                        <div class="bg-primary text-white px-3 py-2 rounded-end-4 text-center" style="writing-mode: vertical-rl; transform: rotate(180deg);">
                            <a href="acheter_ticket.php?event_id=<?= $match['id'] ?>" class="text-white text-decoration-none">Acheter le ticket</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
