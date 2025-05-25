<?php
require_once '../config/db.php';

// Requête pour récupérer les participants avec les titres des événements
$sql = "SELECT p.full_name, p.email, p.profile, p.registered_at, e.title AS event_title
        FROM participants p
        JOIN events e ON p.event_id = e.id
        ORDER BY p.registered_at DESC";

$stmt = $pdo->query($sql);
$participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f4f7;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: 40px auto;
            background-color: #fff;
            padding: 25px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        h1 {
            text-align: center;
            color: #007BFF;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        table th, table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #007BFF;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .no-data {
            text-align: center;
            color: #777;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Participants inscrits</h1>

        <?php if (count($participants) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nom complet</th>
                        <th>Email</th>
                        <th>Profil</th>
                        <th>Événement</th>
                        <th>Date d'inscription</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($participants as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['full_name']) ?></td>
                            <td><?= htmlspecialchars($p['email']) ?></td>
                            <td><?= htmlspecialchars($p['profile']) ?></td>
                            <td><?= htmlspecialchars($p['event_title']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($p['registered_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">Aucun participant pour le moment.</p>
        <?php endif; ?>
    </div>
</body>
</html>
