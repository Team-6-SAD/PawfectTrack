<?php
session_start();

// Check if the 'user' session variable is not set or is false (user not logged in)
if (!isset($_SESSION['user']) || $_SESSION['user'] !== true || !isset($_SESSION['userID'])) {
    // Redirect the user to the login page
    header("Location: Patient Login.php");
    exit(); // Terminate the script
}

// Include your database connection file
require_once '../../backend/pawfect_connect.php';

// Get the PatientID from the session
$userID = $_SESSION['userID'];
$stmt = $conn->prepare("SELECT PatientID FROM usercredentials WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
    // Fetch the PatientID
    $row = $result->fetch_assoc();
    $patientID = $row['PatientID'];
} else {
    // User not found or multiple users found (should not happen)
    // Redirect to the login page
    header("Location: Patient Login.php");
    exit(); // Terminate the script
}

// Check if a picture file is uploaded
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    // Define a directory to store uploaded files
    $uploadDirectory = '../../uploads/';

    // Generate a unique filename for the uploaded picture
    $fileName = basename($_FILES['profile_picture']['name']);
    $targetFilePath = $uploadDirectory . $fileName;
    // Move the uploaded file to the specified directory
    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFilePath)) {
        // File upload successful, update the picture path in the database
        $sqlUpdatePicture = "UPDATE patient SET profilepicture = ? WHERE PatientID = ?";
        $stmtUpdatePicture = mysqli_prepare($conn, $sqlUpdatePicture);
        mysqli_stmt_bind_param($stmtUpdatePicture, "si", $fileName, $patientID);

        if (mysqli_stmt_execute($stmtUpdatePicture)) {
            $_SESSION['success_message'] = "Profile picture updated successfully!";
        } else {
            $_SESSION['error_message'] = "Error updating profile picture: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmtUpdatePicture);
    } else {
        $_SESSION['error_message'] = "Error uploading profile picture.";
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

    // Prepare and execute SQL query to update patient information
    $sql = "UPDATE patient SET ";
    $params = array();
    $types = '';

    // Build the SQL query dynamically based on which fields are not empty
    if (!empty($firstName)) {
        $sql .= "FirstName=?, ";
        $params[] = $firstName;
        $types .= 's';
    }
    if (!empty($middleName)) {
        $sql .= "MiddleName=?, ";
        $params[] = $middleName;
        $types .= 's';
    }
    if (!empty($lastName)) {
        $sql .= "LastName=?, ";
        $params[] = $lastName;
        $types .= 's';
    }

    // Remove the trailing comma and space from the SQL query
    $sql = rtrim($sql, ", ");

    // Check if any fields are being updated
    if (!empty($params)) {
        $sql .= " WHERE PatientID=?";
        // Append the PatientID to the parameters array
        $params[] = $patientID;
        $types .= 'i';

        // Prepare the SQL statement for updating patient information
        $stmt = mysqli_prepare($conn, $sql);

        // Bind parameters for updating patient information
        mysqli_stmt_bind_param($stmt, $types, ...$params);

        // Execute the statement for updating patient information
        if (mysqli_stmt_execute($stmt)) {
            // Patient information updated successfully
            $_SESSION['success_message'] = "Information updated successfully!";
        } else {
            // Error updating patient information
            $_SESSION['error_message'] = "Error updating patient information: " . mysqli_error($conn);
        }

        // Close the statement for updating patient information
        mysqli_stmt_close($stmt);
    }

    // Update email and phone in contactinformation table
    if (!empty($email) || !empty($phoneNumber)) {
        $sqlContact = "UPDATE contactinformation SET ";
        $paramsContact = array();
        $typesContact = '';

        if (!empty($email)) {
            $sqlContact .= "EmailAddress=?, ";
            $paramsContact[] = $email;
            $typesContact .= 's';
        }
        if (!empty($phoneNumber)) {
            $sqlContact .= "LineNumber=?, ";
            $paramsContact[] = $phoneNumber;
            $typesContact .= 's';
        }

        // Remove the trailing comma and space from the SQL query
        $sqlContact = rtrim($sqlContact, ", ");
        $sqlContact .= " WHERE PatientID=?";
        // Append the PatientID to the parameters array
        $paramsContact[] = $patientID;
        $typesContact .= 'i';

        // Prepare the SQL statement for updating contact information
        $stmtContact = mysqli_prepare($conn, $sqlContact);

        // Bind parameters for updating contact information
        mysqli_stmt_bind_param($stmtContact, $typesContact, ...$paramsContact);

        // Execute the statement for updating contact information
        if (mysqli_stmt_execute($stmtContact)) {
            // Contact information updated successfully
            $_SESSION['success_message'] = "Contact information updated successfully!";
        } else {
            // Error updating contact information
            $_SESSION['error_message'] = "Error updating contact information: " . mysqli_error($conn);
        }

        // Close the statement for updating contact information
        mysqli_stmt_close($stmtContact);
    }

    // Close connection
    mysqli_close($conn);

    // Redirect back to the patient settings page
    header("Location: ../patient-settings.php");
    exit();
} else {
    // If form data is not received, redirect back to the patient settings page
    header("Location: ../patient-settings.php");
    exit();
}
?>
