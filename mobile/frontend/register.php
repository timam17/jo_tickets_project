<?php
include_once '../include/header.php';
$notification = ''; // Changer le nom de la variable de message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = trim($_POST['user_name']); // Changer 'username' en 'user_name'
    $user_email = trim($_POST['user_email']); // Changer 'email' en 'user_email'
    $user_password = trim($_POST['user_password']); // Changer 'password' en 'user_password'

    if (empty($user_name) || empty($user_email) || empty($user_password)) {
        $notification = "Tous les champs sont nécessaires."; // Message modifié
    } else {
        // URL de l'API pour l'inscription
        $api_url = 'http://localhost:3000/register'; // Renommer l'url

        // Préparer les données à envoyer en JSON
        $request_data = json_encode([
            "username" => $user_name,
            "email" => $user_email,
            "password" => $user_password
        ]);

        // Initialisation de la requête cURL
        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_data);

        // Exécuter la requête
        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Renommer $http_code en $http_status
        curl_close($ch);

        // Gérer les réponses de l'API
        if ($http_status == 201) {
            $_SESSION['user'] = $user_name; // Stocker l'utilisateur dans la session
            header("Location: index.php"); // Rediriger l'utilisateur après inscription
            exit();
        } else {
            $notification = "Erreur : " . $response; // Message d'erreur modifié
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Utilisateur</title> <!-- Titre modifié -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4" style="width: 350px;">
        <h2 class="text-center">Formulaire d'Inscription</h2> <!-- Modifié le titre -->
        <?php if ($notification): ?> <!-- Changer 'message' en 'notification' -->
            <div class="alert alert-danger"><?= htmlspecialchars($notification) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="user_name" class="form-label">Nom d'utilisateur</label> <!-- 'username' modifié -->
                <input type="text" name="user_name" id="user_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="user_email" class="form-label">Email</label> <!-- 'email' modifié -->
                <input type="email" name="user_email" id="user_email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="user_password" class="form-label">Mot de passe</label> <!-- 'password' modifié -->
                <input type="password" name="user_password" id="user_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">S'inscrire</button> <!-- Changer la couleur du bouton -->
        </form>

        <div class="text-center mt-3">
            <a href="login.php">J'ai déjà un compte, je me connecte</a>
        </div>
    </div>
    </div>
</body>
</html>
