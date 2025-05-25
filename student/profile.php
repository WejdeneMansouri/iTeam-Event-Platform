<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: ../login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

// Traitement de la mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $organization = $_POST['organization'] ?? '';

    $stmt = $pdo->prepare("UPDATE students SET full_name = ?, email = ?, phone = ?, organization = ? WHERE id = ?");
    $stmt->execute([$full_name, $email, $phone, $organization, $student_id]);

    $_SESSION['student_name'] = $full_name; // mise à jour de la session
}

// Récupération des données de l'étudiant
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

// Récupération de l'historique complet
$eventsStmt = $pdo->prepare("
    SELECT e.*, p.status
    FROM participants p
    JOIN events e ON p.event_id = e.id
    WHERE p.student_id = ?
    ORDER BY e.start_date DESC
");
$eventsStmt->execute([$student_id]);
$allEvents = $eventsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil Étudiant</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7fc; padding: 20px; color: #333; }
        h2 { margin-bottom: 15px; color: #1e88e5; }
        .profile-info, form {
            background: white; padding: 20px; border-radius: 8px; max-width: 600px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 40px;
        }
        .profile-info p { margin: 10px 0; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; }
        button {
            margin-top: 15px; background: #1e88e5; color: white; padding: 10px 15px;
            border: none; border-radius: 4px; cursor: pointer;
        }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #1e88e5; color: white; }
        tr:hover { background: #f1f1f1; }
        .status-confirmed { color: green; font-weight: bold; }
        .status-pending { color: orange; font-weight: bold; }
        .status-cancelled { color: red; font-weight: bold; }
        a.back { display: inline-block; margin-top: 20px; text-decoration: none; color: #1e88e5; font-weight: 500; }
        a.back:hover { text-decoration: underline; }
    </style>
</head>
<body>

<h2>👤 Mon Profil</h2>

<div class="profile-info">
    <form method="post">
        <label>Nom complet :</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($student['full_name']) ?>" required>

        <label>Email :</label>
        <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>



        <button type="submit">💾 Mettre à jour</button>
    </form>
</div>

<h2>📜 Historique de participation</h2>

<?php if (count($allEvents) > 0): ?>
<table>
    <thead>
        <tr>
            <th>Titre</th>
            <th>Description</th>
            <th>Date début</th>
            <th>Date fin</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($allEvents as $event): ?>
            <tr>
                <td><?= htmlspecialchars($event['title']) ?></td>
                <td><?= htmlspecialchars($event['description']) ?></td>
                <td><?= htmlspecialchars($event['start_date']) ?></td>
                <td><?= htmlspecialchars($event['end_date']) ?></td>
                <td class="status-<?= strtolower($event['status']) ?>">
                    <?= ucfirst($event['status']) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
    <p>Vous n'avez participé à aucun événement.</p>
<?php endif; ?>

<a href="home.php" class="back">← Retour à l’accueil</a>

</body>
</html>
