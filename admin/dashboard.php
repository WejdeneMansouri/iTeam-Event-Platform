<?php

session_start();
require_once '../config/db.php';

$today = date('Y-m-d');
$pastEventsStmt = $pdo->query("SELECT * FROM events WHERE end_date < '$today'");
$pastEvents = $pastEventsStmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($pastEvents as $pastEvent) {
    $insert = $pdo->prepare("INSERT INTO historique SELECT * FROM events WHERE id = ?");
    $insert->execute([$pastEvent['id']]);

    $delete = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $delete->execute([$pastEvent['id']]);
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
$adminId = $_SESSION['admin_id'];
$stmt = $pdo->prepare("SELECT username, photo_path FROM admins WHERE id = ?");
$stmt->execute([$adminId]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$username = $admin ? htmlspecialchars($admin['username']) : 'Administrateur';
$photoPath = $admin && !empty($admin['photo_path']) ? $admin['photo_path'] : '';




$stmt = $pdo->query("SELECT * FROM events ORDER BY start_date DESC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalEvents = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$totalParticipants = $pdo->query("SELECT COUNT(*) FROM participants")->fetchColumn();
$proposedEventsCount = $pdo->query("SELECT COUNT(*) FROM proposed_events")->fetchColumn();
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
$events = $pdo->query("SELECT * FROM events ORDER BY start_date DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <style>
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
        #calendar {
            margin-top: 40px;
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
        }
        .modal {
    display: none;
    position: fixed;
    top: 10%;
    left: 50%;
    transform: translateX(-50%);
    background-color: #fff;
    padding: 20px 30px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    z-index: 1000;
    max-height: 80vh;
    overflow-y: auto;
    max-width: 500px;
    width: 90%;
    font-family: Arial, sans-serif;
}

.modal h3 {
    margin-top: 0;
    margin-bottom: 15px;
    font-size: 20px;
    text-align: center;
    color: #333;
}

.modal label {
    display: block;
    margin-top: 10px;
    font-weight: bold;
    color: #444;
}

.modal input[type="text"],
.modal input[type="number"],
.modal input[type="datetime-local"],
.modal textarea,
.modal select {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    box-sizing: border-box;
}

.modal textarea {
    resize: vertical;
    min-height: 60px;
}

.modal-buttons {
    margin-top: 20px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    flex-wrap: wrap;
}

.modal-buttons button {
    padding: 8px 14px;
    font-size: 14px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.modal-buttons button[type="submit"] {
    background-color: #28a745;
    color: white;
}

.modal-buttons button[type="submit"]:hover {
    background-color: #218838;
}

.modal-buttons button[type="button"] {
    background-color: #dc3545;
    color: white;
}

.modal-buttons button[type="button"]:hover {
    background-color: #c82333;
}


@media (max-width: 500px) {
    .modal {
        width: 95%;
        padding: 15px;
        max-height: 90vh;
    }

    .modal-buttons {
        flex-direction: column;
        align-items: stretch;
    }

    .modal-buttons button {
        width: 100%;
    }
}

    </style>
</head>
<body>

<div style="display: flex; align-items: center; gap: 15px; margin-bottom: 30px;">
    <?php if ($photoPath && file_exists(__DIR__ . '/../' . $photoPath)): ?>
        <img src="../<?= htmlspecialchars($photoPath) ?>" alt="Photo de <?= $username ?>" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid #1e88e5;">
    <?php else: ?>
        <div style="width: 80px; height: 80px; border-radius: 50%; background: #ccc;"></div>
    <?php endif; ?>
    <h2 style="margin: 0;">Bonjour <?= $username ?></h2>
</div>


<div class="nav">
    <a href="participants.php">👥 Gérer les participants</a>
    <a href="add_event.php">➕ Ajouter un événement</a>
    <a href="add_admin.php">👤 Ajouter un utilisateur</a>
    <a href="historique.php">📜 Historique des événements</a>
    <a href="proposed_events.php" >📩 Gérer les propositions</a><br>

    <a href="../login.php">🔒 Déconnexion</a>
</div>

<div class="notifications">
    
    <div class="notif">
        <a href="participants.php" style="color: white; text-decoration: none;">Participants : <?= $totalParticipants ?></a>
    </div>

    <div class="notif">
        <a href="proposed_events.php" style="color: white; text-decoration: none;">Propositions : <?= $proposedEventsCount ?></a>
    </div>
</div>


<?php if (isset($_GET['view']) && $_GET['view'] === 'top' && $topEvent): ?>
    <div class="event">
        <h3>Top événement : <?= htmlspecialchars($topEvent['title']) ?></h3>
        <p>Nombre total de participants confirmés : <?= $topEvent['total'] ?></p>
    </div>
<?php endif; ?>



<div id='calendar'></div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 600,
        events: [
            <?php foreach ($events as $event): ?>
            {
                title: "<?= htmlspecialchars($event['title']) ?>",
                start: "<?= $event['start_date'] ?>",
                end: "<?= date('Y-m-d', strtotime($event['end_date'] . ' +1 day')) ?>",
                url: "event_details.php?id=<?= $event['id'] ?>"
            },
            <?php endforeach; ?>
        ],
        dateClick: function(info) {
            document.getElementById('eventFormModal').style.display = 'block';
            document.getElementById('eventStartDate').value = info.dateStr;
            document.getElementById('eventEndDate').value = info.dateStr;
            document.getElementById('eventTitle').focus();

        }
    });
    calendar.render();
});

</script>
<div id="eventFormModal" class="modal">
    <h3>Ajouter un événement</h3>
    <form id="eventForm" method="POST" action="add_event.php">
        <label>Titre:</label>
        <input type="text" name="title" required>

        <label>Description:</label>
        <textarea name="description" required></textarea>

        <label>Lieu:</label>
        <input type="text" name="location" required>

        <label>Date début:</label>
        <input type="datetime-local" name="start_date" required>

        <label>Date fin:</label>
        <input type="datetime-local" name="end_date" required>

        <label>Type:</label>
        <select name="type">
            <option value="job_fair">Job Fair</option>
            <option value="workshop">Workshop</option>
            <option value="training">Training</option>
            <option value="meeting">Meeting</option>
            <option value="other">Other</option>
        </select>

        <label>Nombre max. de participants:</label>
        <input type="number" name="max_participants" required>

        <div class="modal-buttons">
            <button type="submit">Ajouter</button>
            <button type="button" onclick="document.getElementById('eventFormModal').style.display='none'">Annuler</button>
        </div>
    </form>
</div>


</body>
</html>
