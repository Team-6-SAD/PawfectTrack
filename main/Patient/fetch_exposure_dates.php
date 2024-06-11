<?php
session_start();
require_once '../backend/pawfect_connect.php';

$userID = $_SESSION['userID'];

// Fetch PatientID associated with the userID
$stmt = $conn->prepare("SELECT PatientID FROM usercredentials WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $patientID = $row['PatientID'];

    // Fetch all unique ExposureDate values for the PatientID
    $stmt = $conn->prepare("SELECT DISTINCT ExposureDate FROM bitedetails WHERE PatientID = ?");
    $stmt->bind_param("i", $patientID);
    $stmt->execute();
    $result = $stmt->get_result();

    $dates = [];
    while ($row = $result->fetch_assoc()) {
        $dates[] = $row['ExposureDate'];
    }

    echo json_encode($dates);
}
?>
