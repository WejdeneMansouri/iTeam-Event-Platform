<?php
session_start();
require_once '../config/db.php';
require_once '../src/PHPMailer.php';
require_once '../src/SMTP.php';
require_once '../src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Redirection si non connecté
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$message = '';

// Fonction pour générer un mot de passe aléatoire
function generatePassword($length = 10) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    return substr(str_shuffle(str_repeat($chars, $length)), 0, $length);
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'] ?? 'organizer'; // 'admin' ou 'organizer'

    if (!empty($username) && !empty($email)) {
        $plain_password = generatePassword(10);
        $password_hash = password_hash($plain_password, PASSWORD_DEFAULT);

        try {
            // Insertion selon le rôle
            if ($role === 'admin') {
                $stmt = $pdo->prepare("INSERT INTO admins (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $email, $password_hash, $role]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO students (full_name, email, password_hash, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$username, $email, $password_hash]);
            }

            // Préparation de l'email
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'wejdene404@gmail.com';
            $mail->Password = 'jgmn ujdh zpde utgd'; // ➤ mot de passe application Gmail
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
                <p>Connectez-vous : " . ($role === 'admin' ?
                    "<a href='http://localhost/webdev/event-platform/login.php'>Se connecter (Admin)</a>" :
                    "<a href='http://localhost/webdev/event-platform/login.php'>Se connecter (Étudiant)</a>") .
                "</p>
                <p>Cordialement,<br>L'équipe iTeam</p>";

            $mail->send();
            $message = "✅ Utilisateur ajouté avec succès. Email envoyé à $email";
        } catch (Exception $e) {
            $message = "✅ Utilisateur ajouté. ⚠️ Mais erreur lors de l'envoi de l'email : " . $mail->ErrorInfo;
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
        body { font-family: Arial; background: #eef; padding: 30px; }
        .container { background: white; padding: 20px; border-radius: 8px; width: 400px; margin: auto; box-shadow: 0 0 10px #ccc; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input, select, button { width: 100%; padding: 8px; margin-top: 5px; }
        button { background: #007BFF; color: white; border: none; cursor: pointer; }
        .message { margin-top: 15px; font-weight: bold; }
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
