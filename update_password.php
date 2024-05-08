<?php
session_start();
require_once 'pawfect_connect.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = $_SESSION['reset_email'];
    $newPassword = $_POST['newPassword'];
    $confirmNewPassword = $_POST['confirmNewPassword'];

    // Validate form data (e.g., check for empty fields, minimum length)
    // Implement JavaScript validation for better user experience

    // Check if the new password matches the confirmation
    if ($newPassword != $confirmNewPassword) {
        $_SESSION['error_no_match'] = "Passwords do not match.";
        header("Location: Admin Login.php");
        exit();
    }

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Find the AdminID using the provided email
    $adminIDQuery = "SELECT AdminID FROM admininformation WHERE email = ?";
    $stmt = mysqli_prepare($conn, $adminIDQuery);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $adminID);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if (!$adminID) {
        // Email not found in admininformation table
        $_SESSION['error'] = "Email not found.";
        header("Location: Admin Login.php");
        exit();
    }

    // Update the password in the admincredentials table using the found AdminID
    $updateSql = "UPDATE admincredentials SET AdminPassword = ? WHERE AdminID = ?";
    $stmt = mysqli_prepare($conn, $updateSql);
    mysqli_stmt_bind_param($stmt, "si", $hashedPassword, $adminID);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        // Password updated successfully
        $_SESSION['success_message_password'] = "Password updated successfully.";
        header("Location: Admin Login.php"); // Redirect to login page or wherever appropriate
        exit();
    } else {
        // Failed to update password
        $_SESSION['error'] = "Failed to update password.";
        header("Location: Admin Login.php");
        exit();
    }
}
?>
