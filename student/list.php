<?php
require_once '../config/db.php';

$stmt = $pdo->prepare("
    SELECT e.*,
           (SELECT COUNT(*) FROM participants p WHERE p.event_id = e.id AND p.status IN ('pending', 'approved')) AS current_participants
    FROM events e
    WHERE e.end_date > NOW()
");
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

$events = array_filter($events, function($event) {
    return $event['current_participants'] < $event['max_participants'];
});
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des événements</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 800px;
            margin: 40px auto;
            background-color: #fff;
            padding: 25px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        h1 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 30px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            background-color: #f1f1f1;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 5px;
        }

        li strong {
            font-size: 18px;
            color: #333;
        }

        a {
            display: inline-block;
            margin-top: 10px;
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .no-events {
            text-align: center;
            color: #555;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Événements disponibles</h1>

        <?php if (empty($events)) : ?>
            <p class="no-events">Aucun événement disponible actuellement.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($events as $event): ?>
                    <li>
                        <strong><?= htmlspecialchars($event['title']) ?></strong><br>
                        <?= htmlspecialchars($event['start_date']) ?> à <?= htmlspecialchars($event['location']) ?><br>
                        <a href="register.php?event_id=<?= $event['id'] ?>">S'inscrire</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
     <a href="home.php">← Retour home</a>
</body>
</html>
