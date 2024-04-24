<?php
require_once 'pawfect_connect.php';
session_start(); // Start the session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admincredentials WHERE AdminUsername = '$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $hashedPassword = $row['AdminPassword'];
        $adminID = $row['AdminID'];
        // Verify password
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['admin'] = true;
            $_SESSION['adminID'] = $adminID;
            header("Location: admindashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid username or password";
        }
    } else {
        $_SESSION['error'] = "Invalid username or password";
    }
    mysqli_close($conn);
}

// Redirect back to the login page
header("Location: Admin Login.php");
exit();
?>
