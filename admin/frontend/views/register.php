<?php
session_start(); // Démarrer la session

include '../../backend/db/db.php'; // Assurez-vous que le chemin est correct

// Vérifier si $pdo est défini et est un objet PDO valide
if (!$pdo) {
    die("Échec de la connexion à la base de données.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Sécuriser le mot de passe

    try {
        // Vérifier si l'utilisateur existe déjà
        $sql = "SELECT * FROM auth_user WHERE username = :username OR email = :email";
        $stmt = $pdo->prepare($sql); // Utiliser $pdo ici au lieu de $conn
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo "L'utilisateur existe déjà.";
        } else {
            // Insérer un nouvel utilisateur (administrateur)
            $sql = "INSERT INTO auth_user (username, email, password, is_superuser, is_active) VALUES (:username, :email, :password, 1, 1)";
            $stmt = $pdo->prepare($sql); // Utiliser $pdo ici au lieu de $conn
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);

            if ($stmt->execute()) {
                // L'inscription a réussi, ouvrir la session et rediriger
                $_SESSION['username'] = $username; // Définir la variable de session
                $_SESSION['email'] = $email; // Vous pouvez aussi stocker l'email si nécessaire

                // Rediriger vers la page index.php
                header("Location: index.php");
                exit(); // Terminer l'exécution du script après la redirection
            } else {
                echo "Erreur lors de l'inscription.";
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
        a{
            display: flex;
            justify-content: center;
            margin-top: 20px;
            text-decoration: none;
            color: #007BFF;
        }
        form {
            max-width: 400px;
            margin: 0 auto;
            padding: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        label, input, button {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }
        button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <form method="POST">
        <label for="username">Nom d'utilisateur:</label>
        <input type="text" name="username" required>

        <label for="email">E-mail:</label>
        <input type="email" name="email" required>

        <label for="password">Mot de passe:</label>
        <input type="password" name="password" required>

        <button type="submit">S'inscrire</button>
    </form>
    <a href="login.php">Déjà inscrit, je me connecte.</a>
</body>
</html>