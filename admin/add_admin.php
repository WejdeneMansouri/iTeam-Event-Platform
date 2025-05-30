<?php
session_start();
require_once '../config/db.php';
require_once '../src/PHPMailer.php';
require_once '../src/SMTP.php';
require_once '../src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$message = '';

function generatePassword($length = 10) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    return substr(str_shuffle(str_repeat($chars, $length)), 0, $length);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $cin = trim($_POST['cin']);
    $gender = $_POST['gender'] ?? '';
    $role = $_POST['role'] ?? 'organizer';

    if (!empty($username) && !empty($email) && !empty($phone) && !empty($cin) && !empty($gender)) {
        $plain_password = generatePassword(10);
        $password_hash = password_hash($plain_password, PASSWORD_DEFAULT);

        try {
            if ($role === 'admin') {
                $stmt = $pdo->prepare("INSERT INTO admins (username, email, password_hash, role, phone, cin, gender) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$username, $email, $password_hash, $role, $phone, $cin, $gender]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO students (full_name, email, password_hash, created_at, phone, cin, gender) VALUES (?, ?, ?, NOW(), ?, ?, ?)");
                $stmt->execute([$username, $email, $password_hash, $phone, $cin, $gender]);
            }

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'wejdene404@gmail.com';
            $mail->Password = 'jgmn ujdh zpde utgd';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('wejdene404@gmail.com', 'iTeam Events');
            $mail->addAddress($email, $username);
            $mail->isHTML(true);
            $mail->Subject = 'Votre compte iTeam Events';
            $mail->Body = "
                <p>Bonjour <strong>$username</strong>,</p>
                <p>Un compte a été créé pour vous sur <strong>iTeam Events</strong>.</p>
                <p><strong>Identifiant :</strong> $email<br>
                   <strong>Mot de passe :</strong> $plain_password</p>
                <p>Connectez-vous : <a href='http://localhost/webdev/event-platform/login.php'>Se connecter</a></p>
                <p>Cordialement,<br>L'équipe iTeam</p>";

            $mail->send();
            $message = "✅ Utilisateur ajouté avec succès. Email envoyé à $email";
        } catch (Exception $e) {
            $message = "✅ Utilisateur ajouté. ⚠️ Erreur email : " . $mail->ErrorInfo;
        }
    } else {
        $message = "❌ Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajout utilisateur</title>
    <style>
        body {
            font-family: Arial;
            background: url('back.png') no-repeat center center fixed;
            background-size: cover;
            padding: 30px;
        }
        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 25px;
            border-radius: 10px;
            width: 450px;
            margin: auto;
            box-shadow: 0 0 15px #888;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input, select, button {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 5px;
        }
        button {
            background: #007BFF;
            color: white;
            border: none;
            margin-top: 15px;
        }
        .message {
            margin-top: 15px;
            font-weight: bold;
        }
        .back {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #007BFF;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Ajouter un utilisateur</h2>
    <form method="post">
        <label for="username">Nom complet</label>
        <input type="text" id="username" name="username" required>

        <label for="email">Adresse email</label>
        <input type="email" id="email" name="email" required>

        <label for="phone">Numéro de téléphone</label>
        <input type="text" id="phone" name="phone" required>

        <label for="cin">Numéro CIN</label>
        <input type="text" id="cin" name="cin" required>

        <label for="gender">Sexe</label>
        <select id="gender" name="gender" required>
            <option value="">-- Sélectionner --</option>
            <option value="Homme">Homme</option>
            <option value="Femme">Femme</option>
        </select>

        <label for="role">Rôle</label>
        <select id="role" name="role">
            <option value="organizer">Étudiant</option>
            <option value="admin">Administrateur</option>
        </select>

        <button type="submit">Créer le compte</button>
    </form>

    <?php if ($message): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>
</div>

<a href="dashboard.php" class="back">← Retour à l’accueil</a>

</body>
</html>
