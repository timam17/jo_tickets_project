<?php
include_once '../include/header.php';

// Fonction de récupération des détails des événements
function fetchEventDetails($authToken) {
    $url = 'http://localhost:3000/events';
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $authToken,
        'Content-Type: application/json'
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        return json_decode($response, true);
    } else {
        return null;
    }
}

// Récupération du token de session
$userToken = $_SESSION['token'] ?? null;
$matchesList = [];

if ($userToken) {
    $matchesList = fetchEventDetails($userToken);
    
    if (!$matchesList) {
        // Si la récupération des événements échoue, redirection vers la page de connexion
        header('Location: login.php');
        exit();
    }
} else {
    // Si aucun token, redirection vers la page de connexion
    header('Location: login.php');
    exit();
}
?>

<div class="container" style="margin-top: 50px;">
    <h2 class="text-white mb-4">Nos Prochains Matchs</h2>

    <div class="row gy-4">
        <?php foreach ($matchesList as $matchDetails): ?>
            <?php
                $stadiumLocation = $matchDetails['stadium'] ?? 'Lieu inconnu';
                $matchStart = isset($matchDetails['start']) ? date('d/m/Y H:i', strtotime($matchDetails['start'])) : 'Date et heure non définies';
                $homeTeam = $matchDetails['team_home'] ?? 'Équipe Maison';
                $awayTeam = $matchDetails['team_away'] ?? 'Équipe Invité';
                $matchScore = $matchDetails['score'] ?? null;
                $isMatchPast = strtotime($matchDetails['start']) < time(); // Vérification si le match est passé
            ?>
            <div class="col-md-6">
                <div class="d-flex bg-dark text-white rounded-4 border border-light p-3 justify-content-between align-items-center" style="position: relative;">
                    <div class="w-100 pe-4">
                        <div class="d-flex justify-content-between mb-2">
                            <div class="border rounded p-2"><?= htmlspecialchars($stadiumLocation) ?></div>
                            <div class="border rounded p-2"><?= htmlspecialchars($matchStart) ?></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="border rounded p-2"><?= htmlspecialchars($homeTeam) ?> vs <?= htmlspecialchars($awayTeam) ?></div>
                            <div class="border rounded p-2"><?= htmlspecialchars($matchScore ?? '-') ?></div>
                        </div>
                    </div>

                    <!-- Affichage du bouton seulement si le score n'est pas défini et que le match est à venir -->
                    <?php if ($matchScore === null && !$isMatchPast): ?>
                        <div class="bg-success text-white px-3 py-2 rounded-end-4 text-center" style="writing-mode: vertical-rl; transform: rotate(180deg);">
                            <a href="acheter_ticket.php?event_id=<?= $matchDetails['id'] ?>" class="text-white text-decoration-none">Acheter un ticket</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
