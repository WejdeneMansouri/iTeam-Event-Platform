<?php 
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

$stmt = $pdo->prepare("SELECT full_name, photo_path FROM students WHERE id = :student_id");
$stmt->execute(['student_id' => $student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    header("Location: login.php");
    exit;
}

$events = $pdo->query("SELECT * FROM events ORDER BY start_date DESC")->fetchAll(PDO::FETCH_ASSOC);

$preInscriptionsStmt = $pdo->prepare("SELECT event_id, status FROM participants WHERE student_id = :student_id");
$preInscriptionsStmt->execute(['student_id' => $student_id]);
$preInscriptions = $preInscriptionsStmt->fetchAll(PDO::FETCH_KEY_PAIR);



?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Étudiant</title>
    <style>
        * {
            margin: 0; padding: 0; box-sizing: border-box;
        }
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background-color: #f4f7fc;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        h2 {
            color: #333;
            font-size: 2rem;
            margin-bottom: 30px;
            font-weight: 600;
        }
        .nav {
            margin-bottom: 20px;
            display: flex;
            justify-content: flex-start;
            gap: 15px;
        }
        .nav a {
            text-decoration: none;
            color: #1e88e5;
            font-weight: 500;
            font-size: 1.1rem;
            transition: color 0.3s, transform 0.3s;
        }
        .nav a:hover {
            color: #1565c0;
            transform: translateY(-2px);
        }
        .event {
            background: white;
            margin: 15px 0;
            padding: 20px;
            border-left: 5px solid #1e88e5;
            border-radius: 8px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            max-width: 100%;
            position: relative;
        }
        .event:hover {
            transform: translateY(-4px);
        }
        .event h3 {
            margin-bottom: 8px;
            font-size: 1.6rem;
            color: #333;
        }
        .event p {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 6px;
        }
        .event small {
            color: #777;
        }
        .status {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #1e88e5;
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: capitalize;
        }
    </style>
</head>
<body>



<div style="display: flex; align-items: center; gap: 15px; margin-bottom: 30px;">
    <?php if (!empty($student['photo_path']) && file_exists(__DIR__ . '/../' . $student['photo_path'])): ?>
        <img src="../<?= htmlspecialchars($student['photo_path']) ?>" alt="Photo de <?= htmlspecialchars($student['full_name']) ?>" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid #1e88e5;">
    <?php else: ?>
        <div style="width: 80px; height: 80px; border-radius: 50%; background: #ccc;"></div>
    <?php endif; ?>
    <h2 style="font-weight: 600; font-size: 1.8rem; color: #333;">
        Bonjour <?= htmlspecialchars($student['full_name']) ?>
    </h2>
</div>


<div class="nav">
    <a href="profile.php">👤 Mon Profil</a>
    <a href="list.php">📅 Gérer les événements</a>
    <a href="participation_status.php">📝 État de participation</a>
    <a href="propose_event.php">➕ Proposer un événement</a>

    <a href="../logout.php">🔒 Déconnexion</a>
</div>


<?php if (count($events) > 0): ?>
    <?php foreach ($events as $event): ?>
        <div class="event">
            <h3><?= htmlspecialchars($event['title']) ?></h3>
            <p><?= htmlspecialchars($event['description']) ?></p>
            <small><?= htmlspecialchars($event['start_date']) ?> → <?= htmlspecialchars($event['end_date']) ?></small>
            <?php if (isset($preInscriptions[$event['id']])): ?>
                <div class="status">Statut : <?= htmlspecialchars($preInscriptions[$event['id']]) ?></div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Aucun événement disponible pour le moment.</p>
<?php endif; ?>

</html>
