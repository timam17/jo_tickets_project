<?php 
session_start(); 

$user = $_SESSION['user'] ?? null;
$user_id = $_SESSION['user_id'] ?? null; 

if (isset($_SESSION['token'])) {
    $token = $_SESSION['token'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Site</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background-color:rgb(26, 27, 29);
        }

        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030; /* Assurez-vous que le navbar est au-dessus du contenu */
        }

        .navbar-brand img {
            width: 100px;
            height: auto;
            object-fit: contain;
        }

        .custom-btn-night {
            background-color:rgba(13, 27, 42, 0); /* bleu nuit foncé */
            border: none;
            transition: background-color 0.3s ease;
        }

        .custom-btn-night:hover {
            background-color:rgb(13, 27, 42); /* un peu plus clair au hover */
        }

        .custom-btn-night:active {
            background-color: rgb(13, 27, 42); /* rouge lors du clic */
        }

        @media (max-width: 767px) {
            .navbar-brand img {
                width: 80px;
            }

            .navbar-nav {
                text-align: center;
            }

            .navbar-nav .nav-item {
                margin-bottom: 10px;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand img {
                width: 70px;
            }
        }
    </style>
</head>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="https://www.selestat.fr/fileadmin/_processed_/4/f/csm_Paris-2024-Logo_ccfe1e7b67.png" alt="logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if ($user): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Bienvenue, <?= htmlspecialchars($user) ?> !</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn custom-btn-night text-white ms-2" href="../frontend/mes_tickets.php">Mes tickets</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-danger text-white ms-2" href="../include/logout.php">Déconnexion</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Connectez-vous pour accéder aux informations</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
