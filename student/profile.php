<?php
session_start();
require_once '../config/db.php'; 

if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

$student_id = $_SESSION['student_id'];

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';

    $update = $pdo->prepare("UPDATE students SET full_name = ?, email = ?, phone = ? WHERE id = ?");
    $update->execute([$full_name, $email, $phone, $student_id]);

    $_SESSION['student_name'] = $full_name;

    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    $message = "✅ Informations mises à jour avec succès.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f6fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }
        form label {
            display: block;
            margin-top: 15px;
            color: #555;
        }
        form input {
            width: 100%;
            padding: 8px 12px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        form button {
            margin-top: 20px;
            background-color: #2ecc71;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        form button:hover {
            background-color: #27ae60;
        }
        .photo {
            text-align: center;
            margin-top: 20px;
        }
        .photo img {
            max-width: 120px;
            border-radius: 8px;
            border: 2px solid #ddd;
        }
        .success-message {
            background-color: #dff0d8;
            padding: 10px;
            border-radius: 6px;
            color: #3c763d;
            margin-bottom: 15px;
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

<div class="container">
    <h2>Mon Profil</h2>

    <?php if (!empty($message)) : ?>
        <div class="success-message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post">
        <label>Nom complet :</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($student['full_name']) ?>" required>

        <label>Email :</label>
        <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>

        <label>Téléphone :</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($student['phone']) ?>">

        <label>CIN :</label>
        <input type="text" value="<?= htmlspecialchars($student['cin']) ?>" disabled>

        <label>Genre :</label>
        <input type="text" value="<?= htmlspecialchars($student['gender']) ?>" disabled>

        <label>Date d'inscription :</label>
        <input type="text" value="<?= htmlspecialchars($student['created_at']) ?>" disabled>

        <button type="submit">💾 Mettre à jour</button>
        <a href="home.php" class="back">← Retour à l’accueil</a>

    </form>
</div>
</body>
</html>