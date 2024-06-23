<?php
require_once 'pawfect_connect.php';
session_start(); // Start the session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT * FROM admincredentials WHERE BINARY AdminUsername = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['AdminPassword'];
        $adminID = $row['AdminID'];
        // Verify password
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['admin'] = true;
            $_SESSION['adminID'] = $adminID;
            header("Location: ../admindashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid username or password";
            header("Location: ../Admin Login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Invalid username or password";
        header("Location: ../Admin Login.php");
        exit();
    }
    $stmt->close();
    $conn->close();
}

// Redirect back to the login page
header("Location: ../Admin Login.php");
exit();
?>