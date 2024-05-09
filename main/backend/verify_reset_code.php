<?php
session_start();

// Check if the reset code is entered and stored in the session
if(isset($_POST['resetCode']) && isset($_SESSION['reset_code'])) {
    // Retrieve the entered code and the code stored in the session
    $enteredCode = $_POST['resetCode'];
    $storedCode = $_SESSION['reset_code'];

    // Verify if the entered code matches the stored code
    if($enteredCode === $storedCode) {
        // Code is correct, display the modal asking for the password
        // Set a session variable to indicate that code verification is successful
        $_SESSION['code_verified'] = true;
    } else {
        // Code is incorrect, display an error message
        $_SESSION['error_message_code'] = 'Invalid reset code.';
    }
} else {
    $_SESSION['error_message'] = 'Reset code not provided.';
}

// Redirect back to the previous page
header("Location: Admin Login.php");
exit();
?>
