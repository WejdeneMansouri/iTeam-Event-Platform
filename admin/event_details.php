<?php
// admin/event_details.php
require_once '../config/db.php';

if (!isset($_GET['id'])) {
    echo "Événement non spécifié.";
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch();

if (!$event) {
    echo "Événement introuvable.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Détails de l’événement</title>
    <style>
        body {
            font-family: Arial;
            background: #f9f9f9;
            padding: 40px;
        }
        .container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: auto;
        }
        h2 {
            color: #333;
        }
        p {
            margin: 8px 0;
        }
        .actions {
            margin-top: 30px;
        }
        .btn {
            padding: 10px 20px;
            margin-right: 10px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
        }
        .edit {
            background-color: #007bff;
        }
        .delete {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><?= htmlspecialchars($event['title']) ?></h2>
        <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($event['description'])) ?></p>
        <p><strong>Lieu:</strong> <?= htmlspecialchars($event['location']) ?></p>
        <p><strong>Date de début:</strong> <?= $event['start_date'] ?></p>
        <p><strong>Date de fin:</strong> <?= $event['end_date'] ?></p>
        <p><strong>Type:</strong> <?= $event['type'] ?></p>
        <p><strong>Participants max:</strong> <?= $event['max_participants'] ?></p>

        <div class="actions">
            <a class="btn edit" href="edit_event.php?id=<?= $event['id'] ?>">Modifier</a>
            <a class="btn delete" href="dashboard.php?delete=<?= $event['id'] ?>" onclick="return confirm('Supprimer cet événement ?')">Supprimer</a>
        </div>
    </div>
</body>
</html>
