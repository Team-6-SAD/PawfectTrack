<?php
session_start();

// Check if the 'admin' session variable is not set or is false (user not logged in)
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true || !isset($_SESSION['adminID'])) {
    // Redirect the user to the login page
    header("Location: ../Admin Login.php");
    exit(); // Terminate the script
}

// Include your database connection file
require_once 'pawfect_connect.php';

// Check if selectedRows array is set and not empty
if (isset($_POST['selectedRows']) && !empty($_POST['selectedRows'])) {
    // Convert the selectedRows string into an array of EquipmentIDs
    $selectedRows = $_POST['selectedRows'];
    
    // Array to store EquipmentIDs that need to be checked for deletion
    $equipmentIDsToDelete = array();
    
    // Loop through each selected EquipmentID
    foreach ($selectedRows as $EquipmentID) {
        // First, delete from equipmentstock table
        $sql = "DELETE FROM equipmentstock WHERE EquipmentID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $EquipmentID);
        $stmt->execute();

        // Now, delete from equipment table
        $sql = "DELETE FROM equipment WHERE EquipmentID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $EquipmentID);
        $stmt->execute();
    }
    
    // Redirect back to the inventory page
    header("Location: ../equipment-inventory.php");
    exit();
} else {
    // No equipment selected for removal
    echo "No equipment selected for removal!";
}
?>
