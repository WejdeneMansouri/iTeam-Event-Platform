<?php
session_start();
require_once 'config/db.php'; // Assure-toi que ce chemin est bon

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = $_POST['identifiant'] ?? '';
    $password = $_POST['password'] ?? '';
    $profile = $_POST['profile'] ?? '';

    if ($profile === 'admin') {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$identifiant]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && $password === $admin['password_hash']) {

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            header('Location: admin/dashboard.php');
            exit;
        } else {
            $error = "Identifiants administrateur invalides.";
        }

    } elseif ($profile === 'student') {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
        $stmt->execute([$identifiant]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student && password_verify($password, $student['password_hash'])) {
            $_SESSION['student_id'] = $student['id'];
            $_SESSION['student_name'] = $student['full_name'];
            header('Location: student/home.php');
            exit;
        } else {
            $error = "Identifiants étudiant invalides.";
        }

    } else {
        $error = "Veuillez sélectionner un profil.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f2f2f2; }
        .login-box {
            width: 350px; margin: 100px auto; background: white; padding: 20px;
            border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; color: #007BFF; }
        input, select, button {
            width: 100%; padding: 10px; margin-top: 10px;
            border: 1px solid #ccc; border-radius: 5px;
        }
        button { background-color: #007BFF; color: white; border: none; }
        .error { color: red; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
<div class="login-box">
    <h2>Connexion</h2>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
        <select name="profile" required>
            <option value="">-- Choisir le profil --</option>
            <option value="admin">Administrateur</option>
            <option value="student">Étudiant</option>
        </select>
        <input type="text" name="identifiant" placeholder="Nom d'utilisateur ou Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">Se connecter</button>
    </form>
</div>
</body>
</html>
