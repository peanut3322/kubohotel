<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "hotel_management";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully"; // Optional for testing
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
