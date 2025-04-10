<?php
include_once '../include/header.php';
$login_message = '';  // Renommer $message en $login_message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_email = trim($_POST['email']);  // Renommer $email en $user_email
    $user_password = trim($_POST['password']);  // Renommer $password en $user_password

    if (empty($user_email) || empty($user_password)) {
        $login_message = "Veuillez remplir tous les champs.";  // Modifié le message d'erreur
    } else {
        $api_url = 'http://localhost:3000/login';  // Renommer $url en $api_url

        $credentials = json_encode([  // Renommer $data en $credentials
            "email" => $user_email,
            "password" => $user_password
        ]);

        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $credentials);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 200) {
            $response_data = json_decode($response, true);  // Renommer $responseData en $response_data

            if (isset($response_data['token'])) {
                $_SESSION['token'] = $response_data['token'];
            }

            if (isset($response_data['user']['username'])) {
                $_SESSION['user'] = $response_data['user']['username'];
            } elseif (isset($response_data['user']['name'])) {
                $_SESSION['user'] = $response_data['user']['name'];
            } elseif (isset($response_data['user']['email'])) {
                $_SESSION['user'] = $response_data['user']['email'];  // Fallback pour l'email
            } else {
                $_SESSION['user'] = $user_email;  // Dernière solution
            }

            if (isset($response_data['user']['id'])) {
                $_SESSION['user_id'] = $response_data['user']['id'];
            }

            header("Location: dashboard.php");  // Rediriger vers une autre page après la connexion
            exit();
        } else {
            $login_message = "Erreur de connexion ou identifiants incorrects.";  // Nouveau message d'erreur
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
        <h2 class="text-center text-primary">Se connecter</h2>  <!-- Changer le titre -->
        <?php if ($login_message): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($login_message) ?></div>  <!-- Changer l'affichage du message -->
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Adresse email</label>  <!-- Changer le libellé -->
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>  <!-- Changer le libellé -->
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Connexion</button>  <!-- Changer le texte du bouton -->
        </form>

        <div class="text-center mt-3">
            <a href="register.php">Pas encore de compte ? Inscrivez-vous ici.</a>  <!-- Texte modifié -->
        </div>
    </div>
</div>
</body>
</html>
