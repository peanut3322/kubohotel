<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);
$status = $_GET['status'];

// Update reservation status
$stmt = $pdo->prepare("UPDATE reservations SET status = ? WHERE id = ?");
$stmt->execute([$status, $id]);

header("Location: login.php");
exit;
?>
