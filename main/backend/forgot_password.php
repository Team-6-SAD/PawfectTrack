<?php
session_start();

// Include your database connection file
require_once 'pawfect_connect.php';
require_once 'src/Exception.php';
require_once 'src/PHPMailer.php';
require_once 'src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Email configuration
$emailHost = 'smtp.gmail.com';
$emailUsername = 'dummyacc0894@gmail.com';
$emailPassword = 'jmqxgzcmremoudtk';
$emailSenderAddress = 'pawfecttrack@gmail.com';
$emailSenderName = 'Dummy Daccount';

// Check if the form was submitted with an email
if(isset($_POST['email'])) {
    // Retrieve the email from the form
    $email = $_POST['email'];

    // Check if the email exists in the database
    $checkEmailQuery = "SELECT * FROM admininformation WHERE email = ?";
    $stmt = mysqli_prepare($conn, $checkEmailQuery);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) > 0) {
        // Email exists, proceed with sending the reset code

        // Generate a random code
        $randomCode = substr(md5(mt_rand()), 0, 8); // Generate a random 8-character code

        // Store the random code in a session variable
        $_SESSION['reset_code'] = $randomCode;
        $_SESSION['reset_code_expiry'] = time() + (30 * 60);

        // Send the code via email
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF; // Enable verbose debug output
            $mail->isSMTP(); // Send using SMTP
            $mail->Host       = $emailHost; // Set the SMTP server to send through
            $mail->SMTPAuth   = true; // Enable SMTP authentication
            $mail->Username   = $emailUsername; // SMTP username
            $mail->Password   = $emailPassword; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587; // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            // Recipients
            $mail->setFrom($emailSenderAddress, $emailSenderName);
            $mail->addAddress($email); // Add a recipient

            $mail->isHTML(false); // Set email format to HTML
            $mail->Subject = 'Password Reset Code';
            $mail->Body    = "Dear User,\r\n";
            $mail->Body   .= "We received a request to reset your password for your account on PawfectTrack. If you did not request a password reset, please ignore this email.\r\n";
            $mail->Body   .= "To reset your password, please use the reset code below:\r\n\r\n";
            $mail->Body   .= "{$randomCode}\r\n";
            $mail->Body   .= "This reset code will be available for 30 minutes only for security reasons.\r\n";
            $mail->Body   .= "If you have any questions or need further assistance, please contact our team at pawfecttrack@gmail.com\r\n\r\n";
            $mail->Body   .= "Thank you,\r\n";
            $mail->Body   .= "PawfectTrack Team";

            $mail->send();

            $_SESSION['success_message'] = 'Password reset code sent to your email.';
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Error sending password reset code: ' . $e->getMessage();
        }

        $_SESSION['reset_email'] = $email;
        header("Location: ../Admin Login.php");
        exit();
    } else {
        // Email does not exist
        $_SESSION['error_message'] = 'Email does not exist.';
        header("Location: ../Admin Login.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = 'Email not provided.';
    // Redirect back to the previous page
    header("Location: ../Admin Login.php");
    exit();
}
?>
