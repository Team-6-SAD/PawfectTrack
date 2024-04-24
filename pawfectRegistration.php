<?php
require_once 'pawfect_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and perform basic validation
    $firstName = trim(htmlspecialchars($_POST["first-name"]));
    $lastName = trim(htmlspecialchars($_POST["last-name"]));
    $username = trim(htmlspecialchars($_POST["username"]));
    $phoneNumber = trim(htmlspecialchars($_POST["phone-number"]));
    $email = trim(htmlspecialchars($_POST["email"]));
    $password = trim(htmlspecialchars($_POST["password"]));
    $confirmPassword = trim(htmlspecialchars($_POST["confirmPassword"]));

    // Perform additional server-side validation
    if (empty($firstName) || empty($lastName) || empty($username) || empty($phoneNumber) || empty($email) || empty($password) || empty($confirmPassword)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        exit;
    }

    // Validate phone number format (assuming 11 digits starting with '09')
    if (!preg_match('/^09\d{9}$/', $phoneNumber)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid phone number format']);
        exit;
    }

    // Check if password and confirm password match
    if ($password !== $confirmPassword) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
        exit;
    }

    // Check if the username already exists
    $stmt = $conn->prepare("SELECT * FROM admincredentials WHERE AdminUsername = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username already exists']);
        exit;
    }

    // Proceed with registration
    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and bind parameters for the query
    $stmt = $conn->prepare("INSERT INTO admincredentials (AdminUsername, AdminPassword) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashedPassword);

    // Execute the statement
    if ($stmt->execute()) {
        // Retrieve the AdminID of the inserted row
        $adminID = $conn->insert_id;

        // Prepare and bind parameters for the second query
        $stmt = $conn->prepare("INSERT INTO admininformation (AdminID, firstname, middlename, lastname, email, phonenumber) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $adminID, $firstName, $middleName, $lastName, $email, $phoneNumber);

        // Execute the second statement
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Registration successful']);
            exit;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to insert user information']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to insert user credentials']);
        exit;
    }
}

// Close statement
$stmt->close();
// Close connection
$conn->close();
?>
