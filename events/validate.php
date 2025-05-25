<?php
require_once '../config/db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE participants SET status = 'approved' WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: list.php");
exit;
