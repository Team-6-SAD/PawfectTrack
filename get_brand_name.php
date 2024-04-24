<?php
// Include your database connection file
require_once 'pawfect_connect.php';

// Check if MedicineBrandID is provided in the POST request
if (isset($_POST['medicineBrandID'])) {
    // Retrieve MedicineBrandID from the POST data
    $medicineBrandID = $_POST['medicineBrandID'];

    // Prepare and execute the SQL query to fetch the BrandName based on MedicineBrandID
    $sql = "SELECT BrandName FROM medicinebrand WHERE MedicineBrandID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $medicineBrandID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if a row is returned
    if ($row = mysqli_fetch_assoc($result)) {
        // Retrieve the BrandName
        $brandName = $row['BrandName'];
        // Return the BrandName as the response
        echo $brandName;
    } else {
        // Return an error message if BrandName is not found
        echo "Error: BrandName not found for the provided MedicineBrandID.";
    }
} else {
    // Return an error message if MedicineBrandID is not provided
    echo "Error: MedicineBrandID not provided in the request.";
}
?>
