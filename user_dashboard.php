<?php  
session_start();  
include 'db.php';  

// Access control for Customer only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Customer') {
    header("Location: bookings.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Room Selection</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            height: 100vh;
            background-color: #f4f4f4;
            overflow: hidden;
            background-image: url('images/hotel.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
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
        }

        .sidebar h2 {
            text-align: center;
            margin: 0 0 20px;
            font-size: 24px;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 12px;
            margin: 10px 0;
            text-decoration: none;
            border-radius: 4px;
            background-color:rgb(90, 84, 84);
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
            padding: 20px;
            background-color: rgba(65, 54, 54, 0.9);
            overflow-y: auto;
            border-radius: 10px;
            margin-left: auto;
        }

        .header {
            background-color: #d32828;
            color: white;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .room-container {
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .room {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            margin: 15px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: transform 0.2s;
            width: 300px;
            height: 350px;
        }

        .room:hover {
            transform: scale(1.05);
        }

        .room img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .available {
            color: green;
            font-weight: bold;
        }

        .unavailable {
            color: red;
            font-weight: bold;
        }

        .dot {
            height: 12px;
            width: 12px;
            border-radius: 50%;
            display: inline-block;
        }

        .dot.green {
            background-color: green;
        }

        .dot.red {
            background-color: red;
        }

        select, button {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
            max-width: 250px;
        }

        button {
            background-color: gold;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: rgb(197, 56, 56);
        }

        h2 {
            text-align: center;
            color: #fff;
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
        <h1>Room Selection</h1>
        <span>Welcome, <?php echo htmlspecialchars($username); ?> (Customer)</span>
    </div>

    <div class="room-container">
        <h2>Select a Room to Book</h2>
        <label for="roomFilter" style="color: #fff;">Filter by Room Type:</label>
        <select id="roomFilter" onchange="filterRooms()">
            <option value="all">All</option>
            <option value="Deluxe Room">Deluxe Room</option>
            <option value="Single Room">Single Room</option>
            <option value="Guest House">Guest House</option>
        </select>
        <div id="roomList" class="room-container"></div>
    </div>
</div>

<script>
    const rooms = [
        { id: 1, type: "Deluxe Room", available: false, img: "images/delux1.jpg" },
        { id: 2, type: "Deluxe Room", available: true, img: "images/delux2.jpg" },
        { id: 3, type: "Deluxe Room", available: true, img: "images/delux3.jpg" },
        { id: 4, type: "Deluxe Room", available: false, img: "images/delux3.jpg" },
        { id: 5, type: "Deluxe Room", available: false, img: "images/delux1.jpg" },

        { id: 6, type: "Single Room", available: true, img: "images/single1.jpg" },
        { id: 7, type: "Single Room", available: true, img: "images/single2.jpg" },
        { id: 8, type: "Single Room", available: false, img: "images/single3.jpg" },
        { id: 9, type: "Single Room", available: true, img: "images/single4.jpg" },
        { id: 10, type: "Single Room", available: true, img: "images/single1.jpg" },

        { id: 11, type: "Guest House", available: true, img: "images/guest1.jpg" },
        { id: 12, type: "Guest House", available: false, img: "images/guest2.jpeg" },
        { id: 13, type: "Guest House", available: true, img: "images/guest3.jpg" },
        { id: 14, type: "Guest House", available: true, img: "images/guest4.jpeg" },
        { id: 15, type: "Guest House", available: false, img: "images/guest5.jpg" }
    ];

    function loadRooms(type = "all") {
        const roomList = document.getElementById("roomList");
        roomList.innerHTML = "";
        rooms.forEach(room => {
            if (type === "all" || room.type === type) {
                const roomDiv = document.createElement("div");
                roomDiv.className = "room";
                roomDiv.innerHTML = `
                    <img src="${room.img}" alt="${room.type}">
                    <div>
                        <p><strong>${room.type}</strong> - 
                        ${room.available ? "<span class='available'>Available</span>" : "<span class='unavailable'>Not Available</span>"}
                        <span class='dot ${room.available ? "green" : "red"}'></span></p>
                        <button onclick="selectRoom(${room.id}, '${room.type}', ${room.available})">Select</button>
                    </div>
                `;
                roomList.appendChild(roomDiv);
            }
        });
    }

    function filterRooms() {
        const filter = document.getElementById("roomFilter").value;
        loadRooms(filter);
    }

    function selectRoom(roomId, roomType, isAvailable) {
        if (!isAvailable) {
            alert("This room is already booked.");
            return;
        }
        window.location.href = `reservation.php?room_id=${roomId}&room_type=${encodeURIComponent(roomType)}`;
    }

    loadRooms();
</script>

</body>
</html>
