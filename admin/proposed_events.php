<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->query("SELECT pe.*, s.full_name FROM proposed_events pe 
                     JOIN students s ON pe.student_id = s.id 
                     ORDER BY pe.created_at DESC");
$proposals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Propositions d'Événements</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7fc;
            padding: 30px;
            color: #333;
        }
        h2 {
            font-size: 24px;
            color: #1e88e5;
            margin-bottom: 25px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #1e88e5;
            color: white;
        }
        img {
            max-width: 150px;
            border-radius: 5px;
        }
        .actions a {
            margin-right: 10px;
            text-decoration: none;
            color: #1565c0;
            font-weight: bold;
        }
        .actions a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h2>📩 Propositions d'Événements</h2>

<?php if (count($proposals) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Étudiant</th>
                <th>Description</th>
                <th>Dates</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($proposals as $proposal): ?>
                <tr>
                    <td><?= htmlspecialchars($proposal['title']) ?></td>
                    <td><?= htmlspecialchars($proposal['full_name']) ?></td>
                    <td><?= nl2br(htmlspecialchars($proposal['description'])) ?></td>
                    <td>
                        <?= htmlspecialchars($proposal['start_date']) ?> → <?= htmlspecialchars($proposal['end_date']) ?>
                    </td>
                    <td>
                        <?php if ($proposal['image_path'] && file_exists('../' . $proposal['image_path'])): ?>
                            <img src="../<?= htmlspecialchars($proposal['image_path']) ?>" alt="Image événement">
                        <?php else: ?>
                            <small>Aucune image</small>
                        <?php endif; ?>
                    </td>
                    <td class="actions">
                        <a href="approve_proposal.php?id=<?= $proposal['id'] ?>">✅ Approuver</a>
                        <a href="delete_proposal.php?id=<?= $proposal['id'] ?>" onclick="return confirm('Supprimer cette proposition ?')">🗑️ Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Aucune proposition trouvée.</p>
<?php endif; ?>
    <a href="dashboard.php">← Retour au dashboard</a>

</body>
</html>
