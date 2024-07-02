<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection
    include_once 'pawfect_connect.php';
    $targetDir = "uploads/";
    $bitePicture = null; // Initialize bitePicture to null

    if (!empty($_FILES["uploadImage"]["name"])) {
        $targetFile = $targetDir . basename($_FILES["uploadImage"]["name"]);

        // Check if the uploaded file is an image
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedExtensions = array("jpg", "jpeg", "png");
        if (!in_array($imageFileType, $allowedExtensions)) {
            echo "Sorry, only JPG, JPEG, PNG files are allowed.";
            exit(); // Stop further execution
        }

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["uploadImage"]["tmp_name"], $targetFile)) {
            echo "The file " . basename($_FILES["uploadImage"]["name"]) . " has been uploaded.";
            $bitePicture = $targetFile; // Assign the file path to bitePicture
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit(); // Stop further execution if file upload fails
        }
    }

    // Extract patient data from the form
    $patientId = $_POST['patientID']; // Assuming you have a hidden input field with name="patientID"

    // Extract exposure data from the form
    $exposureDate = $_POST['exposureDate'];
    $exposureBy = $_POST['exposureBy'];
    $exposureType = $_POST['exposureType'];
    $animalType = $_POST['animalType'];
    $biteLocation = $_POST['biteLocation'];

    // Prepare SQL query to insert exposure data
    if ($bitePicture) {
        $exposureInsertQuery = "INSERT INTO bitedetails (PatientID, ExposureDate, ExposureMethod, ExposureType, AnimalType, BiteLocation, BitePicture) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $exposureStmt = mysqli_prepare($conn, $exposureInsertQuery);
        mysqli_stmt_bind_param($exposureStmt, "issssss", $patientId, $exposureDate, $exposureBy, $exposureType, $animalType, $biteLocation, $bitePicture);
    } else {
        $exposureInsertQuery = "INSERT INTO bitedetails (PatientID, ExposureDate, ExposureMethod, ExposureType, AnimalType, BiteLocation) VALUES (?, ?, ?, ?, ?, ?)";
        $exposureStmt = mysqli_prepare($conn, $exposureInsertQuery);
        mysqli_stmt_bind_param($exposureStmt, "isssss", $patientId, $exposureDate, $exposureBy, $exposureType, $animalType, $biteLocation);
    }
    mysqli_stmt_execute($exposureStmt);
    $exposureId = mysqli_insert_id($conn); // Get the last inserted exposure ID

    // Extract treatment data from the form
    $treatmentCategory = $_POST['treatmentCategory'];
    $sessions = $_POST['sessions'];
    $treatmentDate = $_POST['treatmentDate'];
    $doctorRemarks = $_POST['doctorRemarks'];

    // Insert treatment data with patient ID
    $treatmentInsertQuery = "INSERT INTO treatment (PatientID, BiteID, Category, Session, DateOfTreatment, Recommendation) VALUES (?, ?, ?, ?, ?, ?)";
    $treatmentStmt = mysqli_prepare($conn, $treatmentInsertQuery);
    mysqli_stmt_bind_param($treatmentStmt, "iisiss", $patientId, $exposureId, $treatmentCategory, $sessions, $treatmentDate, $doctorRemarks);
    mysqli_stmt_execute($treatmentStmt);
    $treatmentId = mysqli_insert_id($conn); // Get the last inserted treatment ID
    
    $status = "Done"; // Default status for the first appointment
    $today = date("Y-m-d"); // Current date
    for ($i = 0; $i < $sessions; $i++) {
        // Calculate appointment date based on session number
        switch ($i) {
            case 0:
                // First session, use the treatment date
                $appointmentDate = $treatmentDate;
                $sessionDays = 0;
                break;
            case 1:
                // Second session, 3 days after treatment date
                $appointmentDate = date('Y-m-d', strtotime($treatmentDate . ' +3 days'));
                $status = "Pending";
                $sessionDays = 3;
                break;
            case 2:
                // Third session, 7 days after treatment date
                $appointmentDate = date('Y-m-d', strtotime($treatmentDate . ' +7 days'));
                $sessionDays = 7;
                break;
            case 3:
                // Fourth session, 14 days after treatment date
                $appointmentDate = date('Y-m-d', strtotime($treatmentDate . ' +14 days'));
                $sessionDays = 14;
                break;
            case 4:
                // Fifth session, 28 days after treatment date
                $appointmentDate = date('Y-m-d', strtotime($treatmentDate . ' +28 days'));
                $sessionDays = 28;
                break;
            default:
                // Additional sessions can be handled similarly
                break;
        }

        // Insert appointment information into the database
        $appointmentInsertQuery = "INSERT INTO appointmentinformation (TreatmentID, PatientID, AppointmentDate, Status, SessionDays) VALUES (?, ?, ?, ?, ?)";
        $appointmentStmt = mysqli_prepare($conn, $appointmentInsertQuery);
        mysqli_stmt_bind_param($appointmentStmt, "iissi", $treatmentId, $patientId, $appointmentDate, $status, $sessionDays);
        mysqli_stmt_execute($appointmentStmt);
        $appointmentId = mysqli_insert_id($conn); // Get the last inserted appointment ID
    }

    // Extract medicine data from the form
    $medicineTypes = $_POST['medicineType'];
    $medicineGivens = $_POST['medicineGiven'];
    $dosageQuantities = $_POST['dosageQuantity'];
    $routes = $_POST['route'];
    $quantities = $_POST['quantity'];

    for ($i = 0; $i < count($medicineTypes); $i++) {
        // Fetch BrandName based on MedicineBrandID
        $getBrandNameQuery = "SELECT BrandName FROM medicinebrand WHERE MedicineBrandID = ?";
        $getBrandNameStmt = mysqli_prepare($conn, $getBrandNameQuery);
        mysqli_stmt_bind_param($getBrandNameStmt, "i", $medicineGivens[$i]);
        mysqli_stmt_execute($getBrandNameStmt);
        mysqli_stmt_bind_result($getBrandNameStmt, $brandName);
        mysqli_stmt_fetch($getBrandNameStmt);
        mysqli_stmt_close($getBrandNameStmt);

        // Fetch MedicineName based on MedicineID
        $getMedicineNameQuery = "SELECT MedicineName FROM medicine WHERE MedicineID = ?";
        $getMedicineNameStmt = mysqli_prepare($conn, $getMedicineNameQuery);
        mysqli_stmt_bind_param($getMedicineNameStmt, "i", $medicineTypes[$i]);
        mysqli_stmt_execute($getMedicineNameStmt);
        mysqli_stmt_bind_result($getMedicineNameStmt, $medicineName);
        mysqli_stmt_fetch($getMedicineNameStmt);
        mysqli_stmt_close($getMedicineNameStmt);

        // Insert into medicineusage table with MedicineBrand Name and Medicine Name
        $medicineInsertQuery = "INSERT INTO medicineusage (TreatmentID, MedicineBrand, MedicineName, Dosage, Quantity) VALUES (?, ?, ?, ?, ?)";
        $medicineStmt = mysqli_prepare($conn, $medicineInsertQuery);
        mysqli_stmt_bind_param($medicineStmt, "issii", $treatmentId, $brandName, $medicineName, $dosageQuantities[$i], $quantities[$i]);
        mysqli_stmt_execute($medicineStmt);
        $medicineUsageId = mysqli_insert_id($conn);

        // Insert into machinelearning table
        $machineLearningInsertQuery = "INSERT INTO machinelearning (TreatmentID, MedicineBrand, MedicineName, Dosage, Quantity) VALUES (?, ?, ?, ?, ?)";
        $machineLearningStmt = mysqli_prepare($conn, $machineLearningInsertQuery);
        mysqli_stmt_bind_param($machineLearningStmt, "issii", $treatmentId, $brandName, $medicineName, $dosageQuantities[$i], $quantities[$i]);
        mysqli_stmt_execute($machineLearningStmt);
        $machineLearningId = mysqli_insert_id($conn);

        // Fetch current stock quantity and closest expiry date
        $getStockQuery = "SELECT StockQuantity, StockExpiryDate FROM medicineinventory WHERE MedicineBrandID = ? ORDER BY StockExpiryDate ASC LIMIT 1";
        $getStockStmt = mysqli_prepare($conn, $getStockQuery);
        mysqli_stmt_bind_param($getStockStmt, "i", $medicineGivens[$i]);
        mysqli_stmt_execute($getStockStmt);
        mysqli_stmt_bind_result($getStockStmt, $stockQuantity, $stockExpiryDate);
        mysqli_stmt_fetch($getStockStmt);
        mysqli_stmt_close($getStockStmt);

        // Calculate new stock quantity after usage
        $newStockQuantity = $stockQuantity - $quantities[$i];

        // Update stock quantity in medicineinventory table
        $updateStockQuery = "UPDATE medicineinventory SET StockQuantity = ? WHERE MedicineBrandID = ? AND StockExpiryDate = ?";
        $updateStockStmt = mysqli_prepare($conn, $updateStockQuery);
        mysqli_stmt_bind_param($updateStockStmt, "iis", $newStockQuantity, $medicineGivens[$i], $stockExpiryDate);
        mysqli_stmt_execute($updateStockStmt);
        mysqli_stmt_close($updateStockStmt);
    }

     // Extract equipment data from the form
