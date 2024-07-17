<?php
session_start();

// Check if the 'admin' session variable is not set or is false (user not logged in)
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true || !isset($_SESSION['adminID'])) {
    // Redirect the user to the login page
    header("Location: ../Admin Login.php");
    exit(); // Terminate the script
}

// Include your database connection file
require_once 'pawfect_connect.php';

// Get the AdminID from the session
$adminID = $_SESSION['adminID'];

// Check if a picture file is uploaded
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    // Define allowed file types
    $allowedFileTypes = ['image/jpeg', 'image/jpg', 'image/png'];

    // Get the file type
    $fileType = mime_content_type($_FILES['profile_picture']['tmp_name']);

    // Check if the file type is allowed
    if (in_array($fileType, $allowedFileTypes)) {
        // Define a directory to store uploaded files
        $uploadDirectory = '../uploads/';

        // Generate a unique filename for the uploaded picture
        $fileName = basename($_FILES['profile_picture']['name']);
        $targetFilePath = $uploadDirectory . $fileName;

        // Move the uploaded file to the specified directory
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFilePath)) {
            // File upload successful, update the picture path in the database
            $sqlUpdatePicture = "UPDATE admininformation SET adminphoto = ? WHERE AdminID = ?";
            $stmtUpdatePicture = mysqli_prepare($conn, $sqlUpdatePicture);
            mysqli_stmt_bind_param($stmtUpdatePicture, "si", $fileName, $adminID);
            
            if (mysqli_stmt_execute($stmtUpdatePicture)) {
                $_SESSION['success_message'] = "Profile picture updated successfully!";
            } else {
                $_SESSION['error_message'] = "Error updating profile picture: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmtUpdatePicture);
        } else {
            $_SESSION['error_message'] = "Error uploading profile picture.". mysqli_error($conn);
        }
    } else {
        $_SESSION['error_message'] = "Invalid file type. Only JPG and JPEG files are allowed.";
        header("Location: ../admin-settings.php");
        exit();
        
    }
}

// Check if form data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $firstName = isset($_POST['fName']) ? $_POST['fName'] : '';
    $middleName = isset($_POST['mName']) ? $_POST['mName'] : '';
    $lastName = isset($_POST['lName']) ? $_POST['lName'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $phoneNumber = isset($_POST['phoneNumber']) ? $_POST['phoneNumber'] : '';

    // Prepare and execute SQL query to update admin information
    $sql = "UPDATE admininformation SET ";
    $params = array();
    $types = '';

    // Build the SQL query dynamically based on which fields are not empty
    if (!empty($firstName)) {
        $sql .= "firstname=?, ";
        $params[] = $firstName;
        $types .= 's';
    }
    if (!empty($middleName)) {
        $sql .= "middlename=?, ";
        $params[] = $middleName;
        $types .= 's';
    } else {
        // If middle name is empty, add NULL
        $sql .= "middlename=NULL, ";
    }
    if (!empty($lastName)) {
        $sql .= "lastname=?, ";
        $params[] = $lastName;
        $types .= 's';
    }
    if (!empty($email)) {
        $sql .= "email=?, ";
        $params[] = $email;
        $types .= 's';
    }
    if (!empty($phoneNumber)) {
        $sql .= "phonenumber=?, ";
        $params[] = $phoneNumber;
        $types .= 's';
    }

    // Remove the trailing comma and space from the SQL query
    $sql = rtrim($sql, ", ");

    // Check if any fields are being updated before adding the WHERE clause
    if (!empty($params) || isset($_FILES['profile_picture'])) {
        $sql .= " WHERE AdminID=?";
        // Append the AdminID to the parameters array
        $params[] = $adminID;
        $types .= 'i';

        // Prepare the SQL statement for updating admin information
        $stmt = mysqli_prepare($conn, $sql);

        // Bind parameters for updating admin information
        mysqli_stmt_bind_param($stmt, $types, ...$params);

        // Execute the statement for updating admin information
        if (mysqli_stmt_execute($stmt)) {
            // Admin information updated successfully
            $_SESSION['success_message'] = "Information updated successfully!";
        } else {
            // Error updating admin information
            $_SESSION['error_message'] = "Error updating admin information: " . mysqli_error($conn);
        }

        // Close the statement for updating admin information
        mysqli_stmt_close($stmt);
    }

    // Update the username if it's provided
    if (!empty($_POST['username'])) {
        $username = $_POST['username'];
        $sqlUsername = "UPDATE admincredentials SET AdminUsername = ? WHERE AdminID = ?";
        $stmtUsername = mysqli_prepare($conn, $sqlUsername);
        mysqli_stmt_bind_param($stmtUsername, "si", $username, $adminID);
        if (mysqli_stmt_execute($stmtUsername)) {
            $_SESSION['success_message'] = "Information updated successfully!";
        } else {
            $_SESSION['error_message'] = "Error updating username: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmtUsername);
    }

    // Close connection
    mysqli_close($conn);

    // Redirect back to admin-settings page
    header("Location: ../admin-settings.php");
    exit();
} else {
    // If form data is not received via POST method, redirect back to admin-settings page
    header("Location: ../admin-settings.php");
    exit();
}
?>
