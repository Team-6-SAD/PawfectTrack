<?php
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true || !isset($_SESSION['adminID'])) {
    // Redirect the user to the login page
    header("Location: ../Admin Login.php");
    exit(); // Terminate the script
}

// Include your database connection file
require_once 'pawfect_connect.php';

// Retrieve form data and sanitize input
$productName = mysqli_real_escape_string($conn, $_POST['productName']);
$productBrand = mysqli_real_escape_string($conn, $_POST['productBrand']);
$route = mysqli_real_escape_string($conn, $_POST['route']);

// Check if the Medicine already exists in the medicine table
$sql = "SELECT * FROM medicine WHERE MedicineName = '$productName'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // Medicine already exists, get its MedicineID
    $row = $result->fetch_assoc();
    $medicineID = $row['MedicineID'];
} else {
    // Medicine does not exist, insert it into the medicine table
    $sql = "INSERT INTO medicine (MedicineName) VALUES ('$productName')";
    if ($conn->query($sql) === TRUE) {
        $medicineID = $conn->insert_id; // Get the ID of the last inserted row
    } else {
        $_SESSION['errorMessage'] = "Error: " . $sql . "<br>" . $conn->error;
        header("Location: ../inventory.php");
        exit(); // Terminate the script
    }
}

// Check if the Product Brand already exists for this Medicine
$sql = "SELECT * FROM medicinebrand WHERE MedicineID = '$medicineID' AND BrandName = '$productBrand'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // Product Brand already exists for this Medicine, redirect back to the modal page
    $_SESSION['alreadyExists'] = true;
    header("Location: ../inventory.php");
    exit();
}

// Insert into medicinebrand table
$sql = "INSERT INTO medicinebrand (MedicineID, BrandName, Route) 
        VALUES ($medicineID, '$productBrand', '$route')";
if ($conn->query($sql) === TRUE) {
    $_SESSION['successMessage'] = "Medicine added successfully!";
    header("Location: ../inventory.php"); // Redirect back to the inventory page
    exit();
} else {
    $_SESSION['errorMessage'] = "Error: " . $sql . "<br>" . $conn->error;
    header("Location: ../inventory.php");
    exit(); // Terminate the script
}

$conn->close();
?>
