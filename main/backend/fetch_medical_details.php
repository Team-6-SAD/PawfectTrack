<?php
// Include your database connection file
require_once 'pawfect_connect.php';

// Check if the appointmentID is set in the POST data
if(isset($_POST['appointmentID'])) {
    $appointmentID = $_POST['appointmentID'];

    // Prepare SQL query to fetch medical details based on the appointment ID
    $sql_medical = "SELECT DISTINCT mu.MedicineName, mu.Dosage, mu.TreatmentID, mu.MedicineBrand, mu.Quantity 
                    FROM medicineusage AS mu
                    WHERE mu.TreatmentID IN (
                        SELECT ai.TreatmentID 
                        FROM appointmentinformation AS ai 
                        WHERE ai.AppointmentID = ?
                    )";

    // Prepare and execute the SQL query for medical details
    $stmt_medical = mysqli_prepare($conn, $sql_medical);
    mysqli_stmt_bind_param($stmt_medical, "i", $appointmentID);
    mysqli_stmt_execute($stmt_medical);
    $result_medical = mysqli_stmt_get_result($stmt_medical);

    // Initialize an empty array to store medical details
    $medicalDetails = array();

    // Check if there are any medical results
    if(mysqli_num_rows($result_medical) > 0) {
        // Fetch each row and add it to the medical details array
        while($row = mysqli_fetch_assoc($result_medical)) {
            $medicalDetails[] = array(
                'treatmentID' => $row['TreatmentID'],
                'medicineBrand' => $row['MedicineBrand'],
                'quantity' => $row['Quantity'],
                'dosage' => $row['Dosage'],
                'medicineName' => $row['MedicineName']
            );
        }
    }

    // Prepare SQL query to fetch equipment details based on the appointment ID
    $sql_equipment = "SELECT DISTINCT eu.EquipmentName, eu.Quantity, eu.TreatmentID 
                      FROM equipmentusage AS eu
                      WHERE eu.TreatmentID IN (
                          SELECT ai.TreatmentID 
                          FROM appointmentinformation AS ai 
                          WHERE ai.AppointmentID = ?
                      )";

    // Prepare and execute the SQL query for equipment details
    $stmt_equipment = mysqli_prepare($conn, $sql_equipment);
    mysqli_stmt_bind_param($stmt_equipment, "i", $appointmentID);
    mysqli_stmt_execute($stmt_equipment);
    $result_equipment = mysqli_stmt_get_result($stmt_equipment);

    // Initialize an empty array to store equipment details
    $equipmentDetails = array();

    // Check if there are any equipment results
    if(mysqli_num_rows($result_equipment) > 0) {
        // Fetch each row and add it to the equipment details array
        while($row = mysqli_fetch_assoc($result_equipment)) {
            $equipmentDetails[] = array(
                'treatmentID' => $row['TreatmentID'],
                'equipmentName' => $row['EquipmentName'],
                'quantity' => $row['Quantity']
            );
        }
    }

    // Prepare the response array combining medical and equipment details
    $response = array(
        'medicalDetails' => $medicalDetails,
        'equipmentDetails' => $equipmentDetails
    );

    // Return the combined details as JSON response
    echo json_encode($response);
} else {
    // If appointmentID is not set in the POST data
    echo "Error: Appointment ID not provided.";
}

// Close the database connection
mysqli_close($conn);
?>
