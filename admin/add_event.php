<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $type = $_POST['type'];
    $max_participants = $_POST['max_participants'];

    $stmt = $pdo->prepare("INSERT INTO events (title, description, location, start_date, end_date, type, max_participants)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $location, $start_date, $end_date, $type, $max_participants]);

    $message = "✅ Événement ajouté avec succès.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un événement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f5f5f5;
        }
        h2 {
            color: #333;
        }
        form {
            background-color: #fff;
            padding: 20px;
            width: 500px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input[type="text"],
        input[type="number"],
        input[type="datetime-local"],
        select,
        textarea {
            width: 80%;
            padding: 8px;
            margin-top: 4px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #007BFF;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            color: green;
            margin-bottom: 10px;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007BFF;
        }
        a:hover {
            text-decoration: underline;
        }

.container {
    display: flex;
    gap: 40px; 
    align-items: flex-start; 
    margin-top: 20px;
}

form {
    background-color: #fff;
    padding: 20px;
    width: 500px; 
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    flex-shrink: 0; 
}
 .image-container {
    flex-grow: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%; 
}
.image-container img {
    width: 80%;
    height: auto;
    border-radius: 10px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
    object-fit: cover;
}
.container {
    display: flex;
    gap: 40px;
    align-items: flex-start;
    margin-top: 20px;
    width: 100%;
}

    </style>
</head>
<body>

    <h2>Ajouter un événement</h2>
        <a href="dashboard.php">← Retour au acceuil</a>
    <?php if ($message): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <div class="container">
        <form method="post">
            <label>Titre:</label>
            <input type="text" name="title" required>

            <label>Description:</label>
            <textarea name="description" required></textarea>

            <label>Lieu:</label>
            <input type="text" name="location" required>

            <label>Date début:</label>
            <input type="datetime-local" name="start_date" required>

            <label>Date fin:</label>
            <input type="datetime-local" name="end_date" required>

            <label>Type:</label>
            <select name="type">
                <option value="job_fair">Job Fair</option>
                <option value="workshop">Workshop</option>
                <option value="training">Training</option>
                <option value="meeting">Meeting</option>
                <option value="other">Other</option>
            </select>

            <label>Nombre max. de participants:</label>
            <input type="number" name="max_participants" required>

            <button type="submit">Ajouter</button>
        </form>

        <div class="image-container">
            <img src="../uploads/ev.jpg" alt="Image événement" />
        </div>
    </div>

</body>


</html>
