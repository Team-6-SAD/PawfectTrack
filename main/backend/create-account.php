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
$emailSenderAddress = 'dummyacc0894@gmail.com';
$emailSenderName = 'Dummy Daccount';

// Check if the patientID is set in the URL
if(isset($_GET['patientID'])) {
    // Retrieve the patientID from the URL
    $patientID = $_GET['patientID'];

    // Check if an account already exists for the patient
    $sql_check = "SELECT * FROM usercredentials WHERE PatientID = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "i", $patientID);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    // If an account already exists, redirect back to the patient details page
    if(mysqli_num_rows($result_check) > 0) {
        $_SESSION['account-exists'] = 'Account creation failed.';
        header("Location: ../patientdetails-profile.php?patientID=$patientID");
        exit();
    }

    // Fetch email from the contactinformation table
    $sql_email = "SELECT EmailAddress FROM contactinformation WHERE PatientID = ?";
    $stmt_email = mysqli_prepare($conn, $sql_email);
    mysqli_stmt_bind_param($stmt_email, "i", $patientID);
    mysqli_stmt_execute($stmt_email);
    $result_email = mysqli_stmt_get_result($stmt_email);

    if(mysqli_num_rows($result_email) > 0) {
        $row_email = mysqli_fetch_assoc($result_email);
        $recipientEmail = $row_email['EmailAddress'];

        // Generate a random username and password
        $username = 'patient_' . $patientID;
        $password = bin2hex(random_bytes(8)); // Generate a random password

        // Hash the password using PHP's built-in password_hash function
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and execute the SQL query to insert the username and hashed password into the usercredentials table
        $sql_insert = "INSERT INTO usercredentials (PatientID, Username, Password) VALUES (?, ?, ?)";
        $stmt_insert = mysqli_prepare($conn, $sql_insert);
        mysqli_stmt_bind_param($stmt_insert, "iss", $patientID, $username, $hashedPassword);
        mysqli_stmt_execute($stmt_insert);

        // Check if the insertion was successful
        if(mysqli_stmt_affected_rows($stmt_insert) > 0) {
            // Account created successfully, send an email with account details
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
                $mail->addAddress($recipientEmail); // Add a recipient

                // Content
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = 'Account Created Successfully';
                $mail->Body    = 'Dear User, <br><br>Your account has been successfully created.<br>Username: ' . $username . '<br>Password: ' . $password;

                $mail->send();
                $_SESSION['success_message'] = 'Account created successfully. Email sent with account details.';
                
                // Redirect to the patient details page
                header("Location: ../patientdetails-profile.php?patientID=$patientID");
            } catch (Exception $e) {
                echo "Account created successfully, but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            // Error handling: Account creation failed
            $_SESSION['failed-message'] = 'Account creation failed.';
        }

        // Close the statements
        mysqli_stmt_close($stmt_insert);
    } else {
        echo 'Error: No email found for PatientID ' . $patientID;
    }

    // Close the statements
    mysqli_stmt_close($stmt_check);
    mysqli_stmt_close($stmt_email);

    // Close the database connection
    mysqli_close($conn);
} else {
    // Error handling: PatientID not set in the URL
    echo "PatientID not set!";
}
?>
