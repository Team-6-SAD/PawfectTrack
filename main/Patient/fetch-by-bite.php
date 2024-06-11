<?php
require_once '../backend/pawfect_connect.php';
session_start();

$userID = $_SESSION['userID'];
$exposureDate = $_POST['exposureDate'];

// Fetch PatientID associated with the userID
$stmt = $conn->prepare("SELECT PatientID FROM usercredentials WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $patientID = $row['PatientID'];

    // Fetch patient and related information
    $stmt = $conn->prepare("SELECT p.FirstName, p.LastName, p.MiddleName, p.Age, p.Sex, p.Weight, p.BirthDate, ci.LineNumber AS ContactLineNumber,
                            pa.Province, pa.City, pa.Address,
                            ec.FullName AS EmergencyContactFullName, ec.Relationship AS EmergencyContactRelationship, ec.LineNumber AS EmergencyContactLineNumber,
                            bd.ExposureDate, bd.AnimalType, bd.ExposureType, bd.BiteLocation, bd.ExposureMethod, bd.BiteID,
                            t.DateofTreatment, t.Category, t.Session, t.Recommendation, t.TreatmentID
                            FROM patient p
                            LEFT JOIN contactinformation ci ON p.PatientID = ci.PatientID
                            LEFT JOIN patientaddress pa ON p.PatientID = pa.PatientID
                            LEFT JOIN emergencycontact ec ON p.PatientID = ec.PatientID
                            LEFT JOIN bitedetails bd ON p.PatientID = bd.PatientID
                            LEFT JOIN treatment t ON bd.BiteID = t.BiteID
                            WHERE p.PatientID = ? AND bd.ExposureDate = ?");
    $stmt->bind_param("is", $patientID, $exposureDate);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch patient information
        $patientInfo = $result->fetch_assoc();

        // Fetch MedicineBrand using TreatmentID
        $stmt = $conn->prepare("SELECT mu.MedicineName, mu.MedicineBrand, mb.Route
                                FROM medicineusage mu
                                INNER JOIN medicinebrand mb ON mu.MedicineBrand = mb.BrandName
                                WHERE mu.TreatmentID = ?");
        $stmt->bind_param("i", $patientInfo['TreatmentID']);
        $stmt->execute();
        $medicineResult = $stmt->get_result();

        // Array to hold medicine information
        $medicines = array();

        if ($medicineResult->num_rows > 0) {
            // Fetch medicine information
            while ($medicineRow = $medicineResult->fetch_assoc()) {
                $medicines[] = array(
                    'MedicineBrand' => $medicineRow['MedicineBrand'],
                    'Route' => $medicineRow['Route'],
                    'MedicineName' => $medicineRow['MedicineName']
                );
            }
        }

        // Combine patient and medicine info
        $patientInfo['Medicines'] = $medicines;

        // Return the data as JSON
        echo json_encode($patientInfo);
    } else {
        echo json_encode(['error' => 'Patient information not found.']);
    }
} else {
    echo json_encode(['error' => 'User not associated with a patient.']);
}
?>
