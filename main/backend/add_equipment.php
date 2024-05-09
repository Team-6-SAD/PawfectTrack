<?php
session_start();
// Include your database connection file
require_once 'pawfect_connect.php';

// Retrieve the data sent via POST request
$equipmentName = $_POST['equipmentName'];
$equipmentQuantity = $_POST['quantity'];
$equipmentPrice = $_POST['priceBought'];

// Perform any necessary validation of the input data

// Perform the database insertion
$sql = "INSERT INTO equipment (Name) VALUES (?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $equipmentName);

// Check if the query was executed successfully
if (mysqli_stmt_execute($stmt)) {
    // Retrieve the auto-generated ID of the inserted equipment
    $equipmentId = mysqli_insert_id($conn);
    
    // Insert the equipment stock details into the equipmentstock table
    $sqlStock = "INSERT INTO equipmentstock (EquipmentID, Quantity, PriceBought) VALUES (?, ?, ?)";
    $stmtStock = mysqli_prepare($conn, $sqlStock);
    mysqli_stmt_bind_param($stmtStock, "iii", $equipmentId, $equipmentQuantity, $equipmentPrice);
    
    // Check if the query was executed successfully
    if (mysqli_stmt_execute($stmtStock)) {
        $_SESSION['successMessageEquipment'] = "Equipment added successfully!";
        $response = array('success' => true, 'message' => 'Equipment added successfully.');
        echo json_encode($response);

        // Redirect to inventory.php
        header("Location: inventory.php");
        exit();
    } else {
        // Error occurred while inserting into equipmentstock
        $response = array('success' => false, 'message' => 'Error adding equipment stock: ' . mysqli_error($conn));
        echo json_encode($response);
    }

    // Close the stock statement
    mysqli_stmt_close($stmtStock);
} else {
    // Error occurred while inserting into equipment
    $response = array('success' => false, 'message' => 'Error adding equipment: ' . mysqli_error($conn));
    echo json_encode($response);
}

// Close the main statement
mysqli_stmt_close($stmt);
// Close the database connection
mysqli_close($conn);
?>
