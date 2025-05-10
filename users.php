<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #b8860b;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff8dc;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"],
        select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
        }

        input[type="submit"] {
            background-color: gold;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #d4af37;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        table, th, td {
            border: 1px solid #d4af37;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: gold;
            color: white;
        }

        .actions a {
            text-decoration: none;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            margin: 0 3px;
        }

        .edit {
            background-color: #28a745;
        }

        .delete {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Management</h2>

        <?php
        // Handle delete
        if (isset($_GET['delete'])) {
            $delete_id = $_GET['delete'];
            $conn->exec("DELETE FROM Users WHERE UserID = $delete_id");
            echo "<p style='color:red;'>User deleted successfully.</p>";
        }

        // Handle edit
        $edit_mode = false;
        $edit_id = '';
        $edit_username = '';
        $edit_role = '';
        if (isset($_GET['edit'])) {
            $edit_id = $_GET['edit'];
            $stmt = $conn->query("SELECT * FROM Users WHERE UserID = $edit_id");
            $user = $stmt->fetch();
            if ($user) {
                $edit_mode = true;
                $edit_username = $user['Username'];
                $edit_role = $user['Role'];
            }
        }

        // Handle add/update
        if (isset($_POST['save_user'])) {
            $username = $_POST['username'];
            $role = $_POST['role'];

            if (!empty($_POST['user_id'])) {
                // Update user
                $user_id = $_POST['user_id'];
                if (!empty($_POST['password'])) {
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $conn->exec("UPDATE Users SET Username='$username', Password='$password', Role='$role' WHERE UserID=$user_id");
                } else {
                    $conn->exec("UPDATE Users SET Username='$username', Role='$role' WHERE UserID=$user_id");
                }
                echo "<p style='color:green;'>User updated successfully!</p>";
            } else {
                // Add new user
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $conn->exec("INSERT INTO Users (Username, Password, Role) VALUES ('$username', '$password', '$role')");
                echo "<p style='color:green;'>User added successfully!</p>";
            }
        }
        ?>

        <!-- Form -->
        <form method="POST">
            <input type="hidden" name="user_id" value="<?php echo $edit_mode ? $edit_id : ''; ?>">
            <div>
                <label>Username:</label>
                <input type="text" name="username" value="<?php echo $edit_username; ?>" required>
            </div>
            <div>
                <label>Password: <?php echo $edit_mode ? "(Leave blank to keep current password)" : ""; ?></label>
                <input type="password" name="password" <?php echo $edit_mode ? '' : 'required'; ?>>
            </div>
            <div>
                <label>Role:</label>
                <select name="role">
                    <option value="Staff" <?php echo ($edit_role === 'Staff') ? 'selected' : ''; ?>>Staff</option>
                    <option value="Customer" <?php echo ($edit_role === 'Customer') ? 'selected' : ''; ?>>Customer</option>
                </select>
            </div>
            <div>
                <input type="submit" name="save_user" value="<?php echo $edit_mode ? 'Update User' : 'Add User'; ?>">
            </div>
        </form>

        <!-- User Table -->
        <?php
        $stmt = $conn->query("SELECT * FROM Users ORDER BY UserID DESC");
        echo "<table>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>";
        while ($row = $stmt->fetch()) {
            echo "<tr>
                    <td>{$row['UserID']}</td>
                    <td>{$row['Username']}</td>
                    <td>{$row['Role']}</td>
                    <td class='actions'>
                        <a href='?edit={$row['UserID']}' class='edit'>Edit</a>
                        <a href='?delete={$row['UserID']}' class='delete' onclick=\"return confirm('Are you sure?');\">Delete</a>
                    </td>
                  </tr>";
        }
        echo "</table>";
        ?>
    </div>
</body>
</html>
