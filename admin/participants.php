<?php
session_start();
require_once '../config/db.php';

// Importation de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../src/PHPMailer.php';
require_once '../src/SMTP.php';
require_once '../src/Exception.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Récupération des événements
$eventsStmt = $pdo->query("SELECT id, title FROM events ORDER BY start_date DESC");
$events = $eventsStmt->fetchAll(PDO::FETCH_ASSOC);

// Filtrage
$filter_event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

// Traitement des actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $participant_id = intval($_GET['id']);
    $action = $_GET['action'];

    if (in_array($action, ['confirmed', 'cancelled'])) {
        // Mise à jour du statut
        $stmt = $pdo->prepare("UPDATE participants SET status = ? WHERE id = ?");
        $stmt->execute([$action, $participant_id]);

        // Récupérer infos participant
        $stmt = $pdo->prepare("SELECT p.full_name, p.email, e.title AS event_title FROM participants p
                               JOIN events e ON p.event_id = e.id
                               WHERE p.id = ?");
        $stmt->execute([$participant_id]);
        $participant = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($participant) {
            // Envoi e-mail si confirmé
            if ($action === 'confirmed') {
                sendConfirmationEmail($participant);
            }

            // Envoi e-mail si annulé
            if ($action === 'cancelled') {
                sendCancellationEmail($participant);
            }
        }
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM participants WHERE id = ?");
        $stmt->execute([$participant_id]);
    }
}

// Fonction pour envoyer l'email de confirmation
function sendConfirmationEmail($participant) {
    // Envoi e-mail via PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'wejdene404@gmail.com'; // Remplace avec ton adresse Gmail
        $mail->Password = 'jgmn ujdh zpde utgd'; // Ton mot de passe d'application
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('wejdene404@gmail.com', 'iTeam Event Platform');
        $mail->addAddress($participant['email'], $participant['full_name']);

        $mail->isHTML(true);
        $mail->Subject = 'Confirmation de participation';
        $mail->Body = "Bonjour <strong>{$participant['full_name']}</strong>,<br><br>
                       Votre participation à l'événement <strong>{$participant['event_title']}</strong> a été <span style='color:green;'>confirmée</span>.<br><br>
                       Merci de votre inscription !<br><br>
                       Cordialement,<br>L'équipe iTeam.";

        $mail->send();
    } catch (Exception $e) {
        error_log("Erreur mail : " . $mail->ErrorInfo);
    }
}

// Fonction pour envoyer l'email de refus
function sendCancellationEmail($participant) {
    // Envoi e-mail via PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'wejdene404@gmail.com'; // Remplace avec ton adresse Gmail
        $mail->Password = 'jgmn ujdh zpde utgd'; // Ton mot de passe d'application
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('wejdene404@gmail.com', 'iTeam Event Platform');
        $mail->addAddress($participant['email'], $participant['full_name']);

        $mail->isHTML(true);
        $mail->Subject = 'Refus de participation';
        $mail->Body = "Bonjour <strong>{$participant['full_name']}</strong>,<br><br>
                       Nous regrettons de vous informer que votre participation à l'événement <strong>{$participant['event_title']}</strong> n'a pas été acceptée.<br><br>
                       Nous vous remercions de votre intérêt.<br><br>
                       Cordialement,<br>L'équipe iTeam.";

        $mail->send();
    } catch (Exception $e) {
        error_log("Erreur mail : " . $mail->ErrorInfo);
    }
}

// Récupération des participants
if ($filter_event_id > 0) {
    $stmt = $pdo->prepare("SELECT p.*, e.title AS event_title FROM participants p
                           LEFT JOIN events e ON p.event_id = e.id
                           WHERE p.event_id = ?
                           ORDER BY p.registered_at DESC");
    $stmt->execute([$filter_event_id]);
} else {
    $stmt = $pdo->query("SELECT p.*, e.title AS event_title FROM participants p
                         LEFT JOIN events e ON p.event_id = e.id
                         ORDER BY p.registered_at DESC");
}
$participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des participants</title>
    <style>
        body { font-family: Arial; margin: 40px; background: #f9f9f9; }
        h2 { color: #333; }
        a { text-decoration: none; color: #007BFF; }
        a:hover { text-decoration: underline; }
        .nav { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: 10px; border: 1px solid #ccc; }
        th { background: #007BFF; color: white; }
        .btn { padding: 5px 10px; font-size: 0.9em; border-radius: 4px; color: white; margin-right: 5px; display: inline-block; }
        .confirm { background-color: #28a745; }
        .cancel { background-color: #dc3545; }
        .delete { background-color: #6c757d; }
        /* Style du label du formulaire */
.form-label {
    font-weight: bold;
    color: #007BFF; /* Couleur bleue */
    margin-right: 10px;
}

/* Style du formulaire de sélection */
form {
    margin-bottom: 20px;
    font-size: 1em;
}

/* Style de la sélection des événements */
#event_id {
    padding: 6px 12px;
    font-size: 1em;
    border-radius: 5px;
    border: 1px solid #ccc;
    background-color: #fff;
    transition: border-color 0.3s;
}

/* Change la couleur de bordure au survol */
#event_id:hover {
    border-color: #007BFF;
}

    </style>
</head>
<body>

<h2>Liste des participants</h2>
<div class="nav">
    <a href="dashboard.php">← Retour au dashboard</a>
</div>

<form method="get" action="">
    <label for="event_id">Filtrer par événement :</label>
    <select name="event_id" id="event_id" onchange="this.form.submit()">
        <option value="0">-- Tous les événements --</option>
        <?php foreach ($events as $event): ?>
            <option value="<?= $event['id'] ?>" <?= ($filter_event_id == $event['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($event['title']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<?php if (count($participants) > 0): ?>
    <table>
        <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Organisation</th>
            <th>Événement</th>
            <th>Profil</th>
            <th>Statut</th>
            <th>Action</th>
        </tr>
        <?php foreach ($participants as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['full_name']) ?></td>
                <td><?= htmlspecialchars($p['email']) ?></td>
                <td><?= htmlspecialchars($p['phone']) ?></td>
                <td><?= htmlspecialchars($p['organization']) ?></td>
                <td><?= htmlspecialchars($p['event_title']) ?></td>
                <td><?= $p['profile'] ?></td>
                <td><?= $p['status'] ?></td>
                <td>
                    <?php if ($p['status'] === 'pending'): ?>
                        <a class="btn confirm" href="?action=confirmed&id=<?= $p['id'] ?>&event_id=<?= $filter_event_id ?>">Valider</a>
                        <a class="btn cancel" href="?action=cancelled&id=<?= $p['id'] ?>&event_id=<?= $filter_event_id ?>">Annuler</a>
                    <?php endif; ?>
                    <a class="btn delete" href="?action=delete&id=<?= $p['id'] ?>&event_id=<?= $filter_event_id ?>" onclick="return confirm('Supprimer ce participant ?')">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Aucun participant trouvé.</p>
<?php endif; ?>

</body>
</html>
