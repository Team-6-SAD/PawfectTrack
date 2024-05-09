<?php
session_start();
// Include your database connection file
require_once 'pawfect_connect.php';

// Retrieve the data sent via POST request
$stockQuantity = $_POST['stockQuantity'];
$stockPrice = $_POST['stockPrice'];
$stockDosage = $_POST['stockDosage'];
$stockExpiryDate = $_POST['stockExpiryDate'];
$stockBoughtPrice = $_POST['stockBoughtPrice'];
$brandName = $_POST['brandName'];

// Insert the new stock into the database
$insertSql = "INSERT INTO medicineinventory (StockQuantity, StockPrice, StockDosage, StockExpiryDate, StockBoughtPrice, MedicineBrandID) 
              VALUES (?, ?, ?, ?, ?, ?)";
$insertStmt = mysqli_prepare($conn, $insertSql);
mysqli_stmt_bind_param($insertStmt, "iddssi", $stockQuantity, $stockPrice, $stockDosage, $stockExpiryDate, $stockBoughtPrice, $brandName);

if (mysqli_stmt_execute($insertStmt)) {
    // Data inserted successfully
    $_SESSION['successMessageStock'] = "Medicine added successfully!";
} else {
    // Error occurred during insertion
    $_SESSION['errorMessageStock'] = "Error adding stock: " . mysqli_error($conn);
}

mysqli_stmt_close($insertStmt);
mysqli_close($conn);

// Redirect to inventory.php
header("Location: ../inventory.php");
exit();
?>
