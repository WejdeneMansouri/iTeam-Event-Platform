<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $proposal_id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT image_path FROM proposed_events WHERE id = :id");
    $stmt->execute(['id' => $proposal_id]);
    $proposal = $stmt->fetch();

    if ($proposal && $proposal['image_path']) {
        $imagePath = '../' . $proposal['image_path'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM proposed_events WHERE id = :id");
    $stmt->execute(['id' => $proposal_id]);

    header("Location: proposed_events.php?deleted=1");
    exit;
}

header("Location: proposed_events.php?error=1");
exit;
