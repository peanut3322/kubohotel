<?php
session_start();
include 'db.php';

// Access Control
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Staff')) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            height: 100vh;
        }
        .sidebar {
            width: 220px;
            background-color: #999;
            padding: 15px;
        }
        .sidebar h2 {
            color: #fff;
            text-align: center;
        }
        .sidebar a {
            display: block;
            padding: 10px;
            margin-top: 10px;
            background-color: #aaa;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
        }
        .main {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }
        .header {
            background-color: #c49c55;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            border-radius: 4px;
            color: #fff;
        }
        h1 {
            margin: 0;
        }
        .dashboard-links {
            margin-top: 30px;
        }
        .dashboard-links a {
            display: inline-block;
            margin: 10px;
            padding: 12px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .dashboard-links a:hover {
            background-color: #45a049;
        }
        .logout-form {
            display: inline;
        }
        .logout-button {
            background: #d9534f;
            color: #fff;
            padding: 10px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .logout-button:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>


<div class="main">
    <div class="header">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <div>
            <strong><?php echo $_SESSION['role']; ?></strong>
            <form method="post" action="logout.php" class="logout-form">
                <button type="submit" class="logout-button">Logout</button>
            </form>
        </div>
    </div>

    <div class="dashboard-links">
        <h2>Admin Tools</h2>
        <a href="rooms.php">Manage Rooms</a>
        <a href="bookings.php">Manage Bookings</a>
    </div>
</div>

</body>
</html>
