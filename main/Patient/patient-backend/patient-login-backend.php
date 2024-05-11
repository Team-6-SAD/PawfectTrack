<?php
require_once '../../backend/pawfect_connect.php';
session_start(); // Start the session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM usercredentials WHERE Username = ? AND ActiveStatus = 'Active'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $hashedPassword = $row['Password'];
        $userID = $row['UserID'];
        // Verify password
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user'] = true;
            $_SESSION['userID'] = $userID;
            header("Location: ../patientdashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid username or password";
            header("Location: ../Patient Login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Invalid username or password";
        header("Location: ../Patient Login.php");
        exit();
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}

// Redirect back to the login page
header("Location: ../Patient Login.php");
exit();
?>
