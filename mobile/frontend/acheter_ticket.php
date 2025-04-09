<?php
include_once '../include/header.php';

$token = $_SESSION['token'] ?? null;
$eventId = $_GET['event_id'] ?? null;
$success = null;
$error = null;

$prix_categories = [
    'Silver' => 30.00,
    'Gold' => 50.00,
    'Platinum' => 100.00
];

if (!$token) {
    $error = "Token d'accès manquant. Veuillez vous connecter.";
} elseif (!$eventId) {
    $error = "ID de l'événement manquant.";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantites = [
        'Silver' => (int)($_POST['silver'] ?? 0),
        'Gold' => (int)($_POST['gold'] ?? 0),
        'Platinum' => (int)($_POST['platinum'] ?? 0)
    ];

    $tickets_envoyes = 0;
    $qr_codes = [];

    foreach ($quantites as $categorie => $quantite) {
        $price = $prix_categories[$categorie];

        for ($i = 0; $i < $quantite; $i++) {
            $data = json_encode([
                'event_id' => $eventId,
                'category' => $categorie,
                'price' => $price
            ]);

            // Requête API pour créer le ticket
            $ch = curl_init('http://localhost:3000/tickets');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ]);
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // Affichage de l'erreur si le code HTTP n'est pas 201
            if ($http_code !== 201) {
                $error = "Erreur API: Code HTTP $http_code. Réponse: " . htmlspecialchars($response);
                break;
            }

            // Traitement du succès si le code HTTP est 201
            if ($http_code === 201) {
                $response_data = json_decode($response, true);
                if (isset($response_data['qrCodeUrl'])) {
                    $qr_codes[] = $response_data['qrCodeUrl'];
                }
                $tickets_envoyes++;
            }
        }
    }

    if ($tickets_envoyes > 0) {
        $success = "$tickets_envoyes billet(s) acheté(s) avec succès.";
    } else {
        if (!$error) {
            $error = "Aucun billet n'a pu être acheté.";
        }
    }
}
?>

<div class="container" style="margin-top: 50px;">">
    <h2 class="text-white mb-4">Acheter des Tickets</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <script>
            // Rediriger après 3 secondes
            setTimeout(function() {
                window.location.href = 'mes_tickets.php';  // Redirection vers la page des tickets
            }, 1200);
        </script>
        <?php if (!empty($qr_codes)): ?>
            <div class="mt-4">
                <h5>QR Codes générés :</h5>
                <ul>
                    <?php foreach ($qr_codes as $qr_code): ?>
                        <li><img src="<?= htmlspecialchars($qr_code) ?>" alt="QR Code" /></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (!$success): ?>
        <form method="POST" onsubmit="return confirmerAchat()" class="bg-dark text-white p-4 rounded">
            <?php foreach ($prix_categories as $categorie => $prix): ?>
                <div class="mb-3">
                    <label class="form-label d-block"><?= $categorie ?> (<?= number_format($prix, 2) ?> €)</label>
                    <div class="input-group" style="max-width: 200px;">
                        <button type="button" class="btn btn-outline-light" onclick="changerQuantite('<?= strtolower($categorie) ?>', -1)">−</button>
                        <input type="number" name="<?= strtolower($categorie) ?>" id="<?= strtolower($categorie) ?>" class="form-control text-center" value="0" min="0" readonly onchange="calculerTotal()">
                        <button type="button" class="btn btn-outline-light" onclick="changerQuantite('<?= strtolower($categorie) ?>', 1)">+</button>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="mt-4">
                <strong>Total billets : </strong><span id="totalBillets">0</span><br>
                <strong>Total à payer : </strong><span id="totalPrix">0.00 €</span>
            </div>

            <button type="submit" class="btn btn-primary mt-4">Valider l'achat</button>
            <a href="index.php" class="btn btn-secondary mt-4 ms-2">Retour à la liste</a>
        </form>
    <?php endif; ?>
</div>

<script>
    const prix = {
        silver: <?= $prix_categories['Silver'] ?>,
        gold: <?= $prix_categories['Gold'] ?>,
        platinum: <?= $prix_categories['Platinum'] ?>
    };

    function changerQuantite(id, delta) {
        const input = document.getElementById(id);
        let val = parseInt(input.value) || 0;
        val = Math.max(0, val + delta);
        input.value = val;
        calculerTotal();
    }

    function calculerTotal() {
        let totalBillets = 0;
        let totalPrix = 0;

        for (let key in prix) {
            const val = parseInt(document.getElementById(key).value) || 0;
            totalBillets += val;
            totalPrix += val * prix[key];
        }

        document.getElementById("totalBillets").innerText = totalBillets;
        document.getElementById("totalPrix").innerText = totalPrix.toFixed(2) + " €";
    }

    function confirmerAchat() {
        const total = parseInt(document.getElementById("totalBillets").innerText);
        if (total === 0) {
            alert("Veuillez sélectionner au moins un ticket.");
            return false;
        }
        return confirm("Confirmez-vous l'achat de " + total + " billet(s) ?");
    }
</script>
