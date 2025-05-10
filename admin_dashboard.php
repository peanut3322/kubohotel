<?php
session_start();
include 'db.php';

// Access control: Only allow logged-in users
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");  // Redirect to admin_dashboard.php after logout
    exit;
}

// Handle delete request for users
if (isset($_GET['delete_user'])) {
    $deleteId = $_GET['delete_user'];
    $stmt = $conn->prepare("DELETE FROM Users WHERE UserID = ?");
    $stmt->execute([$deleteId]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle delete request for bookings
if (isset($_GET['delete_booking'])) {
    $deleteId = $_GET['delete_booking'];
    $stmt = $conn->prepare("DELETE FROM Bookings WHERE BookingID = ?");
    $stmt->execute([$deleteId]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// User edit mode
$editMode = false;
$editUsername = "";
$editRole = "";
$editId = "";

if (isset($_GET['edit_user'])) {
    $editId = $_GET['edit_user'];
    $stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
    $stmt->execute([$editId]);
    $editUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($editUser) {
        $editUsername = $editUser['Username'];
        $editRole = $editUser['Role'];
        $editMode = true;
    }
}

// Handle update for users
if (isset($_POST['update_user'])) {
    $userId = $_POST['user_id'];
    $username = trim($_POST['username']);
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE Users SET Username = ?, Role = ? WHERE UserID = ?");
    $stmt->execute([$username, $role, $userId]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle user registration
if (isset($_POST['register_user'])) {
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

// Booking edit mode
$editBookingMode = false;
$editBooking = [];

if (isset($_GET['edit_booking'])) {
    $editBookingId = $_GET['edit_booking'];
    $stmt = $conn->prepare("SELECT * FROM Bookings WHERE BookingID = ?");
    $stmt->execute([$editBookingId]);
    $editBooking = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($editBooking) {
        $editBookingMode = true;
    }
}

// Handle update for bookings
if (isset($_POST['update_booking'])) {
    $bookingId = $_POST['booking_id'];
    $roomId = $_POST['room_id'];
    $guestInfo = $_POST['guest_info'];
    $contactNumber = $_POST['contact_number'];
    $email = $_POST['email'];
    $checkIn = $_POST['check_in'];
    $checkOut = $_POST['check_out'];

    $stmt = $conn->prepare("UPDATE Bookings SET RoomID = ?, GuestInfo = ?, ContactNumber = ?, Email = ?, CheckIn = ?, CheckOut = ? WHERE BookingID = ?");
    $stmt->execute([$roomId, $guestInfo, $contactNumber, $email, $checkIn, $checkOut, $bookingId]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle booking registration
if (isset($_POST['register_booking'])) {
    $roomId = $_POST['room_id'];
    $guestInfo = $_POST['guest_info'];
    $contactNumber = $_POST['contact_number'];
    $email = $_POST['email'];
    $checkIn = $_POST['check_in'];
    $checkOut = $_POST['check_out'];

    if (empty($roomId) || empty($guestInfo) || empty($contactNumber) || empty($email) || empty($checkIn) || empty($checkOut)) {
        $error = "All fields are required.";
    } else {
        $insert = $conn->prepare("INSERT INTO Bookings (RoomID, GuestInfo, ContactNumber, Email, CheckIn, CheckOut) VALUES (?, ?, ?, ?, ?, ?)");
        $insert->execute([$roomId, $guestInfo, $contactNumber, $email, $checkIn, $checkOut]);
        $message = "Booking created successfully!";
    }
}

// Fetch all users and bookings
$users = $conn->query("SELECT * FROM Users")->fetchAll(PDO::FETCH_ASSOC);
$bookings = $conn->query("SELECT * FROM Bookings")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1c1c1c;
            color: #fff;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
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
    
        .sidebar a {
            display: block;
            padding: 8px;
            color: #fff;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #555;
        }
        .btn-action {
            margin-right: 8px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Dashboard</h2>
        <!-- Logout Button -->
        <a href="?logout=true" class="btn" style="background-color: red; color: white;">Logout</a>

        <?php if (isset($message)) echo "<p style='color:green;'>$message</p>"; ?>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

        <!-- User Form -->
        <h3><?php echo $editMode ? "Edit User" : "Register User"; ?></h3>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($editUsername); ?>" required>
            <?php if (!$editMode): ?>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <?php endif; ?>
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="Admin" <?php if ($editRole === "Admin") echo "selected"; ?>>Admin</option>
                <option value="Staff" <?php if ($editRole === "Staff") echo "selected"; ?>>Staff</option>
                <option value="Customer" <?php if ($editRole === "Customer") echo "selected"; ?>>Customer</option>
            </select>
            <?php if ($editMode): ?>
                <input type="hidden" name="user_id" value="<?php echo $editId; ?>">
                <button class="btn" type="submit" name="update_user">Update User</button>
                <a class="btn" href="admin_dashboard.php">Cancel</a>
            <?php else: ?>
                <button class="btn" type="submit" name="register_user">Register</button>
            <?php endif; ?>
        </form>

        <!-- Booking Form -->
        <h3><?php echo $editBookingMode ? "Edit Booking" : "Register New Booking"; ?></h3>
        <form method="POST">
            <input type="text" name="room_id" placeholder="Room ID" value="<?php echo $editBookingMode ? htmlspecialchars($editBooking['RoomID']) : ''; ?>" required>
            <input type="text" name="guest_info" placeholder="Guest Info" value="<?php echo $editBookingMode ? htmlspecialchars($editBooking['GuestInfo']) : ''; ?>" required>
            <input type="text" name="contact_number" placeholder="Contact Number" value="<?php echo $editBookingMode ? htmlspecialchars($editBooking['ContactNumber']) : ''; ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?php echo $editBookingMode ? htmlspecialchars($editBooking['Email']) : ''; ?>" required>
            <input type="date" name="check_in" value="<?php echo $editBookingMode ? htmlspecialchars($editBooking['CheckIn']) : ''; ?>" required>
            <input type="date" name="check_out" value="<?php echo $editBookingMode ? htmlspecialchars($editBooking['CheckOut']) : ''; ?>" required>
            <?php if ($editBookingMode): ?>
                <input type="hidden" name="booking_id" value="<?php echo $editBooking['BookingID']; ?>">
                <button class="btn" type="submit" name="update_booking">Update Booking</button>
                <a class="btn" href="admin_dashboard.php">Cancel</a>
            <?php else: ?>
                <button class="btn" type="submit" name="register_booking">Register Booking</button>
            <?php endif; ?>
        </form>

        <!-- User Table -->
        <h3>Registered Users</h3>
        <table>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['UserID']; ?></td>
                    <td><?php echo htmlspecialchars($user['Username']); ?></td>
                    <td><?php echo htmlspecialchars($user['Role']); ?></td>
                    <td>
                        <a class="btn btn-action" href="?edit_user=<?php echo $user['UserID']; ?>">Edit</a>
                        <a class="btn btn-action" href="?delete_user=<?php echo $user['UserID']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- Booking Table -->
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
                <th>Actions</th>
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
                    <td>
                        <a class="btn btn-action" href="?edit_booking=<?php echo $booking['BookingID']; ?>">Edit</a>
                        <a class="btn btn-action" href="?delete_booking=<?php echo $booking['BookingID']; ?>" onclick="return confirm('Are you sure you want to delete this booking?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
