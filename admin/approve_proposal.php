<?php
require_once '../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: proposed_events.php");
    exit;
}

$proposal_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM proposed_events WHERE id = :id");
$stmt->execute(['id' => $proposal_id]);
$proposal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$proposal) {
    header("Location: proposed_events.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $insert = $pdo->prepare("INSERT INTO events (title, description, start_date, end_date, image_path)
                             VALUES (:title, :description, :start_date, :end_date, :image_path)");
    $insert->execute([
        'title' => $proposal['title'],
        'description' => $proposal['description'],
        'start_date' => $proposal['start_date'],
        'end_date' => $proposal['end_date'],
        'image_path' => $proposal['image_path'] ?? null,
    ]);

    $delete = $pdo->prepare("DELETE FROM proposed_events WHERE id = :id");
    $delete->execute(['id' => $proposal_id]);

    header("Location: ../student/list.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation d'événement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 30px;
        }
        h2 {
            color: #333;
        }
        .event-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: auto;
        }
        .event-container p {
            margin: 10px 0;
        }
        .event-container img {
            margin-top: 10px;
            border-radius: 8px;
        }
        .actions {
            margin-top: 20px;
        }
        .actions button, .actions a {
            padding: 10px 15px;
            background: #007BFF;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 6px;
            margin-right: 10px;
            cursor: pointer;
        }
        .actions a {
            background: #6c757d;
        }
    </style>
</head>
<body>
    <div class="event-container">
        <h2>Valider la proposition d'événement</h2>
        <p><strong>Titre :</strong> <?= htmlspecialchars($proposal['title']) ?></p>
        <p><strong>Description :</strong> <?= htmlspecialchars($proposal['description']) ?></p>
        <p><strong>Date de début :</strong> <?= htmlspecialchars($proposal['start_date']) ?></p>
        <p><strong>Date de fin :</strong> <?= htmlspecialchars($proposal['end_date']) ?></p>
        <?php if ($proposal['image_path']) : ?>
            <p><img src="<?= htmlspecialchars($proposal['image_path']) ?>" width="200"></p>
        <?php endif; ?>

        <form method="post" class="actions">
            <input type="hidden" name="id" value="<?= $proposal['id'] ?>">
            <button type="submit">Ajouter à la liste des événements</button>
            <a href="proposed_events.php">Retour</a>
        </form>
    </div>
</body>
</html>
