<?php
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
        $response = array('success' => true, 'message' => 'Stock added successfully.');
        echo json_encode($response);
    } else {
        // Error occurred during insertion
        $response = array('success' => false, 'message' => 'Error adding stock: ' . mysqli_error($conn));
        echo json_encode($response);
    }
    mysqli_stmt_close($insertStmt);



mysqli_close($conn);
?>
