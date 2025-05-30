<?php
require_once '../config/db.php';

header('Content-Type: application/json');

$stmt = $pdo->query("SELECT id, title, start_date AS start, end_date AS end FROM events");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($events);
