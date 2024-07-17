<?php
// Connect to your database
include_once 'pawfect_connect.php'; // Adjust the filename as per your setup

// Check if the AJAX request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the selected brand ID from the AJAX request
    $selectedBrandID = $_POST["medicineBrandID"];

    // Prepare the SQL query to fetch the Route and TotalQuantity for the selected brand
$sql = "SELECT mb.BrandName, mb.Route, mb.MedicineBrandID, SUM(ms.StockQuantity) AS TotalQuantity 
        FROM medicinebrand mb
        INNER JOIN medicineinventory ms ON mb.MedicineBrandID = ms.MedicineBrandID
        WHERE mb.MedicineBrandID = ?
        GROUP BY mb.BrandName, mb.Route, mb.MedicineBrandID";


    // Prepare the statement
    $stmt = mysqli_prepare($conn, $sql);

    // Bind the parameter
    mysqli_stmt_bind_param($stmt, "i", $selectedBrandID);

    // Execute the statement
    mysqli_stmt_execute($stmt);

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    // Fetch the details
    $row = mysqli_fetch_assoc($result);

    // Return JSON response with the details
    header('Content-Type: application/json');
    echo json_encode($row);
}
?>
