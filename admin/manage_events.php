<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Ajout ou modification d’un événement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $type = $_POST['type'];
    $max_participants = intval($_POST['max_participants']);
    $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;

    if ($event_id > 0) {
        $stmt = $pdo->prepare("UPDATE events SET title=?, description=?, location=?, start_date=?, end_date=?, type=?, max_participants=? WHERE id=?");
        $stmt->execute([$title, $description, $location, $start_date, $end_date, $type, $max_participants, $event_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO events (title, description, location, start_date, end_date, type, max_participants) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $location, $start_date, $end_date, $type, $max_participants]);
    }
    header("Location: manage_events.php");
    exit;
}

// Suppression
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $pdo->prepare("DELETE FROM events WHERE id=?")->execute([$id]);
    header("Location: manage_events.php");
    exit;
}

// Modification (chargement d’un événement existant)
$editing_event = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id=?");
    $stmt->execute([$id]);
    $editing_event = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Tous les événements
$events = $pdo->query("SELECT * FROM events ORDER BY start_date DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des événements</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f0f2f5; }
        h2 { color: #333; }
        form { background: #fff; padding: 20px; margin-bottom: 30px; border-radius: 6px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input, textarea, select { width: 100%; padding: 8px; margin: 8px 0 16px; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #007BFF; color: white; }
        .btn { padding: 6px 12px; border: none; border-radius: 4px; color: white; text-decoration: none; }
        .edit { background-color: #17a2b8; }
        .delete { background-color: #dc3545; }
        .submit-btn { background-color: #28a745; }
        .nav { margin-bottom: 15px; }
    </style>
</head>
<body>

    <div class="nav">
        <a href="dashboard.php">← Retour au Dashboard</a>
    </div>

    <h2><?= $editing_event ? 'Modifier un événement' : 'Ajouter un nouvel événement' ?></h2>
    <form method="post">
        <input type="hidden" name="event_id" value="<?= $editing_event['id'] ?? '' ?>">
        <label>Titre</label>
        <input type="text" name="title" required value="<?= $editing_event['title'] ?? '' ?>">
        
        <label>Description</label>
        <textarea name="description"><?= $editing_event['description'] ?? '' ?></textarea>
        
        <label>Lieu</label>
        <input type="text" name="location" value="<?= $editing_event['location'] ?? '' ?>">
        
        <label>Date de début</label>
        <input type="datetime-local" name="start_date" required value="<?= isset($editing_event['start_date']) ? date('Y-m-d\TH:i', strtotime($editing_event['start_date'])) : '' ?>">
        
        <label>Date de fin</label>
        <input type="datetime-local" name="end_date" required value="<?= isset($editing_event['end_date']) ? date('Y-m-d\TH:i', strtotime($editing_event['end_date'])) : '' ?>">
        
        <label>Type</label>
        <select name="type">
            <option value="job_fair" <?= ($editing_event['type'] ?? '') === 'job_fair' ? 'selected' : '' ?>>Job Fair</option>
            <option value="workshop" <?= ($editing_event['type'] ?? '') === 'workshop' ? 'selected' : '' ?>>Workshop</option>
            <option value="training" <?= ($editing_event['type'] ?? '') === 'training' ? 'selected' : '' ?>>Training</option>
            <option value="meeting" <?= ($editing_event['type'] ?? '') === 'meeting' ? 'selected' : '' ?>>Meeting</option>
            <option value="other" <?= ($editing_event['type'] ?? '') === 'other' ? 'selected' : '' ?>>Autre</option>
        </select>

        <label>Nombre max de participants</label>
        <input type="number" name="max_participants" value="<?= $editing_event['max_participants'] ?? 0 ?>">

        <button class="btn submit-btn" type="submit">Enregistrer</button>
    </form>

    <h2>Événements existants</h2>
    <table>
        <tr>
            <th>Titre</th>
            <th>Lieu</th>
            <th>Date</th>
            <th>Type</th>
            <th>Participants</th>
            <th>Action</th>
        </tr>
        <?php foreach ($events as $event): ?>
            <tr>
                <td><?= htmlspecialchars($event['title']) ?></td>
                <td><?= htmlspecialchars($event['location']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($event['start_date'])) ?></td>
                <td><?= $event['type'] ?></td>
                <td><?= $event['max_participants'] ?></td>
                <td>
                    <a class="btn edit" href="?edit=<?= $event['id'] ?>">Modifier</a>
                    <a class="btn delete" href="?delete=<?= $event['id'] ?>" onclick="return confirm('Supprimer cet événement ?')">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>
