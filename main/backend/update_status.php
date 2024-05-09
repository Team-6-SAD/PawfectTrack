<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include your database connection file
    require_once 'pawfect_connect.php';

    // Retrieve the appointment ID and status from the form data
    $appointmentID = $_POST['appointmentID'];
    $status = $_POST['status'];
    
    // Retrieve the arrays of medicine brands, quantities, dosages, and treatment IDs
    $medicineBrands = $_POST['medicineBrand'];
    $quantities = $_POST['quantity'];
    $treatmentIDs = $_POST['treatmentID'];
    $dosages = $_POST['dosage'];
    $medicineNames = $_POST['medicineName'];

    // Prepare and execute SQL query to update appointment status
    $updateSql = "UPDATE appointmentinformation SET Status = ? WHERE AppointmentID = ?";
    $updateStmt = mysqli_prepare($conn, $updateSql);
    mysqli_stmt_bind_param($updateStmt, "si", $status, $appointmentID);
    mysqli_stmt_execute($updateStmt);

    // Prepare and execute SQL query to insert medicine usage details
    $insertSql = "INSERT INTO medicineusage (TreatmentID, MedicineBrand, Quantity, Dosage, MedicineName) VALUES (?, ?, ?, ?,?)";
    $insertStmt = mysqli_prepare($conn, $insertSql);

    // Iterate through each medicine brand and quantity
    for ($i = 0; $i < count($medicineBrands); $i++) {
        $medicineBrand = $medicineBrands[$i];
        $quantity = $quantities[$i];
        $treatmentID = $treatmentIDs[$i];
        $dosage = $dosages[$i];
        $medicineName = $medicineNames[$i];

        // Bind parameters and execute the insert statement
        mysqli_stmt_bind_param($insertStmt, "isids", $treatmentID, $medicineBrand, $quantity, $dosage, $medicineName);
        mysqli_stmt_execute($insertStmt);
    }

    // Close the prepared statements
    mysqli_stmt_close($updateStmt);
    mysqli_stmt_close($insertStmt);

    // Close the database connection
    mysqli_close($conn);
}
?>
