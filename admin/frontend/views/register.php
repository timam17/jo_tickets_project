<?php
session_start(); // Démarrer la session

include '../../backend/db/db.php'; // Assurez-vous que le chemin est correct

// Vérification de la connexion à la base de données
if (!$pdo) {
    die("Impossible de se connecter à la base de données.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Sécuriser le mot de passe

    try {
        // Vérifier si l'utilisateur existe déjà
        $sql = "SELECT * FROM auth_user WHERE username = :username OR email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo "Ce nom d'utilisateur ou cet email est déjà utilisé.";
        } else {
            // Insérer le nouvel utilisateur (administrateur)
            $sql = "INSERT INTO auth_user (username, email, password, is_superuser, is_active) VALUES (:username, :email, :password, 1, 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);

            if ($stmt->execute()) {
                // Inscription réussie, ouvrir la session et rediriger
                $_SESSION['username'] = $username; // Définir la variable de session
                $_SESSION['email'] = $email; // Optionnel : enregistrer l'email

                // Rediriger vers la page d'accueil
                header("Location: index.php");
                exit(); // Terminer l'exécution après la redirection
            } else {
                echo "Une erreur est survenue lors de l'inscription.";
            }
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Administrateur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f7f7f7;
        }
        .form-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .form-container label,
        .form-container input,
        .form-container button {
            width: 100%;
            margin-bottom: 15px;
        }
        .form-container button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #218838;
        }
        .form-container a {
            display: block;
            text-align: center;
            color: #007BFF;
            text-decoration: none;
        }
        .form-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Créer un compte Administrateur</h2>
    <form method="POST">
        <label for="username">Nom d'utilisateur :</label>
        <input type="text" id="username" name="username" required>

        <label for="email">E-mail :</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">S'inscrire</button>
    </form>
    <a href="login.php">Déjà inscrit ? Connectez-vous ici.</a>
</div>

</body>
</html>
