<?php
session_start();

// Check if the 'admin' session variable is not set or is false (user not logged in)
if (!isset($_SESSION['user']) || $_SESSION['user'] !== true || !isset($_SESSION['userID'])) {
    // Redirect the user to the login page
    header("Location: Patient Login.php");
    exit(); // Terminate the script
}

// Include your database connection file
require_once '../backend/pawfect_connect.php';

// Get the AdminID from the session
$userID = $_SESSION['userID'];
// Prepare and execute a query to retrieve the PatientID associated with the userID
$stmt = $conn->prepare("SELECT PatientID FROM usercredentials WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Fetch the PatientID
    $row = $result->fetch_assoc();
    $patientID = $row['PatientID'];

    // Prepare and execute a query to retrieve the FirstName and LastName using the PatientID
    $stmt = $conn->prepare("SELECT FirstName, LastName FROM patient WHERE PatientID = ?");
    $stmt->bind_param("i", $patientID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Fetch the FirstName and LastName
        $row = $result->fetch_assoc();
        $firstName = $row['FirstName'];
        $lastName = $row['LastName'];

        // Now you have the FirstName and LastName
        // You can use them as needed in your PHP code
    } else {
        // Patient not found
        // Handle the error or redirect as needed
    }
} else {
    // User not found or multiple users found (should not happen)
    // Handle the error or redirect as needed
}
$stmt = $conn->prepare("SELECT p.FirstName, p.LastName, p.MiddleName, p.Age, p.Sex, p.Weight, p.BirthDate, ci.LineNumber AS ContactLineNumber,
                        pa.Province, pa.City, pa.Address,
                        ec.FullName AS EmergencyContactFullName, ec.Relationship AS EmergencyContactRelationship, ec.LineNumber AS EmergencyContactLineNumber,
                        bd.ExposureDate, bd.AnimalType, bd.ExposureType, bd.BiteLocation, bd.ExposureMethod,
                        t.DateofTreatment, t.Category, t.Session, t.Recommendation, t.TreatmentID
                        FROM patient p
                        LEFT JOIN contactinformation ci ON p.PatientID = ci.PatientID
                        LEFT JOIN patientaddress pa ON p.PatientID = pa.PatientID
                        LEFT JOIN emergencycontact ec ON p.PatientID = ec.PatientID
                        LEFT JOIN bitedetails bd ON p.PatientID = bd.PatientID
                        LEFT JOIN treatment t ON p.PatientID = t.PatientID
                        WHERE p.PatientID = ?");
$stmt->bind_param("i", $patientID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch patient information
    $patientInfo = $result->fetch_assoc();

    // Access patient information
    $firstName = $patientInfo['FirstName'];
    $lastName = $patientInfo['LastName'];
    $middleName = $patientInfo['MiddleName'];
    $age = $patientInfo['Age'];
    $sex = $patientInfo['Sex'];
    $weight = $patientInfo['Weight'];
    $birthDate = $patientInfo['BirthDate'];
    $contactLineNumber = $patientInfo['ContactLineNumber'];
    $province = $patientInfo['Province'];
    $city = $patientInfo['City'];
    $address = $patientInfo['Address'];
    $emergencyContactFullName = $patientInfo['EmergencyContactFullName'];
    $emergencyContactRelationship = $patientInfo['EmergencyContactRelationship'];
    $emergencyContactLineNumber = $patientInfo['EmergencyContactLineNumber'];
    $exposureDate = $patientInfo['ExposureDate'];
    $animalType = $patientInfo['AnimalType'];
    $exposureType = $patientInfo['ExposureType'];
    $biteLocation = $patientInfo['BiteLocation'];
    $exposureMethod = $patientInfo['ExposureMethod'];
    $dateOfTreatment = $patientInfo['DateofTreatment'];
    $category = $patientInfo['Category'];
    $session = $patientInfo['Session'];
    $recommendation = $patientInfo['Recommendation'];
    $treatmentID = $patientInfo['TreatmentID'];

    // Fetch MedicineBrand using TreatmentID
    $stmt = $conn->prepare("SELECT mu.MedicineName, mu.MedicineBrand, mb.Route
                            FROM medicineusage mu
                            INNER JOIN medicinebrand mb ON mu.MedicineBrand = mb.BrandName
                            WHERE mu.TreatmentID = ?");
    $stmt->bind_param("i", $treatmentID);
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
    } else {
        echo "No medicines found for this treatment.";
    }

    // Output or process the fetched patient and treatment-related information as needed
} else {
    echo "Patient information not found.";
}

require('../includes/fpdf/fpdf.php');

$pdf = new FPDF();
$pdf->AddPage();
$pdf->AddFont('Poppins', '', 'C:\xampp2\htdocs\pawfect\main\includes\fpdf\font\Poppins-Regular.afm');
$pdf->AddFont('Poppins', 'B','../Poppins-Bold.afm');
// Set font
$pdf->SetFont('Arial', '', 12);

// Header
$pdf->Image('ABC-Vax-Header.png', 10, 10, 0, 0, 'PNG');
$pdf->Ln(20);

$pdf->SetLineWidth(0.5); // Set line width
$pdf->SetDrawColor(0, 0, 0); // Set line color (black)
$pdf->Line(10,50, 200, 50); // Draw line (adjust coordinates as needed)
$pdf->Ln(10); // Add space after the line

// Personal Information section
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0,30, 'Personal Information', 0, 1, 'C');
$pdf->Ln(5);

// Patient Name
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 10, 'Patient Name:', 0, 0);
$pdf->Cell(0, 10, $firstName . ' ' . $middleName . ' ' . $lastName, 0, 1);

// Age
$pdf->Cell(50, 10, 'Age:', 0, 0);
$pdf->Cell(0, 10, $age, 0, 1);

// Sex
$pdf->Cell(50, 10, 'Sex:', 0, 0);
$pdf->Cell(0, 10, $sex, 0, 1);

// Weight
$pdf->Cell(50, 10, 'Weight:', 0, 0);
$pdf->Cell(0, 10, $weight, 0, 1);

// Phone Number
$pdf->Cell(50, 10, 'Phone Number:', 0, 0);
$pdf->Cell(0, 10, $contactLineNumber, 0, 1);

// Birth Date
$pdf->Cell(50, 10, 'Birth Date:', 0, 0);
$pdf->Cell(0, 10, $birthDate, 0, 1);

$pdf->Output();
?>
