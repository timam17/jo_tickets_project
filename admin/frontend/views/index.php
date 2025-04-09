<?php
include '../include/header.php';
include '../../backend/db/db.php'; // Connexion à la base de données

// Vérifier la connexion à la base de données
if (!$pdo) {
    die("Échec de la connexion à la base de données.");
}

// Récupérer la liste des matchs avec les détails des équipes et des stades
$sql = "SELECT e.id, e.start, s.name AS stadium, s.location, 
               t1.id AS home_team_id, t1.name AS home_team, 
               t2.id AS away_team_id, t2.name AS away_team, 
               e.score, t3.name AS winner
        FROM mainapp_event e
        LEFT JOIN mainapp_stadium s ON e.stadium_id = s.id
        LEFT JOIN mainapp_team t1 ON e.team_home_id = t1.id
        LEFT JOIN mainapp_team t2 ON e.team_away_id = t2.id
        LEFT JOIN mainapp_team t3 ON e.winner_id = t3.id
        ORDER BY e.start ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mise à jour des résultats
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $match_id = $_POST['match_id'];
    $score = $_POST['score'];
    $winner_id = $_POST['winner'];

    // Mise à jour des informations du match
    $update_sql = "UPDATE mainapp_event SET score = :score, winner_id = :winner WHERE id = :match_id";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->bindParam(':score', $score, PDO::PARAM_STR);
    $update_stmt->bindParam(':winner', $winner_id, PDO::PARAM_INT);
    $update_stmt->bindParam(':match_id', $match_id, PDO::PARAM_INT);
    
    if ($update_stmt->execute()) {
        header("Location: index.php"); // Recharger la page après la mise à jour
        exit();
    } else {
        echo "Erreur lors de la mise à jour du match.";
    }
}

// Récupérer la liste des équipes pour le menu déroulant
$team_sql = "SELECT id, name FROM mainapp_team";
$team_stmt = $pdo->prepare($team_sql);
$team_stmt->execute();
$teams = $team_stmt->fetchAll(PDO::FETCH_ASSOC);


function getMatchClass($date) {
    $quart_dates = ['2024-07-31', '2024-08-01'];
    $demi_dates = ['2024-08-04'];
    $finale_date = '2024-08-07';

    $match_date = date("Y-m-d", strtotime($date));

    if (in_array($match_date, $quart_dates)) {
        return "quart";
    } elseif (in_array($match_date, $demi_dates)) {
        return "demi";
    } elseif ($match_date === $finale_date) {
        return "finale";
    }
    return "";
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration des matchs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f4f4f4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        th {
            background: #007BFF;
            color: white;
        }
        form {
            display: inline-block;
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }

        .quart {
            background-color:rgba(231, 231, 231, 0.58);
            font-weight: bold;
        }

        .demi {
            background-color:rgba(219, 210, 190, 0.52);
            font-weight: bold;
        }

        .finale {
            background-color:rgba(255, 216, 157, 0.64);
            font-weight: bold;
            font-size: 1.1em;
        }

        .actions {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }

        .actions input, 
        .actions select, 
        .actions button {
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .actions button {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        .actions button:hover {
            background-color: #218838;
        }


    </style>
</head>
<body>

<table>
    <tr>
        <th>Date & Heure</th>
        <th>Stade</th>
        <th>Équipe Domicile</th>
        <th>Équipe Extérieure</th>
        <th>Score</th>
        <th>Gagnant</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($matches as $match): ?>
    <tr class="<?= getMatchClass($match['start']) ?>">
        <td><?= date("d/m/Y H:i", strtotime($match['start'])) ?></td>
        <td><?= $match['stadium'] ?> (<?= $match['location'] ?>)</td>
        <td><?= $match['home_team'] ?? "?" ?></td>
        <td><?= $match['away_team'] ?? "?" ?></td>
        <td><?= $match['score'] ?? "?" ?></td>
        <td><?= $match['winner'] ?? "?" ?></td>
        <td class="actions">
            <form method="POST" class="actions">
                <input type="hidden" name="match_id" value="<?= $match['id'] ?>">
                <input type="text" name="score" placeholder="Ex: 2-1" required>
                
                <!-- Sélectionner le gagnant parmi les équipes de ce match -->
                <select name="winner" required>
                    <option value="">Choisir le gagnant</option>
                    <option value="<?= $match['home_team_id'] ?>"><?= $match['home_team'] ?></option>
                    <option value="<?= $match['away_team_id'] ?>"><?= $match['away_team'] ?></option>
                </select>
                
                <button type="submit">Mettre à jour</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
