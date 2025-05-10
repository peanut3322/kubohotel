<?php
session_start();
include 'db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: bookings.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Get room ID
$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
if ($room_id === 0) {
    echo "Invalid room selection.";
    exit;
}

// Fetch room details
try {
    $stmt = $pdo->prepare("SELECT * FROM Rooms WHERE RoomID = ?");
    $stmt->execute([$room_id]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$room) {
        echo "Room not found.";
        exit;
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

// Handle Admin Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $role === 'Admin') {
    $room_type = $_POST['room_type'];
    $bedding = $_POST['bedding'];
    $availability = $_POST['availability'];

    try {
        $stmt = $pdo->prepare("UPDATE Rooms SET RoomType = ?, Bedding = ?, Availability = ? WHERE RoomID = ?");
        $stmt->execute([$room_type, $bedding, $availability, $room_id]);
        $room['RoomType'] = $room_type;
        $room['Bedding'] = $bedding;
        $room['Availability'] = $availability;
        $message = "<p style='color:green;'>Room updated successfully.</p>";
    } catch (PDOException $e) {
        $message = "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
    }
}

// Handle Customer Reservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $role === 'Customer') {
    $guest_name = $_POST['guest_name'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    $checkin_date = $_POST['checkin_date'];
    $checkout_date = $_POST['checkout_date'];
    $guest_count = $_POST['guest_count'];

    try {
        $sql = "INSERT INTO reservations (room_id, guest_name, contact_number, email, checkin_date, checkout_date, guest_count, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$room_id, $guest_name, $contact_number, $email, $checkin_date, $checkout_date, $guest_count]);
        $message = "<p style='color:green;'>Reservation submitted successfully!</p>";
    } catch (PDOException $e) {
        $message = "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Room Details</title>
    <style>
        /* Basic layout styling omitted for brevity – reuse your existing styles here */
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        form { background: #fff; padding: 20px; max-width: 500px; margin: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        input, select, button { width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 4px; border: 1px solid #ccc; }
        button { background: gold; color: #fff; border: none; cursor: pointer; }
        button:hover { background: #d4af37; }
    </style>
</head>
<body>

<h2 style="text-align:center;">Room: <?= htmlspecialchars($room['RoomType']) ?> (ID: <?= $room_id ?>)</h2>
<?php if (isset($message)) echo $message; ?>

<?php if ($role === 'Admin'): ?>
    <form method="POST">
        <h3>Edit Room Details</h3>
        <label>Room Type:</label>
        <input type="text" name="room_type" value="<?= htmlspecialchars($room['RoomType']) ?>" required>

        <label>Bedding:</label>
        <input type="text" name="bedding" value="<?= htmlspecialchars($room['Bedding']) ?>" required>

        <label>Availability:</label>
        <select name="availability" required>
            <option value="Available" <?= $room['Availability'] === 'Available' ? 'selected' : '' ?>>Available</option>
            <option value="Booked" <?= $room['Availability'] === 'Booked' ? 'selected' : '' ?>>Booked</option>
        </select>

        <button type="submit">Update Room</button>
    </form>
<?php elseif ($role === 'Customer'): ?>
    <form method="POST">
        <h3>Reserve This Room</h3>
        <label>Guest Name:</label>
        <input type="text" name="guest_name" required>

        <label>Contact Number:</label>
        <input type="text" name="contact_number" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Check-In Date:</label>
        <input type="date" name="checkin_date" required>

        <label>Check-Out Date:</label>
        <input type="date" name="checkout_date" required>

        <label>Number of Guests:</label>
        <select name="guest_count">
            <?php for ($i = 1; $i <= 4; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
        </select>

        <button type="submit">Confirm Reservation</button>
    </form>
<?php else: ?>
    <p>You do not have access to this page.</p>
<?php endif; ?>

</body>
</html>
