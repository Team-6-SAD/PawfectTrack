<?php
session_start();

// Check if the deactivation checkbox is checked

// Include your database connection file
require_once '../../backend/pawfect_connect.php';

// Get the UserID from the session (corrected variable name from $userID to $adminID)
$userID = $_SESSION['userID'];

// Prepare and execute the SQL query to delete admin credentials
$sqlDeleteCredentials = "DELETE FROM usercredentials WHERE UserID = ?";
$stmtDeleteCredentials = mysqli_prepare($conn, $sqlDeleteCredentials);

// Check if prepare() succeeded
if ($stmtDeleteCredentials === false) {
    die('MySQL prepare error: ' . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmtDeleteCredentials, "i", $userID);
mysqli_stmt_execute($stmtDeleteCredentials);

// Close prepared statement
mysqli_stmt_close($stmtDeleteCredentials);

// Clear session variables
$_SESSION = array(); // Clear all session variables

// Destroy the session
session_destroy();

// Close database connection
mysqli_close($conn);

// Redirect the user to a relevant page after deactivation
header("Location: ../Patient Login.php");
exit(); // Terminate the script
?>
