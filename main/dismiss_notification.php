<?php
include 'backend/pawfect_connect.php';

// Capture and log incoming data
$data = json_decode(file_get_contents('php://input'), true);
file_put_contents('log.txt', "Incoming Data:\n" . print_r($data, true), FILE_APPEND);

$response = ['status' => 'error', 'message' => 'Invalid data'];

if (isset($data['medicine_brand_id'])) {
    $medicineBrandID = $data['medicine_brand_id'];
    
    // Update medicineinventory table
    $sqlMedicineInv = "UPDATE medicineinventory SET dismissed_at = NOW() WHERE MedicineBrandID = ?";
    $stmtMedicineInv = $conn->prepare($sqlMedicineInv);
    $stmtMedicineInv->bind_param("i", $medicineBrandID);
    
    // Update medicinebrand table
    $sqlMedicineBrand = "UPDATE medicinebrand SET dismissed_at = NOW() WHERE MedicineBrandID = ?";
    $stmtMedicineBrand = $conn->prepare($sqlMedicineBrand);
    $stmtMedicineBrand->bind_param("i", $medicineBrandID);
    
    // Execute updates
    $conn->autocommit(false); // Start a transaction
    $updateMedicineInv = $stmtMedicineInv->execute();
    $updateMedicineBrand = $stmtMedicineBrand->execute();
    
    if ($updateMedicineInv && $updateMedicineBrand) {
        $conn->commit(); // Commit transaction if both updates are successful
        $response = ['status' => 'success', 'message' => 'Notification dismissed for medicine and related brand'];
    } else {
        $conn->rollback(); // Rollback transaction if any update fails
        $response['message'] = 'Failed to update medicine and related brand notifications: ' . $conn->error;
    }
    
    $stmtMedicineInv->close();
    $stmtMedicineBrand->close();
} elseif (isset($data['equipment_id'])) {
    $equipmentID = $data['equipment_id'];
    
    // Update equipmentstock table
    $sqlEquipmentStock = "UPDATE equipmentstock SET dismissed_at = NOW() WHERE EquipmentID = ?";
    $stmtEquipmentStock = $conn->prepare($sqlEquipmentStock);
    $stmtEquipmentStock->bind_param("i", $equipmentID);
    
    // Update equipment table (assuming you have a dismissed_at column in equipment table)
    $sqlEquipment = "UPDATE equipment SET dismissed_at = NOW() WHERE EquipmentID = ?";
    $stmtEquipment = $conn->prepare($sqlEquipment);
    $stmtEquipment->bind_param("i", $equipmentID);
    
    // Execute updates
    $conn->autocommit(false); // Start a transaction
    $updateEquipmentStock = $stmtEquipmentStock->execute();
    $updateEquipment = $stmtEquipment->execute();
    
    if ($updateEquipmentStock && $updateEquipment) {
        $conn->commit(); // Commit transaction if both updates are successful
        $response = ['status' => 'success', 'message' => 'Notification dismissed for equipment and related equipment stock'];
    } else {
        $conn->rollback(); // Rollback transaction if any update fails
        $response['message'] = 'Failed to update equipment and related equipment stock notifications: ' . $conn->error;
    }
    
    $stmtEquipmentStock->close();
    $stmtEquipment->close();
} else {
    $response['message'] = 'No valid ID provided';
}

$conn->close();

// Log the response being sent back
file_put_contents('log.txt', "Response:\n" . print_r($response, true), FILE_APPEND);

// Set content type to JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