$equipmentTypes = $_POST['equipmentType'];
$equipmentAmounts = $_POST['equipmentAmount'];

// Insert equipment data with patient ID
for ($i = 0; $i < count($equipmentTypes); $i++) {
    // Fetch equipment name from equipment table
    $getEquipmentNameQuery = "SELECT Name FROM equipment WHERE EquipmentID = ?";
    $getEquipmentNameStmt = mysqli_prepare($conn, $getEquipmentNameQuery);
    mysqli_stmt_bind_param($getEquipmentNameStmt, "i", $equipmentTypes[$i]);
    mysqli_stmt_execute($getEquipmentNameStmt);
    mysqli_stmt_bind_result($getEquipmentNameStmt, $equipmentName);
    mysqli_stmt_fetch($getEquipmentNameStmt);
    mysqli_stmt_close($getEquipmentNameStmt);

    // Insert into equipmentusage table
    $equipmentInsertQuery = "INSERT INTO equipmentusage (TreatmentID, EquipmentID, EquipmentName, Quantity) VALUES (?, ?, ?, ?)";
    $equipmentStmt = mysqli_prepare($conn, $equipmentInsertQuery);
    mysqli_stmt_bind_param($equipmentStmt, "iisi", $treatmentId, $equipmentTypes[$i], $equipmentName, $equipmentAmounts[$i]);
    mysqli_stmt_execute($equipmentStmt);
    $equipmentId = mysqli_insert_id($conn);

    // Fetch current stock quantity
    $getEquipmentStockQuery = "SELECT Quantity FROM equipmentstock WHERE EquipmentID = ? LIMIT 1";
    $getEquipmentStockStmt = mysqli_prepare($conn, $getEquipmentStockQuery);
    mysqli_stmt_bind_param($getEquipmentStockStmt, "i", $equipmentTypes[$i]);
    mysqli_stmt_execute($getEquipmentStockStmt);
    mysqli_stmt_bind_result($getEquipmentStockStmt, $equipmentStockQuantity);
    mysqli_stmt_fetch($getEquipmentStockStmt);
    mysqli_stmt_close($getEquipmentStockStmt);

    // Calculate new stock quantity after usage
    $newEquipmentStockQuantity = $equipmentStockQuantity - $equipmentAmounts[$i];

    // Update stock quantity in equipmentstock table
    $updateEquipmentStockQuery = "UPDATE equipmentstock SET Quantity = ? WHERE EquipmentID = ?";
    $updateEquipmentStockStmt = mysqli_prepare($conn, $updateEquipmentStockQuery);
    mysqli_stmt_bind_param($updateEquipmentStockStmt, "ii", $newEquipmentStockQuantity, $equipmentTypes[$i]);
    mysqli_stmt_execute($updateEquipmentStockStmt);
    mysqli_stmt_close($updateEquipmentStockStmt);
}


    // Close database connection
    mysqli_close($conn);
        session_start();
    $_SESSION['successPatientModal'] = true;
}
?>
