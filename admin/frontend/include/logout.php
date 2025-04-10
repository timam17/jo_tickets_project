<?php
session_start();
if (isset($_SESSION)) {
    session_unset(); // Libère toutes les variables de session
    session_destroy(); // Termine la session
}
header("Location: ../views/login.php"); // Redirige vers la page de connexion
exit();
?>

