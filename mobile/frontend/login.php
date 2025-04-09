<?php
include_once '../include/header.php';
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $message = "Tous les champs sont obligatoires.";
    } else {
        $url = 'http://localhost:3000/login';

        $data = json_encode([
            "email" => $email,
            "password" => $password
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 200) {
            $responseData = json_decode($response, true);

            // Vérifiez si le token existe dans la réponse
            if (isset($responseData['token'])) {
                // Stockez le token dans la session
                $_SESSION['token'] = $responseData['token'];
            }

            // Assurez-vous que la réponse contient un champ 'user' avec les informations
            if (isset($responseData['user']['username'])) {
                $_SESSION['user'] = $responseData['user']['username'];
            } elseif (isset($responseData['user']['name'])) {
                $_SESSION['user'] = $responseData['user']['name'];
            } elseif (isset($responseData['user']['email'])) {
                $_SESSION['user'] = $responseData['user']['email']; // Fallback si aucun nom
            } else {
                $_SESSION['user'] = $email; // Dernier recours
            }

            // Stockez l'ID de l'utilisateur dans la session
            if (isset($responseData['user']['id'])) {
                $_SESSION['user_id'] = $responseData['user']['id'];
            }

            // Redirection vers la page d'accueil après une connexion réussie
            header("Location: index.php");
            exit();
        } else {
            $message = "Identifiants incorrects ou erreur de connexion.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4" style="width: 350px;">
        <h2 class="text-center">Connexion</h2>
        <?php if ($message): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
        </form>

        <div class="text-center mt-3">
            <a href="register.php">Je n'ai pas encore de compte, je m'inscris</a>
        </div>
    </div>
</div>
</body>
</html>
