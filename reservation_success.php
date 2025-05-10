<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Customer') {
    header("Location: login.php");
    exit;
}

// Get room details from URL
$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
$room_type = isset($_GET['room_type']) ? htmlspecialchars($_GET['room_type']) : '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guest_name = $_POST['guest_name'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    $checkin_date = $_POST['checkin_date'];
    $checkout_date = $_POST['checkout_date'];
    $guest_count = $_POST['guest_count'];

    try {
        // Insert reservation into the database
        $stmt = $pdo->prepare("INSERT INTO reservations (room_id, guest_name, contact_number, email, checkin_date, checkout_date, guest_count, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
        $stmt->execute([$room_id, $guest_name, $contact_number, $email, $checkin_date, $checkout_date, $guest_count]);

        // Set success message
        $_SESSION['success_message'] = "Reservation successful! Please wait for a confirmation via message.";
        header("Location: reservation_success.php");
        exit;
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reserve Room</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: white;
            color: #333;
        }

        .sidebar {
            background-color: red;
            color: white;
            padding: 20px;
            width: 220px;
            height: 100vh;
            position: fixed;
        }

        .sidebar h2 {
            text-align: center;
        }

        .sidebar img {
            width: 180px;
            margin: 0 auto;
            display: block;
        }

        .sidebar a {
            display: block;
            padding: 10px;
            margin: 5px 0;
            text-decoration: none;
            color: white;
            background-color: darkred;
            border-radius: 4px;
        }

        .sidebar a:hover {
            background-color: #b30000;
        }

        .main-content {
            margin-left: 240px;
            padding: 20px;
        }

        h2 {
            color: red;
            text-align: center;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            margin: 0 auto;
            width: 300px;
        }

        input, button {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border-radius: 4px;
        }

        button {
            background-color: red;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #b30000;
        }

        .message-box {
            background-color: #4CAF50; /* Green background */
            color: white;
            padding: 10px;
            text-align: center;
            margin-top: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Hotel Logo</h2>
    <img src="kubo.png" alt="Hotel Logo">
    <a href="dashboard.php">Dashboard</a>
    <a href="reservation.php">My Reservations</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
    <h2>Reserve Room: <?php echo $room_type; ?> (ID: <?php echo $room_id; ?>)</h2>
    
    <!-- Display Success Message -->
    <?php
    if (isset($_SESSION['success_message'])) {
        echo "<div class='message-box'>" . $_SESSION['success_message'] . "</div>";
        unset($_SESSION['success_message']);
    }
    ?>

    <form method="POST">
        <label>Guest Name:</label>
        <input type="text" name="guest_name" required><br>
        <label>Contact Number:</label>
        <input type="text" name="contact_number" required><br>
        <label>Email:</label>
        <input type="email" name="email" required><br>
        <label>Check-In Date:</label>
        <input type="date" name="checkin_date" required><br>
        <label>Check-Out Date:</label>
        <input type="date" name="checkout_date" required><br>
    
        <button type="submit">Confirm Reservation</button>
    </form>
</div>

</body>
</html>
