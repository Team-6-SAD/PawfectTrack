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

// Check if the request method is POST and the content type is JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    // Get the raw POST data
    $rawPostData = file_get_contents('php://input');
    // Decode the JSON data into an associative array
    $data = json_decode($rawPostData, true);

    // Check if selectedRows array is set and not empty
    if (isset($data['selectedRows']) && !empty($data['selectedRows'])) {
        // Get the selectedRows array
        $selectedRows = $data['selectedRows'];
        
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
} else {
    // Invalid request
    echo "Invalid request!";
}
?>
