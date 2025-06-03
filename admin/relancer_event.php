<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: historique.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM historique WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch();

if (!$event) {
    echo "Événement introuvable.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $places = $_POST['places'];


    $insert = $pdo->prepare("INSERT INTO events (title, description, start_date, end_date, max_participants) VALUES (?, ?, ?, ?, ?)");
    $insert->execute([$title, $description, $start, $end, $places]);

    
    $delete = $pdo->prepare("DELETE FROM historique WHERE id = ?");
    $delete->execute([$id]);

    header("Location: dashboard.php?msg=relance_success");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier & Relancer</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
            background-color: #f4f7fc;
        }
        form {
            background: white;
            padding: 20px;
            max-width: 600px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        h2 {
            color: #1e88e5;
            text-align: center;
        }
        input, textarea {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #43a047;
            color: white;
            padding: 10px 16px;
            border: none;
            font-size: 1rem;
            border-radius: 4px;
            cursor: pointer;
        }
        a.back {
            display: inline-block;
            margin-top: 10px;
            color: #1e88e5;
            text-decoration: none;
        }
    </style>
</head>
<body>

<h2>🔄 Modifier et relancer l'événement</h2>

<form method="POST">
    <input type="text" name="title" value="<?= htmlspecialchars($event['title']) ?>" required>
    <textarea name="description" rows="5" required><?= htmlspecialchars($event['description']) ?></textarea>
    <input type="date" name="start_date" value="<?= htmlspecialchars($event['start_date']) ?>" required>
    <input type="date" name="end_date" value="<?= htmlspecialchars($event['end_date']) ?>" required>
    <input type="number" name="places" value="<?= htmlspecialchars($event['max_places']) ?>" required>
    <button type="submit">✅ Relancer</button>
</form>

<p style="text-align:center;"><a href="historique.php" class="back">⬅ Retour à l’historique</a></p>

</body>
</html>
