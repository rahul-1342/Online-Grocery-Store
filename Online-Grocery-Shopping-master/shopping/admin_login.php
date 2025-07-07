<?php
session_start();
error_reporting(0);
include('includes/config.php'); // Ensure this file connects to your database

// Admin login logic
if(isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Hash the password

    // Query to check admin credentials
    $query = mysqli_query($con, "SELECT * FROM admin WHERE username='$username' AND password='$password'");
    $num = mysqli_fetch_array($query);

    if($num > 0) {
        $_SESSION['admin_login'] = $username; // Store session variable
        header("location: admin_dashboard.php"); // Redirect to admin dashboard
        exit();
    } else {
        $_SESSION['errmsg'] = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Admin Login</h2>
        <form method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" name="login">Login</button>
            <span style="color:red;">
                <?php echo htmlentities($_SESSION['errmsg']); ?>
                <?php $_SESSION['errmsg'] = ""; ?>
            </span>
        </form>
    </div>
</body>
</html>
