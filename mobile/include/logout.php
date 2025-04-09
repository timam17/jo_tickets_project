<?php
session_start();
session_destroy(); // Supprime toutes les données de session
header("Location: ../frontend/register.php"); // Redirige vers la page de connexion
exit();
?>
