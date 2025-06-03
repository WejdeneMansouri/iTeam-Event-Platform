<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $image_path = null;

    if (!empty($_FILES['image']['name'])) {
        $upload_dir = '../uploads/proposed_events/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        $target_path = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_path = 'uploads/proposed_events/' . $filename;
        } else {
            $error = "Erreur lors du téléversement de l'image.";
        }
    }

    if (!$error) {
        $stmt = $pdo->prepare("INSERT INTO proposed_events (student_id, title, description, start_date, end_date, image_path) 
                               VALUES (:student_id, :title, :description, :start_date, :end_date, :image_path)");
        $stmt->execute([
            'student_id' => $student_id,
            'title' => $title,
            'description' => $description,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'image_path' => $image_path
        ]);

        $success = "Votre proposition a été envoyée avec succès !";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Proposer un événement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f7f9fb;
        }
        h2 {
            margin-bottom: 20px;
            color: #1e88e5;
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            max-width: 600px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            background: #1e88e5;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background: #1565c0;
        }
        .success {
            background: #c8e6c9;
            padding: 10px;
            margin-bottom: 10px;
            border-left: 5px solid #2e7d32;
        }
        .error {
            background: #ffcdd2;
            padding: 10px;
            margin-bottom: 10px;
            border-left: 5px solid #c62828;
        }
         .image-container {
    flex-grow: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
}
.image-container img {
    width: 100%;          
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
.back-link {
    display: inline-block;
    margin-bottom: 20px;
    color: #1e88e5;
    text-decoration: none;
    font-weight: bold;
    font-size: 16px;
    transition: color 0.2s ease;
}
.back-link:hover {
    color: #1565c0;
    text-decoration: underline;
}

    </style>
</head>
<body>


<h2>Proposer un événement</h2>
<a href="home.php" class="back-link">← Retour à l'accueil</a>
<?php if ($success): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
<?php elseif ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="container">
    
    <form method="post" enctype="multipart/form-data">
        <label>Titre :</label>
        <input type="text" name="title" required>

        <label>Description :</label>
        <textarea name="description" rows="5" required></textarea>

        <label>Date de début :</label>
        <input type="date" name="start_date" required>

        <label>Date de fin :</label>
        <input type="date" name="end_date" required>

        <label>Image (optionnel) :</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit">Envoyer la proposition</button>
    </form>

    <div class="image-container">
        <img src="../uploads/ev.jpg" alt="Image événement" />
    </div>
</div>


</body>
</body>
</html>
