<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection
    include_once 'pawfect_connect.php';

    // Extract patient data from the form
    $fName = $_POST['fName'];
    $mName = $_POST['mName'];
    $lName = $_POST['lName'];
    $birthDate = $_POST['birthDate'];
    $age = $_POST['age'];
    $sex = $_POST['sex'];
    $weight = $_POST['weight'];
    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];
    $province = $_POST['province'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $emergencyContact = $_POST['emergencyContact'];
    $emergencyContactRelationship = $_POST['emergency_contact_relationship'];
    $emergencyPhoneNumber = $_POST['emergencyPhoneNumber'];

    // Insert patient data into the database
    $patientInsertQuery = "INSERT INTO patient (FirstName, MiddleName, LastName, BirthDate, Age, Sex, Weight) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $patientStmt = mysqli_prepare($conn, $patientInsertQuery);
    mysqli_stmt_bind_param($patientStmt, "sssssss", $fName, $mName, $lName, $birthDate, $age, $sex, $weight);
    mysqli_stmt_execute($patientStmt);
    $patientId = mysqli_insert_id($conn); // Get the last inserted patient ID

    // Insert contact information with patient ID
    $contactInsertQuery = "INSERT INTO contactinformation (PatientID, LineNumber, EmailAddress) VALUES (?, ?, ?)";
    $contactStmt = mysqli_prepare($conn, $contactInsertQuery);
    mysqli_stmt_bind_param($contactStmt, "iss", $patientId, $phoneNumber, $email);
    mysqli_stmt_execute($contactStmt);
    $contactId = mysqli_insert_id($conn); // Get the last inserted contact ID

    // Insert patient address with patient ID
    $addressInsertQuery = "INSERT INTO patientaddress (PatientID, Province, City, Address) VALUES (?, ?, ?, ?)";
    $addressStmt = mysqli_prepare($conn, $addressInsertQuery);
    mysqli_stmt_bind_param($addressStmt, "isss", $patientId, $province, $city, $address);
    mysqli_stmt_execute($addressStmt);
    $addressId = mysqli_insert_id($conn); // Get the last inserted address ID

    // Insert emergency contact with patient ID
    $emergencyInsertQuery = "INSERT INTO emergencycontact (PatientID, FullName, Relationship, LineNumber) VALUES (?, ?, ?, ?)";
    $emergencyStmt = mysqli_prepare($conn, $emergencyInsertQuery);
    mysqli_stmt_bind_param($emergencyStmt, "isss", $patientId, $emergencyContact, $emergencyContactRelationship, $emergencyPhoneNumber);
    mysqli_stmt_execute($emergencyStmt);
    $emergencyId = mysqli_insert_id($conn); // Get the last inserted emergency contact ID

    // Handle uploaded image
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["uploadImage"]["name"]);
    move_uploaded_file($_FILES["uploadImage"]["tmp_name"], $targetFile);
    $bitePicture = $targetFile;

    // Extract exposure data from the form
    $exposureDate = $_POST['exposureDate'];
    $exposureBy = $_POST['exposureBy'];
    $exposureType = $_POST['exposureType'];
    $animalType = $_POST['animalType'];
    $biteLocation = $_POST['biteLocation'];

    // Insert exposure data with patient ID
    $exposureInsertQuery = "INSERT INTO bitedetails (PatientID, ExposureDate, ExposureMethod, ExposureType, AnimalType, BiteLocation, BitePicture) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $exposureStmt = mysqli_prepare($conn, $exposureInsertQuery);
    mysqli_stmt_bind_param($exposureStmt, "issssss", $patientId, $exposureDate, $exposureBy, $exposureType, $animalType, $biteLocation, $bitePicture);
    mysqli_stmt_execute($exposureStmt);
    $exposureId = mysqli_insert_id($conn); // Get the last inserted exposure ID

    // Extract treatment data from the form
    $treatmentCategory = $_POST['treatmentCategory'];
    $sessions = $_POST['sessions'];
    $treatmentDate = $_POST['treatmentDate'];
    $doctorRemarks = $_POST['doctorRemarks'];

    // Insert treatment data with patient ID
    $treatmentInsertQuery = "INSERT INTO treatment (PatientID, Category, Session, DateOfTreatment, Recommendation) VALUES (?, ?, ?, ?, ?)";
    $treatmentStmt = mysqli_prepare($conn, $treatmentInsertQuery);
    mysqli_stmt_bind_param($treatmentStmt, "isiss", $patientId, $treatmentCategory, $sessions, $treatmentDate, $doctorRemarks);
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
        mysqli_stmt_bind_param($appointmentStmt, "iissi",$treatmentId, $patientId, $appointmentDate, $status, $sessionDays);
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
        $machineLearningInsertQuery = "INSERT INTO machinelearning (TreatmentID, MedicineBrand, MedicineName, Dosage, Quantity) VALUES (?, ?, ?, ?, ?)";
$machineLearningStmt = mysqli_prepare($conn, $machineLearningInsertQuery);
mysqli_stmt_bind_param($machineLearningStmt, "issii", $treatmentId, $brandName, $medicineName, $dosageQuantities[$i], $quantities[$i]);

// Execute the insert query for machinelearning table
mysqli_stmt_execute($machineLearningStmt);

// Get the last inserted ID for machinelearning table
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
        $equipmentInsertQuery = "INSERT INTO equipmentusage (TreatmentID, EquipmentID, Quantity) VALUES (?, ?, ?)";
        $equipmentStmt = mysqli_prepare($conn, $equipmentInsertQuery);
        mysqli_stmt_bind_param($equipmentStmt, "iii", $treatmentId, $equipmentTypes[$i], $equipmentAmounts[$i]);
        mysqli_stmt_execute($equipmentStmt);
        $equipmentId = mysqli_insert_id($conn);
    }

    // Close database connection
    mysqli_close($conn);
}
?>
