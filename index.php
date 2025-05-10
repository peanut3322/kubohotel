<?php  
session_start();  
include 'db.php';  

if ($_SERVER['REQUEST_METHOD'] == 'POST') {  
    $username = $_POST['username'];  
    $password = $_POST['password'];  

    $stmt = $conn->prepare("SELECT * FROM Users WHERE Username = ?");  
    $stmt->execute([$username]);  
    $user = $stmt->fetch();  

    if ($user && password_verify($password, $user['Password'])) {  
        $_SESSION['user_id'] = $user['UserID'];  
        $_SESSION['username'] = $user['Username'];  
        $_SESSION['role'] = $user['Role'];  
        $_SESSION['message'] = "Successfully logged in!";

        if ($user['Role'] === 'Customer') {
            header("Location: user_dashboard.php");
        } else {
            header("Location: admin_dashboard.php");
        }
        exit;  
    } else {  
        $error = "Invalid username or password.";  
    }  
}  
?>  

<!DOCTYPE html>  
<html lang="en">  
<head>  
<meta charset="UTF-8" />  
<title>Login - KUBO HOTEL</title>  
<style>  
* {
    box-sizing: border-box;
}

body, html {
    margin: 0;  
    padding: 0;  
    font-family: Arial, sans-serif;  
    height: 100vh;  
    overflow: hidden;
}

.slideshow-container {
    position: fixed;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
}

.slide {
    position: absolute;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    opacity: 0;
    animation: fade 18s infinite;
}

.slide:nth-child(1) { background-image: url('images/hotel.jpg'); animation-delay: 0s; }
.slide:nth-child(2) { background-image: url('images/hotel1.jpg'); animation-delay: 6s; }
.slide:nth-child(3) { background-image: url('images/hotel2.jpg'); animation-delay: 12s; }

@keyframes fade {
    0%, 100% { opacity: 0; }
    10%, 30% { opacity: 1; }
    40%, 90% { opacity: 0; }
}

.login-container {  
    display: flex;  
    width: 85%;  
    max-width: 950px;  
    height: 520px;  
    border-radius: 15px;  
    overflow: hidden;  
    box-shadow: 0 0 20px rgba(0,0,0,0.7);  
    background-color: rgba(255,255,255,0.1);  
    backdrop-filter: blur(8px);  
    margin: auto;
    position: relative;
    top: 50%;
    transform: translateY(-50%);
}  

.logo-box {  
    flex: 1;  
    background-color: rgba(50, 50, 50, 0.8);  
    display: flex;  
    flex-direction: column;  
    justify-content: center;  
    align-items: center;  
    padding: 20px;  
    text-align: center;  
}  

.logo-box h1 {  
    font-family: 'Times New Roman', serif;  
    font-size: 2.8em;  
    color: white;  
    margin: 0;  
    animation: slide-in 3s ease-in-out infinite alternate;  
}  

@keyframes slide-in {  
    from { transform: translateY(-10px); opacity: 0.7; }  
    to { transform: translateY(10px); opacity: 1; }  
}  

.logo-box img {  
    max-width: 260px;  
    width: 100%;  
    height: auto;  
    margin-top: 20px;  
    border-radius: 10px;  
}  

.login-form {  
    flex: 1;  
    background-color: rgba(0, 0, 0, 0.7);  
    padding: 40px 30px;  
    display: flex;  
    flex-direction: column;  
    justify-content: center;  
}  

.login-form h2 {  
    font-size: 30px;  
    color: #fff;  
    text-align: center;  
    margin-bottom: 20px;  
}  

input[type=text], input[type=password] {  
    width: 100%;  
    padding: 14px;  
    margin: 12px 0;  
    border-radius: 8px;  
    border: none;  
    font-size: 16px;  
}  

button {  
    width: 100%;  
    padding: 14px;  
    margin-top: 20px;  
    background-color: #0d6efd;  
    color: #fff;  
    border: none;  
    border-radius: 8px;  
    font-size: 18px;  
    cursor: pointer;  
    transition: background 0.3s;  
}  

button:hover {  
    background-color: #0069d9;  
}  

p {  
    margin-top: 15px;  
    text-align: center;  
    color: #ccc;  
}  

p a {  
    color: gold;  
    text-decoration: none;  
}  

@media(max-width: 768px){  
    .login-container {  
        flex-direction: column;  
        height: auto;  
    }  
}  
</style>  
</head>  
<body>  

<div class="slideshow-container">
    <div class="slide"></div>
    <div class="slide"></div>
    <div class="slide"></div>
</div>

<div class="login-container">  
    <div class="logo-box">  
        <h1>J&A COMPANY</h1>  
        <img src="kubo.png" alt="KUBO Logo" />  
    </div>  

    <div class="login-form">  
        <h2>Login</h2>  
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>  

        <form method="POST" action="">  
            <input type="text" name="username" placeholder="Username" required>  
            <input type="password" name="password" placeholder="Password" required>  
            <button type="submit">LOGIN</button>  
        </form>  
        <p>No account? Register <a href="register.php">here</a></p>  
    </div>  
</div>  
</body>  
</html>
