<?php
session_start(); // Démarrer la session

include '../../backend/db/db.php'; // Assurez-vous que le chemin est correct

// Vérifier si $pdo est défini et est un objet PDO valide
if (!$pdo) {
    die("Échec de la connexion à la base de données.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_or_email = $_POST['username_or_email']; // Utiliser un seul champ pour le nom d'utilisateur ou l'email
    $password = $_POST['password'];

    try {
        // Vérifier si l'utilisateur existe avec le nom d'utilisateur ou l'email
        $sql = "SELECT * FROM auth_user WHERE username = :username_or_email OR email = :username_or_email";
        $stmt = $pdo->prepare($sql); // Utiliser $pdo ici au lieu de $conn
        $stmt->bindParam(':username_or_email', $username_or_email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Utilisateur trouvé
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            // Vérifier si le mot de passe correspond
            if (password_verify($password, $user['password'])) {
                // Le mot de passe est correct, démarrer la session
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];

                // Rediriger vers la page d'accueil ou dashboard
                header("Location: index.php");
                exit(); // Terminer l'exécution du script après la redirection
            } else {
                echo "Le mot de passe est incorrect.";
            }
        } else {
            echo "Aucun utilisateur trouvé avec ce nom d'utilisateur ou email.";
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
    <title>Connexion</title>
    <style>
        a {
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
        <label for="username_or_email">Nom d'utilisateur ou E-mail:</label>
        <input type="text" name="username_or_email" required>

        <label for="password">Mot de passe:</label>
        <input type="password" name="password" required>

        <button type="submit">Se connecter</button>
    </form>
    <a href="register.php">Pas encore inscrit, je m'inscris.</a>
</body>
</html>
