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
    // Convert the selectedRows string into an array of InventoryIDs
    $selectedRows = $_POST['selectedRows'];
    
    // Convert the array to a comma-separated string of integers for the SQL query
    $selectedRowsList = implode(',', array_map('intval', $selectedRows));
    
    // Fetch the MedicineBrandID and StockExpiryDate of the rows being deleted
    $deleteCriteria = [];
    $sql = "SELECT MedicineBrandID, StockExpiryDate FROM medicineinventory WHERE InventoryID IN ($selectedRowsList)";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $deleteCriteria[] = [
                'MedicineBrandID' => $row['MedicineBrandID'],
                'StockExpiryDate' => $row['StockExpiryDate']
            ];
        }
    }
    
    if (!empty($deleteCriteria)) {
        // Build the SQL query to delete rows that match the criteria
        $deleteConditions = [];
        foreach ($deleteCriteria as $criteria) {
            $brandID = $criteria['MedicineBrandID'];
            $expiryDate = $criteria['StockExpiryDate'];
            $deleteConditions[] = "(MedicineBrandID = $brandID AND StockExpiryDate = '$expiryDate')";
        }
        
        $deleteConditionsString = implode(' OR ', $deleteConditions);
        $sqlDelete = "DELETE FROM medicineinventory WHERE $deleteConditionsString";
        
        if ($conn->query($sqlDelete) === TRUE) {
            // Redirect back to the inventory page
            header("Location: ../Inventory.php");
            exit();
        } else {
            echo "Error: " . $sqlDelete . "<br>" . $conn->error;
        }
    } else {
        echo "No matching rows found for deletion!";
    }
} else {
    // No medicine brands selected for removal
    echo "No medicine brands selected for removal!";
}
?>
