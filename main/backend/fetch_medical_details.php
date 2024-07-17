<?php
// Include your database connection file
require_once 'pawfect_connect.php';

// Initialize response arrays
$responseMedical = array();
$responseEquipment = array();
$responseMedicineStock = array(); // Array for storing medicine inventory details
$responseEquipmentStock = array(); // Array for storing equipment stock details

// Check if the appointmentID is set in the POST data
if(isset($_POST['appointmentID'])) {
    $appointmentID = $_POST['appointmentID'];

    // Prepare SQL query to fetch medical details based on the appointment ID
$sql_medical = "SELECT mu.MedicineName, mu.Dosage, mu.TreatmentID, mu.MedicineBrand, mu.Quantity 
                FROM medicineusage AS mu
                WHERE mu.TreatmentID IN (
                    SELECT ai.TreatmentID 
                    FROM appointmentinformation AS ai 
                    WHERE ai.AppointmentID = ?
                )
                GROUP BY mu.MedicineName, mu.Dosage, mu.TreatmentID, mu.MedicineBrand, mu.Quantity";

    // Prepare and execute the SQL query for medical details
    $stmt_medical = mysqli_prepare($conn, $sql_medical);
    mysqli_stmt_bind_param($stmt_medical, "i", $appointmentID);
    mysqli_stmt_execute($stmt_medical);
    $result_medical = mysqli_stmt_get_result($stmt_medical);

    // Check if there are any medical results
    if(mysqli_num_rows($result_medical) > 0) {
        // Fetch each row and add it to the medical details array
        while($row = mysqli_fetch_assoc($result_medical)) {
            $medicineBrand = $row['MedicineBrand'];
            
            // Fetch MedicineBrandID from medicinebrand table
            $sql_brand = "SELECT MedicineBrandID FROM medicinebrand WHERE BrandName = ?";
            $stmt_brand = mysqli_prepare($conn, $sql_brand);
            mysqli_stmt_bind_param($stmt_brand, "s", $medicineBrand);
            mysqli_stmt_execute($stmt_brand);
            mysqli_stmt_bind_result($stmt_brand, $medicineBrandID);
            mysqli_stmt_fetch($stmt_brand);
            
            // Close statement for fetching MedicineBrandID
            mysqli_stmt_close($stmt_brand);

            // Fetch StockQuantity from medicineinventory for current MedicineBrandID
            $sql_stock = "SELECT SUM(StockQuantity) AS totalStock FROM medicineinventory WHERE MedicineBrandID = ?";
            $stmt_stock = mysqli_prepare($conn, $sql_stock);
            mysqli_stmt_bind_param($stmt_stock, "i", $medicineBrandID);
            mysqli_stmt_execute($stmt_stock);
            mysqli_stmt_bind_result($stmt_stock, $totalStock);
            mysqli_stmt_fetch($stmt_stock);

            // Add medical detail with StockQuantity to the response array
            $responseMedical[] = array(
                'treatmentID' => $row['TreatmentID'],
                'medicineBrand' => $row['MedicineBrand'],
                'quantity' => $row['Quantity'],
                'dosage' => $row['Dosage'],
                'medicineName' => $row['MedicineName'],
                'stockQuantity' => $totalStock // Include StockQuantity in medical details
            );

            // Add medicine stock detail to separate array
            $responseMedicineStock[] = array(
                'medicineBrand' => $row['MedicineBrand'],
                'stockQuantity' => $totalStock
            );

            // Close the statement for fetching StockQuantity
            mysqli_stmt_close($stmt_stock);
        }
    }

    // Prepare SQL query to fetch equipment details based on the appointment ID
$sql_equipment = "SELECT eu.EquipmentName, SUM(eu.Quantity) AS QuantityUsed, eu.TreatmentID,
                         es.Quantity AS StockQuantity
                  FROM equipmentusage AS eu
                  INNER JOIN equipment AS e ON eu.EquipmentName = e.Name
                  INNER JOIN equipmentstock AS es ON e.EquipmentID = es.EquipmentID
                  WHERE eu.TreatmentID IN (
                      SELECT ai.TreatmentID 
                      FROM appointmentinformation AS ai 
                      WHERE ai.AppointmentID = ?
                  )
                  GROUP BY eu.EquipmentName, eu.TreatmentID, es.Quantity";


    // Prepare and execute the SQL query for equipment details
    $stmt_equipment = mysqli_prepare($conn, $sql_equipment);
    mysqli_stmt_bind_param($stmt_equipment, "i", $appointmentID);
    mysqli_stmt_execute($stmt_equipment);
    $result_equipment = mysqli_stmt_get_result($stmt_equipment);

    // Check if there are any equipment results
    if(mysqli_num_rows($result_equipment) > 0) {
        // Fetch each row and add it to the equipment details array
        while($row = mysqli_fetch_assoc($result_equipment)) {
            $responseEquipment[] = array(
                'treatmentID' => $row['TreatmentID'],
                'equipmentName' => $row['EquipmentName'],
                'quantity' => $row['QuantityUsed'],
                'stockQuantity' => $row['StockQuantity'] // Include stockQuantity in equipment details
            );

            // Add equipment stock detail to separate array
            $responseEquipmentStock[] = array(
                'equipmentName' => $row['EquipmentName'],
                'stockQuantity' => $row['StockQuantity']
            );
        }
    }

    // Close statements
    mysqli_stmt_close($stmt_medical);
    mysqli_stmt_close($stmt_equipment);
} else {
    // If appointmentID is not set in the POST data
    echo "Error: Appointment ID not provided.";
}

// Prepare the final response array combining medical, equipment, and medicine stock details
$response = array(
    'medicalDetails' => $responseMedical,
    'equipmentDetails' => $responseEquipment,
    'medicineStockDetails' => $responseMedicineStock, // Include medicine stock details
    'equipmentStockDetails' => $responseEquipmentStock // Include equipment stock details
);

// Return the combined details as JSON response
echo json_encode($response);

// Close the database connection
mysqli_close($conn);
?>
