<?php
session_start(); // Démarrer la session

include '../../backend/db/db.php'; // Assurez-vous que le chemin est correct

// Vérification de la connexion à la base de données
if (!$pdo) {
    die("Erreur de connexion à la base de données.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login']; // Utiliser un champ pour le nom d'utilisateur ou l'email
    $password = $_POST['password'];

    try {
        // Rechercher l'utilisateur par nom d'utilisateur ou email
        $sql = "SELECT * FROM auth_user WHERE username = :login OR email = :login";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':login', $login, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // L'utilisateur est trouvé
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            // Vérifier si le mot de passe correspond
            if (password_verify($password, $user['password'])) {
                // Mot de passe correct, démarrer la session
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];

                // Rediriger vers la page d'accueil ou tableau de bord
                header("Location: dashboard.php");
                exit(); // Terminer l'exécution après la redirection
            } else {
                echo "Mot de passe incorrect.";
            }
        } else {
            echo "Aucun utilisateur trouvé avec ce nom ou email.";
        }
    } catch (PDOException $e) {
        echo "Erreur de connexion à la base de données : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion utilisateur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f9;
        }
        .form-container {
            max-width: 350px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
    <h2>Se connecter</h2>
    <form method="POST">
        <label for="login">Nom d'utilisateur ou E-mail :</label>
        <input type="text" id="login" name="login" required>

        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Connexion</button>
    </form>
    <a href="register.php">Pas encore inscrit ? Créez un compte ici.</a>
</div>

</body>
</html>
