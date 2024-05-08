<?php
require_once 'pawfect_connect.php';
// Check if selectedRows is set in the POST data
// Check if selectedRows is set in the POST data
if (isset($_POST['selectedRows'])) {
    $selectedRows = $_POST['selectedRows'];
    echo "Selected Rows: " . implode(', ', $selectedRows); // Debugging statement

    // Variable to track if any record is processed
    $processed = false;

    // Loop through each selected row
    foreach ($selectedRows as $selectedRow) {
        // Explode the selectedRow by commas to get individual PatientID values
        $patientIds = explode(',', $selectedRow);

        // Loop through each PatientID
        foreach ($patientIds as $patientId) {
            // Sanitize the PatientID to prevent SQL injection
            $patientId = mysqli_real_escape_string($conn, $patientId);

            // Update archiveStatus for each table
            $tables = array("appointmentinformation", "bitedetails", "contactinformation", "emergencycontact", "patient", "patientaddress", "treatment", "usercredentials");

            foreach ($tables as $table) {
                // Prepare the SQL query
                $sqlUpdateArchiveStatus = "UPDATE $table SET ActiveStatus = 'Active' WHERE PatientID = '$patientId'";

                // Execute the query
                if (!$conn->query($sqlUpdateArchiveStatus)) {
                    echo "Error updating archiveStatus for $table: " . $conn->error;
                    exit; // Exit the script if an error occurs
                }
            }

            // Set processed to true since at least one record is processed
            $processed = true;
        }
    }

    // Set session variable for success message
    session_start();
    $_SESSION['archive_success'] = true;
} else {
    echo "No records selected for updating archiveStatus";
}

// Close the database connection
$conn->close();
header("Location: Archival.php");
exit();

?>