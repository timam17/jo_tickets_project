<?php
include_once '../include/header.php';

$accessToken = $_SESSION['token'] ?? null;
$eventIdentifier = $_GET['event_id'] ?? null;
$successMessage = null;
$errorMessage = null;

$ticketPrices = [
    'Silver' => 30.00,
    'Gold' => 50.00,
    'Platinum' => 100.00
];

if (!$accessToken) {
    $errorMessage = "L'accès à votre compte est nécessaire. Veuillez vous connecter pour acheter des billets.";
} elseif (!$eventIdentifier) {
    $errorMessage = "L'ID de l'événement est manquant. Impossible de procéder.";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantities = [
        'Silver' => (int)($_POST['silver'] ?? 0),
        'Gold' => (int)($_POST['gold'] ?? 0),
        'Platinum' => (int)($_POST['platinum'] ?? 0)
    ];

    $totalTicketsPurchased = 0;
    $generatedQrCodes = [];

    foreach ($quantities as $category => $quantity) {
        $ticketPrice = $ticketPrices[$category];

        for ($i = 0; $i < $quantity; $i++) {
            $requestData = json_encode([
                'event_id' => $eventIdentifier,
                'category' => $category,
                'price' => $ticketPrice
            ]);

            // API Request to create ticket
            $ch = curl_init('http://localhost:3000/tickets');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json'
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // Handle the error if HTTP code is not 201
            if ($httpCode !== 201) {
                $errorMessage = "Erreur de l'API : Code HTTP $httpCode. Réponse : " . htmlspecialchars($response);
                break;
            }

            // Handle success when HTTP code is 201
            if ($httpCode === 201) {
                $responseData = json_decode($response, true);
                if (isset($responseData['qrCodeUrl'])) {
                    $generatedQrCodes[] = $responseData['qrCodeUrl'];
                }
                $totalTicketsPurchased++;
            }
        }
    }

    if ($totalTicketsPurchased > 0) {
        $successMessage = "$totalTicketsPurchased billet(s) acheté(s) avec succès ! Vous pouvez retrouver vos billets dans votre espace personnel.";
    } else {
        if (!$errorMessage) {
            $errorMessage = "Aucun billet n'a été acheté. Vérifiez vos sélections.";
        }
    }
}
?>

<div class="container" style="margin-top: 50px;">
    <h2 class="text-white mb-4">Achetez vos billets pour l'événement JO 2024</h2>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
    <?php elseif ($successMessage): ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
        <script>
            // Rediriger vers la page des tickets après 3 secondes
            setTimeout(function() {
                window.location.href = 'mes_tickets.php';  // Redirection vers mes_tickets.php
            }, 1200);
        </script>
        <?php if (!empty($generatedQrCodes)): ?>
            <div class="mt-4">
                <h5>Vos QR Codes :</h5>
                <ul>
                    <?php foreach ($generatedQrCodes as $qrCode): ?>
                        <li><img src="<?= htmlspecialchars($qrCode) ?>" alt="QR Code" /></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (!$successMessage): ?>
        <form method="POST" onsubmit="return confirmPurchase()" class="bg-dark text-white p-4 rounded">
            <?php foreach ($ticketPrices as $category => $price): ?>
                <div class="mb-3">
                    <label class="form-label d-block"><?= $category ?> (<?= number_format($price, 2) ?> €)</label>
                    <div class="input-group" style="max-width: 200px;">
                        <button type="button" class="btn btn-outline-light" onclick="adjustQuantity('<?= strtolower($category) ?>', -1)">−</button>
                        <input type="number" name="<?= strtolower($category) ?>" id="<?= strtolower($category) ?>" class="form-control text-center" value="0" min="0" readonly onchange="calculateTotal()">
                        <button type="button" class="btn btn-outline-light" onclick="adjustQuantity('<?= strtolower($category) ?>', 1)">+</button>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="mt-4">
                <strong>Total billets : </strong><span id="totalTickets">0</span><br>
                <strong>Total à payer : </strong><span id="totalPrice">0.00 €</span>
            </div>

            <button type="submit" class="btn btn-primary mt-4">Confirmer l'achat</button>
            <a href="index.php" class="btn btn-secondary mt-4 ms-2">Retour à la liste</a>
        </form>
    <?php endif; ?>
</div>

<script>
    const prices = {
        silver: <?= $ticketPrices['Silver'] ?>,
        gold: <?= $ticketPrices['Gold'] ?>,
        platinum: <?= $ticketPrices['Platinum'] ?>
    };

    function adjustQuantity(id, delta) {
        const input = document.getElementById(id);
        let currentValue = parseInt(input.value) || 0;
        currentValue = Math.max(0, currentValue + delta);
        input.value = currentValue;
        calculateTotal();
    }

    function calculateTotal() {
        let totalTickets = 0;
        let totalAmount = 0;

        for (let key in prices) {
            const quantity = parseInt(document.getElementById(key).value) || 0;
            totalTickets += quantity;
            totalAmount += quantity * prices[key];
        }

        document.getElementById("totalTickets").innerText = totalTickets;
        document.getElementById("totalPrice").innerText = totalAmount.toFixed(2) + " €";
    }

    function confirmPurchase() {
        const total = parseInt(document.getElementById("totalTickets").innerText);
        if (total === 0) {
            alert("Vous devez sélectionner au moins un billet avant de confirmer.");
            return false;
        }
        return confirm("Êtes-vous sûr de vouloir acheter " + total + " billet(s) ?");
    }
</script>
