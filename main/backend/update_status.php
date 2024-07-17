<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include your database connection file
    require_once 'pawfect_connect.php';

    // Initialize variables to store error messages
    $errors = [];

    // Retrieve the appointment ID and status from the form data
    $appointmentID = $_POST['appointmentID'];
    $status = $_POST['status'];

    // Validate appointmentID and status
    if (!isset($appointmentID) || empty($appointmentID)) {
        $errors[] = "Appointment ID is required.";
    }

    if (!isset($status) || empty($status)) {
        $errors[] = "Appointment status is required.";
    } elseif (!in_array($status, ['Done', 'Cancel'])) {
        $errors[] = "Invalid appointment status.";
    }

    // Check for errors before proceeding
    if (empty($errors)) {
        if ($status == "Done") {
            // Prepare and execute SQL query to update appointment status
            $updateSql = "UPDATE appointmentinformation SET Status = ? WHERE AppointmentID = ?";
            $updateStmt = mysqli_prepare($conn, $updateSql);
            mysqli_stmt_bind_param($updateStmt, "si", $status, $appointmentID);
            mysqli_stmt_execute($updateStmt);
            mysqli_stmt_close($updateStmt);

            // Retrieve arrays of medicine brands, quantities, dosages, and treatment IDs
            $medicineBrands = $_POST['medicineBrand'];
            $quantities = $_POST['quantity'];
            $treatmentIDs = $_POST['treatmentID'];
            $dosages = $_POST['dosage'];
            $medicineNames = $_POST['medicineName'];

            // Prepare and execute SQL query to insert medicine usage details
            $insertMedicineSql = "INSERT INTO medicineusage (TreatmentID, MedicineBrand, Quantity, Dosage, MedicineName) VALUES (?, ?, ?, ?, ?)";
            $insertMedicineStmt = mysqli_prepare($conn, $insertMedicineSql);
            mysqli_stmt_bind_param($insertMedicineStmt, "isids", $treatmentID, $medicineBrand, $quantity, $dosage, $medicineName);

            // Prepare and execute SQL query to insert machine learning details
            $machineLearningInsertQuery = "INSERT INTO machinelearning (TreatmentID, MedicineBrand, MedicineName, Dosage, Quantity) VALUES (?, ?, ?, ?, ?)";
            $machineLearningStmt = mysqli_prepare($conn, $machineLearningInsertQuery);
            mysqli_stmt_bind_param($machineLearningStmt, "isids", $treatmentID, $medicineBrand, $medicineName, $dosage, $quantity);

            // Prepare and execute SQL query to fetch MedicineBrandID based on MedicineBrand
            $fetchMedicineBrandIDSql = "SELECT MedicineBrandID FROM medicinebrand WHERE BrandName = ?";
            $fetchMedicineBrandIDStmt = mysqli_prepare($conn, $fetchMedicineBrandIDSql);
            mysqli_stmt_bind_param($fetchMedicineBrandIDStmt, "s", $medicineBrand);

            // Prepare and execute SQL query to update stock quantity in medicineinventory table
            $updateStockSql = "UPDATE medicineinventory SET StockQuantity = ? WHERE MedicineBrandID = ? AND StockExpiryDate = ?";
            $updateStockStmt = mysqli_prepare($conn, $updateStockSql);

            // Iterate through each medicine detail and insert into medicineusage and machinelearning tables
            for ($i = 0; $i < count($medicineBrands); $i++) {
                $medicineBrand = $medicineBrands[$i];
                $quantity = $quantities[$i];
                $treatmentID = $treatmentIDs[$i];
                $dosage = $dosages[$i];
                $medicineName = $medicineNames[$i];

                // Insert into medicineusage table
                mysqli_stmt_execute($insertMedicineStmt);

                // Insert into machinelearning table
                mysqli_stmt_execute($machineLearningStmt);

                // Fetch MedicineBrandID
                mysqli_stmt_execute($fetchMedicineBrandIDStmt);
                mysqli_stmt_bind_result($fetchMedicineBrandIDStmt, $medicineBrandID);
                mysqli_stmt_fetch($fetchMedicineBrandIDStmt);
                mysqli_stmt_close($fetchMedicineBrandIDStmt);

                // Update stock quantity in medicineinventory table
                $remainingQuantity = $quantity;
                while ($remainingQuantity > 0) {
                    // Fetch current stock quantity and closest expiry date
                    $getStockQuery = "SELECT StockQuantity, StockExpiryDate FROM medicineinventory WHERE MedicineBrandID = ? AND StockQuantity > 0 ORDER BY StockExpiryDate ASC LIMIT 1";
                    $getStockStmt = mysqli_prepare($conn, $getStockQuery);
                    mysqli_stmt_bind_param($getStockStmt, "i", $medicineBrandID);
                    mysqli_stmt_execute($getStockStmt);
                    mysqli_stmt_bind_result($getStockStmt, $stockQuantity, $stockExpiryDate);
                    mysqli_stmt_fetch($getStockStmt);
                    mysqli_stmt_close($getStockStmt);

                    if ($stockQuantity > 0) {
                        if ($remainingQuantity <= $stockQuantity) {
                            // Update stock quantity in medicineinventory table
                            mysqli_stmt_bind_param($updateStockStmt, "iis", $newStockQuantity, $medicineBrandID, $stockExpiryDate);
                            mysqli_stmt_execute($updateStockStmt);
                            $remainingQuantity = 0;
                        } else {
                            // Deplete the current stock and move to the next batch
                            $remainingQuantity -= $stockQuantity;
                            mysqli_stmt_bind_param($updateStockStmt, "iis", 0, $medicineBrandID, $stockExpiryDate);
                            mysqli_stmt_execute($updateStockStmt);
                        }
                    } else {
                        break;
                    }
                }
            }

            // Close statements
            mysqli_stmt_close($insertMedicineStmt);
            mysqli_stmt_close($machineLearningStmt);
            mysqli_stmt_close($updateStockStmt);

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
            mysqli_stmt_close($deleteStmt);
        }

        // Close the database connection
        mysqli_close($conn);
    } else {
        // Handle errors if any
        foreach ($errors as $error) {
            echo "<p>Error: $error</p>";
        }
    }
}
?>
