<?php
// Connect to your database
include_once 'pawfect_connect.php'; // Adjust the filename as per your setup

// Check if the AJAX request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the selected medicine type from the AJAX request
    $selectedMedicineID = $_POST["medicineType"];

    // Prepare the SQL query with a placeholder for the medicine ID
    $sql = "SELECT mb.BrandName, mb.Route, mb.MedicineBrandID  
            FROM medicinebrand mb
            INNER JOIN medicineinventory ms ON mb.MedicineBrandID = ms.MedicineBrandID
            WHERE mb.MedicineID = ? AND ms.StockQuantity > 0";

    // Prepare the statement
    $stmt = mysqli_prepare($conn, $sql);

    // Bind the parameter
    mysqli_stmt_bind_param($stmt, "i", $selectedMedicineID);

    // Execute the statement
    mysqli_stmt_execute($stmt);

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    // Generate options for the "Medicine Given" dropdown based on the query result
    $options = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $options[] = $row;
    }

    // Return JSON response with the options
    header('Content-Type: application/json');
    echo json_encode($options);
}
?>
