<?php
session_start();

// Check if the deactivation checkbox is checked

    // Include your database connection file
    require_once 'pawfect_connect.php';

    // Get the AdminID from the session
    $userID = $_SESSION['userID'];

    // Prepare and execute the SQL query to delete admin credentials
    $sqlDeleteCredentials = "DELETE FROM usercredentials WHERE UserID = ?";
    $stmtDeleteCredentials = mysqli_prepare($conn, $sqlDeleteCredentials);
    mysqli_stmt_bind_param($stmtDeleteCredentials, "i", $adminID);
    mysqli_stmt_execute($stmtDeleteCredentials);

    // Close prepared statements
    mysqli_stmt_close($stmtDeleteInfo);
    mysqli_stmt_close($stmtDeleteCredentials);

    // Close database connection
    mysqli_close($conn);

    // Redirect the user to a relevant page after deactivation
    header("Location: ../Patient Login.php");
    exit(); // Terminate the script

?>
