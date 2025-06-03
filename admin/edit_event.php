<?php
require_once '../config/db.php';

if (!isset($_GET['id'])) {
    echo "ID d'événement manquant.";
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE events SET title = ?, description = ?, location = ?, start_date = ?, end_date = ?, type = ?, max_participants = ? WHERE id = ?");
    $stmt->execute([
        $_POST['title'],
        $_POST['description'],
        $_POST['location'],
        $_POST['start_date'],
        $_POST['end_date'],
        $_POST['type'],
        $_POST['max_participants'],
        $id
    ]);
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modifier l’événement</title>
    <style>
        body {
            font-family: Arial;
            background: #f0f0f0;
            padding: 40px;
        }
        form {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
        }
        input, textarea, select {
            display: block;
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
    </style>
</head>
<body>

<form method="POST">
    <h2>Modifier l’événement</h2>
    <input type="text" name="title" value="<?= htmlspecialchars($event['title']) ?>" required>
    <textarea name="description"><?= htmlspecialchars($event['description']) ?></textarea>
    <input type="text" name="location" value="<?= htmlspecialchars($event['location']) ?>" required>
    <input type="datetime-local" name="start_date" value="<?= str_replace(' ', 'T', $event['start_date']) ?>" required>
    <input type="datetime-local" name="end_date" value="<?= str_replace(' ', 'T', $event['end_date']) ?>" required>
    <select name="type">
        <?php
        $types = ['job_fair', 'workshop', 'training', 'meeting', 'other'];
        foreach ($types as $type) {
            $selected = ($event['type'] === $type) ? 'selected' : '';
            echo "<option value=\"$type\" $selected>$type</option>";
        }
        ?>
    </select>
    <input type="number" name="max_participants" value="<?= $event['max_participants'] ?>">
    <button type="submit">Enregistrer les modifications</button>
</form>

</body>
</html>
