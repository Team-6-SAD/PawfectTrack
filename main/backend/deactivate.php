<?php
session_start();

// Check if the deactivation checkbox is checked

    // Include your database connection file
    require_once 'pawfect_connect.php';

    // Get the AdminID from the session
    $adminID = $_SESSION['adminID'];

    // Prepare and execute the SQL query to delete admin information
    $sqlDeleteInfo = "DELETE FROM admininformation WHERE AdminID = ?";
    $stmtDeleteInfo = mysqli_prepare($conn, $sqlDeleteInfo);
    mysqli_stmt_bind_param($stmtDeleteInfo, "i", $adminID);
    mysqli_stmt_execute($stmtDeleteInfo);

    // Prepare and execute the SQL query to delete admin credentials
    $sqlDeleteCredentials = "DELETE FROM admincredentials WHERE AdminID = ?";
    $stmtDeleteCredentials = mysqli_prepare($conn, $sqlDeleteCredentials);
    mysqli_stmt_bind_param($stmtDeleteCredentials, "i", $adminID);
    mysqli_stmt_execute($stmtDeleteCredentials);

    // Close prepared statements
    mysqli_stmt_close($stmtDeleteInfo);
    mysqli_stmt_close($stmtDeleteCredentials);

    // Close database connection
    mysqli_close($conn);
    unset($_SESSION['admin']);
    unset($_SESSION['adminID']);
    // Redirect the user to a relevant page after deactivation
    header("Location: ../Admin Login.php");
    exit(); // Terminate the script

?>
