<?php
session_start();
include 'db.php'; // Assumes $pdo is defined inside db.php

// Redirect if not logged in or not a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Customer') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get room details from URL
$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
$room_type = isset($_GET['room_type']) ? htmlspecialchars($_GET['room_type']) : '';

$successMessage = "";

// Handle reservation submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $checkin_date = $_POST['checkin_date'];
    $checkout_date = $_POST['checkout_date'];

    try {
        $stmt = $pdo->prepare("INSERT INTO reservations (user_id, room_id, checkin_date, checkout_date, status, created_at)
                               VALUES (?, ?, ?, ?, 'Pending', NOW())");
        $stmt->execute([$user_id, $room_id, $checkin_date, $checkout_date]);

        $successMessage = "Reservation successful! Please wait for confirmation.";
    } catch (PDOException $e) {
        $successMessage = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reserve Room</title>
    <style>
        body { font-family: Arial; margin: 0; background-color: white; color: #333; }
        .sidebar { background-color: red; color: white; padding: 20px; width: 220px; height: 100vh; position: fixed; }
        .sidebar h2 { text-align: center; }
        .sidebar img { width: 180px; margin: 0 auto; display: block; }
        .sidebar a { display: block; padding: 10px; margin: 5px 0; text-decoration: none; color: white; background-color: darkred; border-radius: 4px; }
        .sidebar a:hover { background-color: #b30000; }
        .main-content { margin-left: 240px; padding: 20px; }
        h2 { color: red; text-align: center; }
        form { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2); margin: 0 auto; width: 300px; }
        input, button { width: 100%; padding: 8px; margin: 5px 0; border-radius: 4px; }
        button { background-color: red; color: white; cursor: pointer; }
        button:hover { background-color: #b30000; }
        .message-box { background-color: #4CAF50; color: white; padding: 10px; text-align: center; margin-top: 20px; border-radius: 5px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Kubo Hotel</h2>
    <img src="kubo.png" alt="Hotel Logo">
    <a href="dashboard.php">Dashboard</a>
    <a href="reservation.php">My Reservations</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
    <h2>Reserve Room: <?php echo $room_type; ?> (ID: <?php echo $room_id; ?>)</h2>

    <?php if ($successMessage): ?>
        <div class="message-box"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Check-In Date:</label>
        <input type="date" name="checkin_date" required>

        <label>Check-Out Date:</label>
        <input type="date" name="checkout_date" required>

        <button type="submit">Confirm Reservation</button>
    </form>
</div>

</body>
</html>
