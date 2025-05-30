<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: connexion/login.php');
    exit;
}
