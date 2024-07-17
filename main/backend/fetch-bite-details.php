<?php
require_once 'pawfect_connect.php';

// Get the selected date and patientID from the AJAX request
$date = $_GET['date'];
$patientID = $_GET['patientID'];

// Prepare and execute the SQL query to fetch patient and bite details based on the selected date
$sqlPatientBite = "SELECT p.FirstName, p.MiddleName, p.LastName, p.Sex,
                          c.LineNumber, c.EmailAddress,
                          b.AnimalType, b.ExposureType, b.ExposureDate, b.BiteLocation, b.ExposureMethod, b.BitePicture,
                          t.DateofTreatment
                   FROM patient AS p
                   LEFT JOIN contactinformation AS c ON p.PatientID = c.PatientID
                   LEFT JOIN bitedetails AS b ON p.PatientID = b.PatientID
                   LEFT JOIN treatment AS t ON p.PatientID = t.PatientID
                   WHERE p.PatientID = ? AND b.ExposureDate = ?";

$stmtPatientBite = mysqli_prepare($conn, $sqlPatientBite);
mysqli_stmt_bind_param($stmtPatientBite, "is", $patientID, $date);
mysqli_stmt_execute($stmtPatientBite);
$resultPatientBite = mysqli_stmt_get_result($stmtPatientBite);

// Initialize an array to store bite pictures
$bitePictures = [];

// Check if there is a row returned for patient and bite details
if ($rowPatientBite = mysqli_fetch_assoc($resultPatientBite)) {
    // Patient and bite details retrieved successfully
    $pfirstName = $rowPatientBite['FirstName'];
    $pmiddleName = $rowPatientBite['MiddleName'];
    $plastName = $rowPatientBite['LastName'];
    $sex = $rowPatientBite['Sex'];
    $lineNumber = $rowPatientBite['LineNumber'];
    $emailAddress = $rowPatientBite['EmailAddress'];
    $animalType = $rowPatientBite['AnimalType'];
    $exposureType = $rowPatientBite['ExposureType'];
    $exposureDate = $rowPatientBite['ExposureDate'];
    $biteLocation = $rowPatientBite['BiteLocation'];
    $exposureMethod = $rowPatientBite['ExposureMethod'];
    $dateofTreatment = $rowPatientBite['DateofTreatment'];

    // Add the first bite picture to the array
    $bitePictures[] = $rowPatientBite['BitePicture'];

    // Fetch additional bite pictures if available
    while ($rowPatientBite = mysqli_fetch_assoc($resultPatientBite)) {
        $bitePictures[] = $rowPatientBite['BitePicture'];
    }

    // Return the data as a JSON object
    echo json_encode([
        'FirstName' => $pfirstName,
        'MiddleName' => $pmiddleName,
        'LastName' => $plastName,
        'Sex' => $sex,
        'LineNumber' => $lineNumber,
        'EmailAddress' => $emailAddress,
        'AnimalType' => $animalType,
        'ExposureType' => $exposureType,
        'ExposureDate' => $exposureDate,
        'BiteLocation' => $biteLocation,
        'ExposureMethod' => $exposureMethod,
        'BitePictures' => $bitePictures,  // Return the array of bite pictures
        'DateofTreatment' => $dateofTreatment
    ]);
} else {
    // No data found for the selected date
    echo json_encode(['error' => 'No bite details found for the selected date.']);
}

// Close the statement and connection
mysqli_stmt_close($stmtPatientBite);
mysqli_close($conn);
?>
