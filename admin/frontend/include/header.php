<?php
session_start();

// Vérification de la session utilisateur
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirige vers la page de connexion si non connecté
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #007BFF;
            padding: 15px;
            color: white;
            font-size: 18px;
        }
        .username {
            font-weight: bold;
        }
        .logout-button {
            background-color: #D9534F; /* Couleur rouge légèrement modifiée */
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            text-decoration: none;
            border-radius: 5px;
        }
        .logout-button:hover {
            background-color: #C9302C; /* Rouge plus foncé au hover */
        }
    </style>
</head>
<body>

<header class="header">
    <span class="username">Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?> !</span>
    <a href="../include/logout.php" class="logout-button">Déconnexion</a>
</header>

</body>
</html>
