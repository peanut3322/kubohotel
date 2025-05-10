<?php
session_start();
include '../db.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        h1 { color: #333; }
        a {
            display: block;
            margin: 10px 0;
            color: white;
            background-color: #007BFF;
            padding: 10px;
            width: 250px;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
        }
        a:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <h1>Welcome, Admin!</h1>
    <a href="pending_reservations.php">Pending Reservations</a>
    <a href="all_reservations.php">All Reservations</a>
    <a href="manage_rooms.php">Manage Rooms</a>
    <a href="logout.php">Logout</a>
</body>
</html>
