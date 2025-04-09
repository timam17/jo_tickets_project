<?php
$host = '127.0.0.1';
$dbname = 'jo_project_starter';
$username = 'root';
$password = '';


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    echo "Échec de la connexion à la base de données: " . $e->getMessage();
}
?>