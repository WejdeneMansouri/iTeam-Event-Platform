<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$events = $pdo->query("SELECT * FROM historique ORDER BY end_date DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des événements</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f7fc;
            padding: 20px;
            color: #333;
        }
        h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #1e88e5;
        }
        .nav {
            margin-bottom: 20px;
        }
        .nav a {
            margin-right: 15px;
            text-decoration: none;
            color: #1e88e5;
            font-weight: bold;
        }
        .event {
            background: white;
            margin-bottom: 20px;
            padding: 20px;
            border-left: 5px solid #9e9e9e;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        .event h3 {
            margin: 0;
            font-size: 1.4rem;
        }
        .event small {
            color: #888;
        }
        .buttons {
            margin-top: 10px;
        }
        .buttons a {
            margin-right: 10px;
            text-decoration: none;
            background: #1e88e5;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        .buttons a.modify {
            background-color: #43a047;
        }
        .buttons a:hover {
            opacity: 0.85;
        }
    </style>
</head>
<body>

<h2>📜 Historique des événements archivés</h2>

<div class="nav">
    <a href="dashboard.php">⬅ Retour au tableau de bord</a>
</div>

<?php if (count($events) > 0): ?>
    <?php foreach ($events as $event): ?>
        <div class="event">
            <h3><?= htmlspecialchars($event['title']) ?></h3>
            <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
            <small>📅 Du <?= htmlspecialchars($event['start_date']) ?> au <?= htmlspecialchars($event['end_date']) ?></small>

            <div class="buttons">
                <a class="modify" href="relancer_event.php?id=<?= $event['id'] ?>">🔄 Modifier & Relancer</a>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Aucun événement archivé pour le moment.</p>
<?php endif; ?>

</body>
</html>
