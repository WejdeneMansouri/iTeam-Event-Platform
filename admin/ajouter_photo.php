<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$message = '';


$users = $pdo->query("SELECT email FROM admins UNION SELECT email FROM students")->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_email = $_POST['user_email'] ?? '';
    
    if (!empty($user_email) && isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir);

        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $newFilename = uniqid('photo_') . '.' . $ext;
        $targetPath = $uploadDir . $newFilename;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
         
            $stmt = $pdo->prepare("SELECT id FROM photos WHERE user_email = ?");
            $stmt->execute([$user_email]);

            if ($stmt->fetch()) {
                $update = $pdo->prepare("UPDATE photos SET filename = ? WHERE user_email = ?");
                $update->execute([$newFilename, $user_email]);
            } else {
                $insert = $pdo->prepare("INSERT INTO photos (user_email, filename) VALUES (?, ?)");
                $insert->execute([$user_email, $newFilename]);
            }

            $message = "✅ Photo ajoutée avec succès pour $user_email.";
        } else {
            $message = "❌ Erreur lors de l’enregistrement du fichier.";
        }
    } else {
        $message = "❌ Veuillez sélectionner un utilisateur et une photo.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une photo</title>
    <style>
        body { font-family: Arial; padding: 30px; background: #f9f9f9; }
        .container { background: white; padding: 20px; border-radius: 10px; width: 400px; margin: auto; box-shadow: 0 0 10px #ccc; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input, select, button { width: 100%; margin-top: 5px; padding: 8px; border-radius: 5px; }
        button { background: #28a745; color: white; border: none; margin-top: 15px; }
        .message { margin-top: 15px; font-weight: bold; color: green; }
    </style>
</head>
<body>

<div class="container">
    <h2>Ajouter une photo d’utilisateur</h2>
    <form method="post" enctype="multipart/form-data">
        <label for="user_email">Choisir un utilisateur :</label>
        <select name="user_email" id="user_email" required>
            <option value="">-- Sélectionner --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= htmlspecialchars($user['email']) ?>"><?= htmlspecialchars($user['email']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="photo">Téléverser une photo :</label>
        <input type="file" name="photo" id="photo" accept="image/*" required>

        <button type="submit">Ajouter la photo</button>
    </form>

    <?php if ($message): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>
</div>

</body>
</html>
