<?php
session_start();
require_once '../config/db.php';
require_once '../src/PHPMailer.php';
require_once '../src/SMTP.php';
require_once '../src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['student_id'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['event_id'])) {
    die("Aucun événement sélectionné.");
}

$event_id = intval($_GET['event_id']);
$student_id = $_SESSION['student_id'];
$success = false;
$email_sent = false;

// Récupération de l’événement
$eventStmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$eventStmt->execute([$event_id]);
$event = $eventStmt->fetch();

// Vérifie s’il est déjà inscrit
$stmt = $pdo->prepare("SELECT * FROM participants WHERE student_id = ? AND event_id = ?");
$stmt->execute([$student_id, $event_id]);
$existing = $stmt->fetch();

if (!$existing && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_SESSION['student_name'];
    $email = $_POST['email'] ?? '';

    $stmt = $pdo->prepare("INSERT INTO participants (full_name, email, event_id, student_id, status)
                           VALUES (?, ?, ?, ?, 'pending')");
    $stmt->execute([$full_name, $email, $event_id, $student_id]);
    $success = true;

    // Envoi du mail de pré-inscription
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'wejdene404@gmail.com'; // 👉 Remplace ici
        $mail->Password   = 'jgmn ujdh zpde utgd'; // 👉 Ton mot de passe d'application
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('ton_email@gmail.com', 'iTeam Events');
        $mail->addAddress($email, $full_name);

        $mail->isHTML(true);
        $mail->Subject = 'Pré-inscription reçue';
        $mail->Body    = "
            Bonjour $full_name,<br><br>
            Votre demande de pré-inscription à l’événement <strong>{$event['title']}</strong> a bien été enregistrée.<br>
            Vous recevrez une notification dès que votre participation sera confirmée par un administrateur.<br><br>
            Merci et à bientôt !";

        $mail->send();
        $email_sent = true;
    } catch (Exception $e) {
        $email_sent = false;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Pré-inscription</title>
    <style>
        body { font-family: Arial; background: #f4f7fc; padding: 30px; }
        .box { max-width: 500px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { color: #007BFF; }
        input, button { width: 100%; margin-top: 10px; padding: 10px; border: 1px solid #ccc; border-radius: 6px; }
        button { background: #007BFF; color: white; font-weight: bold; cursor: pointer; }
        .success { background: #d4edda; padding: 10px; border-left: 4px solid #28a745; margin-bottom: 10px; }
        .error { background: #f8d7da; padding: 10px; border-left: 4px solid #dc3545; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="box">
    <h2>Pré-inscription à : <?= htmlspecialchars($event['title']) ?></h2>

    <?php if ($success): ?>
        <div class="success">Votre pré-inscription a été enregistrée ✅</div>
        <?php if ($email_sent): ?>
            <div class="success">Un email de confirmation a été envoyé 📧</div>
        <?php else: ?>
            <div class="error">⚠️ Email non envoyé. Vérifiez la configuration SMTP.</div>
        <?php endif; ?>
    <?php else: ?>
        <form method="post">
            <label>Email :</label>
            <input type="email" name="email" required>
            <button type="submit">Confirmer</button>
        </form>
    <?php endif; ?>

    <a href="home.php">← Retour</a>
</div>

</body>
</html>
