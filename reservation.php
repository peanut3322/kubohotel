<?php
session_start();
include 'db.php';

// Only Customers can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Customer') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
$room_type = isset($_GET['room_type']) ? htmlspecialchars($_GET['room_type']) : '';

$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    try {
        $stmt = $conn->prepare("INSERT INTO Bookings (RoomID, GuestInfo, ContactNumber, Email, CheckIn, CheckOut)
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$room_id, $username, $contact_number, $email, $check_in, $check_out]);
        $successMessage = "✔ Booking successfully added! We will contact you soon.";
        echo "<script>alert('✔ Booking successful! We will contact you soon.');</script>";
    } catch (PDOException $e) {
        $errorMessage = "❌ Booking failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Room Booking</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            height: 100vh;
            background-image: url('images/hotel.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        @keyframes slideInLeft {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .sidebar {
            width: 220px;
            background-color: rgba(87, 76, 76, 0.9);
            padding: 15px;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-radius: 10px;
            animation: slideInLeft 0.7s ease-out;
        }

        .sidebar h2 {
            margin-bottom: 20px;
            font-size: 24px;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 12px;
            margin: 10px 0;
            text-decoration: none;
            border-radius: 4px;
            background-color: rgb(90, 84, 84);
            text-align: center;
            width: 100%;
        }

        .sidebar a:hover {
            background-color: rgb(102, 97, 97);
        }

        .sidebar img {
            max-width: 150px;
            margin-bottom: 20px;
        }

        .main {
            flex: 1;
            padding: 30px;
            background-color: rgba(65, 54, 54, 0.9);
            overflow-y: auto;
            border-radius: 10px;
            color: #fff;
        }

        .header {
            background-color: #d32828;
            color: white;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        form {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            max-width: 500px;
            margin: 0 auto;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.2);
            color: #333;
        }

        input, button {
            width: 100%;
            margin: 10px 0;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            background-color: gold;
            color: white;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: rgb(197, 56, 56);
        }

        .message-box {
            padding: 15px;
            border-radius: 5px;
            margin: 20px auto;
            max-width: 500px;
            font-weight: bold;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .nav-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
        }

        .nav-buttons a {
            text-decoration: none;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
        }

        .nav-buttons a:hover {
            background-color: #0056b3;
        }

        h2 {
            text-align: center;
            color: white;
        }

        label {
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <img src="kubo.png" alt="Logo">
    <h2>Customer Panel</h2>
    <a href="user_dashboard.php">Dashboard</a>
    <a href="logout.php">Logout</a>
</div>

<!-- Main Content -->
<div class="main">
    <div class="header">
        <h1>Book Room</h1>
    </div>

    <h2><?php echo htmlspecialchars($room_type); ?> (Room ID: <?php echo $room_id; ?>)</h2>

    <?php if ($successMessage): ?>
        <div class="message-box success"><?php echo $successMessage; ?></div>
    <?php elseif ($errorMessage): ?>
        <div class="message-box error"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Your Name:</label>
        <input type="text" value="<?php echo htmlspecialchars($username); ?>" disabled>

        <label>Contact Number:</label>
        <input type="text" name="contact_number" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Check-In Date:</label>
        <input type="date" name="check_in" required>

        <label>Check-Out Date:</label>
        <input type="date" name="check_out" required>

        <button type="submit">Confirm Booking</button>
    </form>

    <div class="nav-buttons">
        <a href="dashboard.php">← Back to Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

</body>
</html>
