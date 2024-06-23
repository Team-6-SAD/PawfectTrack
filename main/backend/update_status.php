<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include your database connection file
    require_once 'pawfect_connect.php';

    // Retrieve the appointment ID and status from the form data
    $appointmentID = $_POST['appointmentID'];
    $status = $_POST['status'];

    // Prepare and execute SQL query to update appointment status


    if ($status == "Done") {
        // Retrieve the arrays of medicine brands, quantities, dosages, and treatment IDs
        $updateSql = "UPDATE appointmentinformation SET Status = ? WHERE AppointmentID = ?";
        $updateStmt = mysqli_prepare($conn, $updateSql);
        mysqli_stmt_bind_param($updateStmt, "si", $status, $appointmentID);
        mysqli_stmt_execute($updateStmt);
        
        $medicineBrands = $_POST['medicineBrand'];
        $quantities = $_POST['quantity'];
        $treatmentIDs = $_POST['treatmentID'];
        $dosages = $_POST['dosage'];
        $medicineNames = $_POST['medicineName'];

        // Prepare and execute SQL query to insert medicine usage details
        $insertSql = "INSERT INTO medicineusage (TreatmentID, MedicineBrand, Quantity, Dosage, MedicineName) VALUES (?, ?, ?, ?, ?)";
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

        // Close the insert statement
        mysqli_stmt_close($insertStmt);
    } elseif ($status == "Cancel") {
        // Fetch the TreatmentID associated with the given AppointmentID
        $selectSql = "SELECT TreatmentID FROM appointmentinformation WHERE AppointmentID = ?";
        $selectStmt = mysqli_prepare($conn, $selectSql);
        mysqli_stmt_bind_param($selectStmt, "i", $appointmentID);
        mysqli_stmt_execute($selectStmt);
        mysqli_stmt_bind_result($selectStmt, $treatmentID);
        mysqli_stmt_fetch($selectStmt);
        mysqli_stmt_close($selectStmt);

        // Delete all appointments with the fetched TreatmentID and Status "Pending"
        $deleteSql = "DELETE FROM appointmentinformation WHERE TreatmentID = ? AND Status = 'Pending'";
        $deleteStmt = mysqli_prepare($conn, $deleteSql);
        mysqli_stmt_bind_param($deleteStmt, "i", $treatmentID);
        mysqli_stmt_execute($deleteStmt);

        // Close the delete statement
        mysqli_stmt_close($deleteStmt);
    }

    // Close the update statement
    mysqli_stmt_close($updateStmt);

    // Close the database connection
    mysqli_close($conn);
}
?>
