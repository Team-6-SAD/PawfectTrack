<?php
session_start();

// Check if the 'admin' session variable is not set or is false (user not logged in)
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true || !isset($_SESSION['adminID'])) {
    // Redirect the user to the login page
    header("Location: Admin Login.php");
    exit(); // Terminate the script
}

// Include your database connection file
require_once 'pawfect_connect.php';

// Check if selectedRows array is set and not empty
if (isset($_POST['selectedRows']) && !empty($_POST['selectedRows'])) {
    // Convert the selectedRows string into an array of MedicineBrandIDs
    $selectedRows = $_POST['selectedRows'];
    
    // Array to store MedicineIDs that need to be checked for deletion
    $medicineIDsToDelete = array();
    
    // Loop through each selected MedicineBrandID
    foreach ($selectedRows as $medicineBrandID) {
        // Prepare and execute the SQL query to delete entries from medicineinventory table
        $sql = "DELETE FROM medicineinventory WHERE MedicineBrandID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $medicineBrandID);
        $stmt->execute();
        
        // Store the associated MedicineID for later deletion check
        $sql = "SELECT MedicineID FROM medicinebrand WHERE MedicineBrandID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $medicineBrandID);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $medicineID = $row['MedicineID'];
        $medicineIDsToDelete[] = $medicineID;
        
        // Prepare and execute the SQL query to delete entries from medicinebrand table
        $sql = "DELETE FROM medicinebrand WHERE MedicineBrandID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $medicineBrandID);
        $stmt->execute();
    }
    
    // Check if any MedicineIDs need to be checked for deletion
    foreach ($medicineIDsToDelete as $medicineID) {
        // Check if there are any remaining MedicineBrand entries for this MedicineID
        $sql = "SELECT COUNT(*) AS brandCount FROM medicinebrand WHERE MedicineID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $medicineID);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $brandCount = $row['brandCount'];
        
        // If there are no more MedicineBrand entries, delete the associated Medicine entry
        if ($brandCount == 0) {
            $sql = "DELETE FROM medicine WHERE MedicineID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $medicineID);
            $stmt->execute();
        }
    }
    
    // Redirect back to the inventory page
    header("Location: inventory.php");
    exit();
} else {
    // No medicine brands selected for removal
    echo "No medicine brands selected for removal!";
}
?>
