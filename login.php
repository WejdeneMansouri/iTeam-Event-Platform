<?php
session_start();
require_once 'config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = $_POST['identifiant'] ?? '';
    $password = $_POST['password'] ?? '';
    $profile = $_POST['profile'] ?? '';

    if ($profile === 'admin') {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$identifiant]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

if ($admin && password_verify($password, $admin['password_hash'])) {
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
    <title>Connexion - ITEAM University</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f5;
        }
        .login-container {
            width: 400px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
        .logo {
            display: block;
            margin: 0 auto 10px auto;
            height: 80px;
        }
        .title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            color: #003366;
            margin-bottom: 20px;
        }
        .form-control {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-select {
            background-color: #f9f9f9;
        }
        .submit-btn {
            background-color: #0056b3;
            color: white;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #004494;
        }
        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
        .profile-tabs {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .profile-tabs label {
            flex: 1;
            text-align: center;
            padding: 10px;
            border: 1px solid #ccc;
            cursor: pointer;
            background-color: #f0f0f0;
        }
        .profile-tabs input:checked + label {
            background-color: #0056b3;
            color: white;
        }
        input[type="radio"] {
            display: none;
        }
    </style>
</head>
<body>
<div class="login-container">
    <img src="logo.png" alt="Logo ITEAM University" class="logo">
    <div class="title">ITEAM UNIVERSITY</div>
    <div style="text-align: center; font-size: 18px; margin-bottom: 10px;"><strong>Connexion</strong></div>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="profile-tabs">
            <input type="radio" id="student" name="profile" value="student" required>
            <label for="student">ÉTUDIANT</label>

            <input type="radio" id="admin" name="profile" value="admin">
            <label for="admin">ADMINISTRATION</label>
        </div>

        <input type="text" name="identifiant" placeholder="Login" class="form-control" required>
        <input type="password" name="password" placeholder="Mot de passe" class="form-control" required>

        <select name="annee" class="form-control form-select">
            <option value="2024/2025">2024/2025</option>
            <option value="2023/2024">2023/2024</option>
        </select>

        <button type="submit" class="form-control submit-btn">SE CONNECTER</button>
    </form>
</div>
</body>
</html>
