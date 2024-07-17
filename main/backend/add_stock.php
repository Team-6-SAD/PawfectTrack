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

// Check if the record already exists based on brandName and stockExpiryDate
$checkSql = "SELECT * FROM medicineinventory WHERE MedicineBrandID = ? AND StockExpiryDate = ?";
$checkStmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($checkStmt, "ss", $brandName, $stockExpiryDate);
mysqli_stmt_execute($checkStmt);
mysqli_stmt_store_result($checkStmt);

if (mysqli_stmt_num_rows($checkStmt) > 0) {
    // If record exists, update the StockQuantity
    $updateSql = "UPDATE medicineinventory SET StockQuantity = StockQuantity + ? WHERE MedicineBrandID = ? AND StockExpiryDate = ?";
    $updateStmt = mysqli_prepare($conn, $updateSql);
    mysqli_stmt_bind_param($updateStmt, "iss", $stockQuantity, $brandName, $stockExpiryDate);
    
    if (mysqli_stmt_execute($updateStmt)) {
        // Quantity updated successfully
        $_SESSION['successMessageStock'] = "Stock quantity updated successfully!";
    } else {
        // Error occurred during update
        $_SESSION['errorMessageStock'] = "Error updating stock quantity: " . mysqli_error($conn);
    }
    
    mysqli_stmt_close($updateStmt);
} else {
    // If record does not exist, insert a new record
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
}

mysqli_close($conn);

// Redirect to inventory.php
header("Location: ../Inventory.php");
exit();
?>
