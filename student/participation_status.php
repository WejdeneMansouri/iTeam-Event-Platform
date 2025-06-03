<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: ../login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

$stmt = $pdo->prepare("
    SELECT e.title, e.start_date, e.end_date, e.location, p.status
    FROM participants p
    JOIN events e ON p.event_id = e.id
    WHERE p.student_id = ?
    ORDER BY e.start_date DESC
");
$stmt->execute([$student_id]);
$participations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>État de participation</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7fc; padding: 20px; }
        h2 { color: #007BFF; }
        .nav a {
            margin-right: 15px;
            text-decoration: none;
            color: #1e88e5;
            font-weight: 500;
        }
        .table {
            width: 100%;
            margin-top: 20px;
            background: white;
            border-collapse: collapse;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .table th, .table td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        .table th {
            background-color: #1e88e5;
            color: white;
        }
    </style>
</head>
<body>

<h2>📝 État de vos participations</h2>

<div class="nav">
    <a href="home.php">← Retour au tableau de bord</a>
</div>

<?php if (count($participations) > 0): ?>
    <table class="table">
        <tr>
            <th>Événement</th>
            <th>Date</th>
            <th>Lieu</th>
            <th>Statut</th>
        </tr>
        <?php foreach ($participations as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['title']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($p['start_date'])) ?> - <?= date('d/m/Y H:i', strtotime($p['end_date'])) ?></td>
                <td><?= htmlspecialchars($p['location']) ?></td>
                <td><?= ucfirst($p['status']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Vous n'avez encore participé à aucun événement.</p>
<?php endif; ?>
</body>
</html>
