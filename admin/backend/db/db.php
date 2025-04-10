<?php
// Informations de connexion à la base de données
$host = '127.0.0.1';
$dbname = 'jo_project_starter';
$username = 'root';
$password = '';

// Utilisation d'un tableau pour stocker les options de connexion pour plus de sécurité
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Pour gérer les erreurs avec des exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Pour récupérer les résultats sous forme de tableau associatif
    PDO::ATTR_EMULATE_PREPARES => false,  // Désactivation de l'émulation des requêtes préparées pour une meilleure sécurité
];

try {
    // Tentative de connexion à la base de données avec les options
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, $options);
    
    // Connexion réussie
    //echo "Connexion réussie à la base de données.";
} catch (PDOException $e) {
    // En cas d'erreur de connexion
    echo "Échec de la connexion à la base de données: " . $e->getMessage();
    exit();  // Arrêt de l'exécution si la connexion échoue
}
?>
