<?php
// Include your database connection file
require_once 'pawfect_connect.php';

// Check if the appointmentID is set in the POST data
if(isset($_POST['appointmentID'])) {
    $appointmentID = $_POST['appointmentID'];

    // Prepare SQL query to fetch medical details based on the appointment ID
    $sql = "SELECT mu.MedicineName,mu.Dosage, mu.TreatmentID, mu.MedicineBrand, mu.Quantity 
            FROM medicineusage AS mu
            WHERE mu.TreatmentID IN (
                SELECT ai.TreatmentID 
                FROM appointmentinformation AS ai 
                WHERE ai.AppointmentID = ?)";

    // Prepare and execute the SQL query
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $appointmentID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Initialize an empty array to store medical details
    $medicalDetails = array();

    // Check if there are any results
    if(mysqli_num_rows($result) > 0) {
        // Fetch each row and add it to the medical details array
        while($row = mysqli_fetch_assoc($result)) {
            $medicalDetails[] = array(
                'treatmentID' => $row['TreatmentID'],
                'medicineBrand' => $row['MedicineBrand'],
                'quantity' => $row['Quantity'],
                'dosage' => $row['Dosage'],
                'medicineName' => $row['MedicineName']
            );
        }
    }

    // Return the medical details as JSON response
    echo json_encode($medicalDetails);
} else {
    // If appointmentID is not set in the POST data
    echo "Error: Appointment ID not provided.";
}

// Close the database connection
mysqli_close($conn);
?>
