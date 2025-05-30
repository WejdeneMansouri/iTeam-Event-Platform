<?php
session_start();
require_once '../config/db.php';

// Transfert automatique des événements passés vers "historique"
$today = date('Y-m-d');
$pastEventsStmt = $pdo->query("SELECT * FROM events WHERE end_date < '$today'");
$pastEvents = $pastEventsStmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($pastEvents as $pastEvent) {
    // Insertion dans historique
    $insert = $pdo->prepare("INSERT INTO historique SELECT * FROM events WHERE id = ?");
    $insert->execute([$pastEvent['id']]);

    // Suppression de la table events
    $delete = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $delete->execute([$pastEvent['id']]);
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: connexion/login.php");
    exit;
}

// Récupération des événements pour affichage rapide
$stmt = $pdo->query("SELECT * FROM events ORDER BY start_date DESC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Statistiques
$totalEvents = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$totalParticipants = $pdo->query("SELECT COUNT(*) FROM participants")->fetchColumn();
$confirmedParticipants = $pdo->query("SELECT COUNT(*) FROM participants WHERE status = 'confirmed'")->fetchColumn();
$topEventStmt = $pdo->query("
    SELECT e.title, COUNT(p.id) AS total
    FROM events e
    JOIN participants p ON e.id = p.event_id
    GROUP BY e.id
    ORDER BY total DESC
    LIMIT 1
");
$topEvent = $topEventStmt->fetch();
// Liste des événements
$events = $pdo->query("SELECT * FROM events ORDER BY start_date DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js'></script>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <style>
        /* styles CSS non modifiés */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background-color: #f4f7fc;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            display: block;
        }
        h2 { color: #333; font-size: 2rem; margin-bottom: 30px; font-weight: 600; }
        .nav { margin-bottom: 20px; display: flex; justify-content: flex-start; gap: 15px; }
        .nav a {
            text-decoration: none;
            color: #1e88e5;
            font-weight: 500;
            font-size: 1.1rem;
            transition: color 0.3s, transform 0.3s;
        }
        .nav a:hover { color: #1565c0; transform: translateY(-2px); }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            margin-top: 30px;
        }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th {
            background-color: #1e88e5;
            color: white;
            font-size: 1rem;
        }
        tr:hover { background-color: #f1f1f1; transition: background-color 0.3s; }
        .event {
            background: white;
            margin: 15px 0;
            padding: 20px;
            border-left: 5px solid #1e88e5;
            max-width: 100%;
            border-radius: 8px;
            position: relative;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .event:hover { transform: translateY(-4px); }
        .event h3 { margin: 0; font-size: 1.6rem; color: #333; }
        .event p { font-size: 1.1rem; color: #555; }
        .event small { display: block; margin-top: 10px; color: #777; }
        .event-actions { margin-top: 15px; }
        .event-actions a {
            margin-right: 15px;
            padding: 8px 15px;
            background: #1e88e5;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s, transform 0.3s;
        }
        .event-actions a:hover { background: #1565c0; transform: translateY(-2px); }
        .event-actions a.delete { background: #dc3545; }
        .event-actions a.delete:hover { background: #c82333; }
        .notifications {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .notif {
            background: #1e88e5;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .notif:hover { transform: translateY(-2px); }
    </style>
</head>
<body>

<h2>Tableau de bord de l’administrateur</h2>

<div class="nav">
    <a href="participants.php">👥 Gérer les participants</a>
    <a href="add_event.php">➕ Ajouter un événement</a>
    <a href="add_admin.php">👤 Ajouter un utilisateur</a>
    <a href="historique.php">🕓 Historique</a>

    <a href="../login.php">🔒 Déconnexion</a>
</div>

<div class="notifications">
    <div class="notif">
        <a href="/webdev/event-platform/events/list.php" style="color: white; text-decoration: none;">Événements : <?= $totalEvents ?></a>
    </div>
    <div class="notif">
        <a href="participants.php" style="color: white; text-decoration: none;">Participants : <?= $totalParticipants ?></a>
    </div>
    <div class="notif">
        <a href="participants.php?status=confirmed" style="color: white; text-decoration: none;">Confirmés : <?= $confirmedParticipants ?></a>
    </div>
</div>

<?php if (isset($_GET['view']) && $_GET['view'] === 'top' && $topEvent): ?>
    <div class="event">
        <h3>Top événement : <?= htmlspecialchars($topEvent['title']) ?></h3>
        <p>Nombre total de participants confirmés : <?= $topEvent['total'] ?></p>
    </div>
<?php endif; ?>

<?php if (count($events) > 0): ?>
    <?php foreach ($events as $event): ?>
        <div class="event">
            <h3><?= htmlspecialchars($event['title']) ?></h3>
            <p><?= htmlspecialchars($event['description']) ?></p>
            <small><?= htmlspecialchars($event['start_date']) ?> → <?= htmlspecialchars($event['end_date']) ?></small>
            <div class="event-actions">
                <a href="event_details.php?id=<?= $event['id'] ?>">Détails</a>
                <a href="delete_event.php?id=<?= $event['id'] ?>" class="delete" onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Aucun événement enregistré.</p>
<?php endif; ?>

</body>
</html>
