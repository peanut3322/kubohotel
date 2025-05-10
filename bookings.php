<?php
session_start();
include 'db.php';

// Access control: Only allow logged-in users
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

// Handle user registration
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = $_POST['role'];

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM Users WHERE Username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            $error = "Username already taken.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO Users (Username, Password, Role) VALUES (?, ?, ?)");
            $insert->execute([$username, $hashed, $role]);
            $message = "Account created successfully!";
        }
    }
}

// Fetch all users
$users = $conn->query("SELECT * FROM Users")->fetchAll(PDO::FETCH_ASSOC);
// Fetch all bookings
$bookings = $conn->query("SELECT * FROM Bookings")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard with User and Booking Info</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1c1c1c;
            color: #fff;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background-color: #2c2c2c;
            padding: 20px;
            border-radius: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #444;
        }
        .btn {
            padding: 8px 12px;
            background-color: #d4af37;
            color: #000;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #c39c34;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .sidebar {
            width: 200px;
            background-color: #333;
            padding: 15px;
            float: left;
        }
        .sidebar a {
            display: block;
            padding: 8px;
            color: #fff;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>Admin Menu</h3>
        <a href="#">Dashboard</a>
        <a href="#">User Info</a>
        <a href="#">Bookings</a>
    </div>

    <div class="container">
        <h2>Admin Dashboard with User and Booking Info</h2>

        <?php if (isset($message)) echo "<p style='color:green;'>$message</p>"; ?>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

        <h3>Register User</h3>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="Admin">Admin</option>
                <option value="Staff">Staff</option>
                <option value="Customer">Customer</option>
            </select>
            <button class="btn" type="submit" name="register">Register</button>
        </form>

        <h3>Registered Users</h3>
        <table>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Role</th>
            </tr>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['UserID']; ?></td>
                    <td><?php echo htmlspecialchars($user['Username']); ?></td>
                    <td><?php echo htmlspecialchars($user['Role']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3>All Bookings</h3>
        <table>
            <tr>
                <th>Booking ID</th>
                <th>Room ID</th>
                <th>Guest Info</th>
                <th>Contact Number</th>
                <th>Email</th>
                <th>Check-In</th>
                <th>Check-Out</th>
            </tr>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo $booking['BookingID']; ?></td>
                    <td><?php echo $booking['RoomID']; ?></td>
                    <td><?php echo htmlspecialchars($booking['GuestInfo']); ?></td>
                    <td><?php echo htmlspecialchars($booking['ContactNumber']); ?></td>
                    <td><?php echo htmlspecialchars($booking['Email']); ?></td>
                    <td><?php echo htmlspecialchars($booking['CheckIn']); ?></td>
                    <td><?php echo htmlspecialchars($booking['CheckOut']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
