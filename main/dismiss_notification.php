<?php
// Database credentials
include 'backend/pawfect_connect.php';
date_default_timezone_set('Asia/Manila'); // Set to Philippine Time

// Read JSON input from POST request
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

// Check if MedicineBrandID or EquipmentID is provided and valid
if ((!isset($input['medicine_brand_id']) || !is_numeric($input['medicine_brand_id'])) && 
    (!isset($input['equipment_id']) || !is_numeric($input['equipment_id']))) {
    http_response_code(400);
    die("Invalid ID provided");
}

// Initialize variables
$medicine_brand_id = isset($input['medicine_brand_id']) ? intval($input['medicine_brand_id']) : null;
$equipment_id = isset($input['equipment_id']) ? intval($input['equipment_id']) : null;

// Prepare SQL statements for both tables
$sqlBrand = "UPDATE medicinebrand SET dismissed_at = NOW() WHERE MedicineBrandID = ?";
$sqlInventory = "UPDATE medicineinventory SET dismissed_at = NOW() WHERE MedicineBrandID = ?";
$sqlEquipment = "UPDATE equipment SET dismissed_at = NOW() WHERE EquipmentID = ?";
$sqlEquipmentStock = "UPDATE equipmentstock SET dismissed_at = NOW() WHERE EquipmentID = ?";

// Start a transaction
$conn->begin_transaction();

try {
    // Update dismissed_at in medicinebrand table
    if ($medicine_brand_id !== null) {
        $stmtBrand = $conn->prepare($sqlBrand);
        $stmtBrand->bind_param('i', $medicine_brand_id);
        if (!$stmtBrand->execute()) {
            throw new Exception('Failed to update medicinebrand table');
        }
        $stmtBrand->close();

        // Update dismissed_at in medicineinventory table
        $stmtInventory = $conn->prepare($sqlInventory);
        $stmtInventory->bind_param('i', $medicine_brand_id);
        if (!$stmtInventory->execute()) {
            throw new Exception('Failed to update medicineinventory table');
        }
        $stmtInventory->close();
    }

    // Update dismissed_at in equipment table
    if ($equipment_id !== null) {
        $stmtEquipment = $conn->prepare($sqlEquipment);
        $stmtEquipment->bind_param('i', $equipment_id);
        if (!$stmtEquipment->execute()) {
            throw new Exception('Failed to update equipment table');
        }
        $stmtEquipment->close();

        // Update dismissed_at in equipmentstock table
        $stmtEquipmentStock = $conn->prepare($sqlEquipmentStock);
        $stmtEquipmentStock->bind_param('i', $equipment_id);
        if (!$stmtEquipmentStock->execute()) {
            throw new Exception('Failed to update equipmentstock table');
        }
        $stmtEquipmentStock->close();
    }

    // Commit the transaction
    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Rollback the transaction in case of an error
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

// Close the connection
$conn->close();
?>
