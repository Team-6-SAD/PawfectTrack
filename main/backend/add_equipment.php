<?php
session_start();
// Include your database connection file
require_once 'pawfect_connect.php';

// Retrieve the data sent via POST request
$equipmentName = $_POST['equipmentName'];
$equipmentQuantity = $_POST['quantity'];
$equipmentPrice = $_POST['priceBought'];

// Perform any necessary validation of the input data

// Check if the equipment already exists
$sqlCheck = "SELECT EquipmentID FROM equipment WHERE Name = ?";
$stmtCheck = mysqli_prepare($conn, $sqlCheck);
mysqli_stmt_bind_param($stmtCheck, "s", $equipmentName);
mysqli_stmt_execute($stmtCheck);
mysqli_stmt_bind_result($stmtCheck, $existingEquipmentId);
mysqli_stmt_fetch($stmtCheck);
mysqli_stmt_close($stmtCheck);

if ($existingEquipmentId) {
    // Equipment already exists, update the existing record in equipmentstock by adding to the current values
    $sqlUpdate = "UPDATE equipmentstock SET Quantity = Quantity + ?, PriceBought = ? WHERE EquipmentID = ?";
    $stmtUpdate = mysqli_prepare($conn, $sqlUpdate);
    mysqli_stmt_bind_param($stmtUpdate, "idi", $equipmentQuantity, $equipmentPrice, $existingEquipmentId);
    
    if (mysqli_stmt_execute($stmtUpdate)) {
        $_SESSION['successMessageEquipment'] = "Equipment updated successfully!";
        $response = array('success' => true, 'message' => 'Equipment updated successfully.');
        echo json_encode($response);
       header("Location: ../equipment-inventory.php");
            exit();
    } else {
        // Error occurred while updating equipmentstock
        $response = array('success' => false, 'message' => 'Error updating equipment stock: ' . mysqli_error($conn));
        echo json_encode($response);
       header("Location: ../equipment-inventory.php");
            exit();
    }

    mysqli_stmt_close($stmtUpdate);
} else {
    // Equipment does not exist, insert new record into equipment and equipmentstock
    $sqlInsertEquipment = "INSERT INTO equipment (Name) VALUES (?)";
    $stmtInsertEquipment = mysqli_prepare($conn, $sqlInsertEquipment);
    mysqli_stmt_bind_param($stmtInsertEquipment, "s", $equipmentName);

    if (mysqli_stmt_execute($stmtInsertEquipment)) {
        // Retrieve the auto-generated ID of the inserted equipment
        $newEquipmentId = mysqli_insert_id($conn);
        
        // Insert the equipment stock details into the equipmentstock table
        $sqlInsertStock = "INSERT INTO equipmentstock (EquipmentID, Quantity, PriceBought) VALUES (?, ?, ?)";
        $stmtInsertStock = mysqli_prepare($conn, $sqlInsertStock);
        mysqli_stmt_bind_param($stmtInsertStock, "idi", $newEquipmentId, $equipmentQuantity, $equipmentPrice);
        
        if (mysqli_stmt_execute($stmtInsertStock)) {
            $_SESSION['successMessageEquipment'] = "Equipment added successfully!";
            $response = array('success' => true, 'message' => 'Equipment added successfully.');
            echo json_encode($response);

            // Redirect to inventory.php
            header("Location: ../equipment-inventory.php");
            exit();
        } else {
            // Error occurred while inserting into equipmentstock
            $response = array('success' => false, 'message' => 'Error adding equipment stock: ' . mysqli_error($conn));
            echo json_encode($response);
        }

        mysqli_stmt_close($stmtInsertStock);
    } else {
        // Error occurred while inserting into equipment
        $response = array('success' => false, 'message' => 'Error adding equipment: ' . mysqli_error($conn));
        echo json_encode($response);
    }

    mysqli_stmt_close($stmtInsertEquipment);
}

// Close the database connection
mysqli_close($conn);
?>
