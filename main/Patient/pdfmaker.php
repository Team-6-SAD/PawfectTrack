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
$pdf->AddFont('Poppins','','Poppins-Regular.php');
$pdf->AddFont('Poppins','B','Poppins-Bold.php');
// Set font
$pdf->SetFont('Poppins', '', 12);

// Header
$pdf->Image('ABC-Vax-Header.png', 10, 10, 0, 0, 'PNG');
$pdf->Ln(20);

$pdf->SetLineWidth(0.5); // Set line width
$pdf->SetDrawColor(0, 0, 0); // Set line color (black)
$pdf->Line(10,50, 200, 50); // Draw line (adjust coordinates as needed)
$pdf->Ln(10); // Add space after the line

// Personal Information section
$pdf->SetFont('Poppins', 'B', 12);
$red = hexdec("04");   // Red value in decimal (4)
$green = hexdec("49"); // Green value in decimal (73)
$blue = hexdec("A6");  // Blue value in decimal (166)

// Set text color using RGB values
$pdf->SetTextColor($red, $green, $blue); // Color #0449A6
$pdf->Cell(50, 30, 'Personal Information', 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetTextColor(0, 0, 0); // Reset text color
$pdf->SetFont('Poppins', '', 10);

// Patient Name
$pdf->SetFont('Poppins', 'B', 10);
$pdf->Cell(30, -20, 'Patient Name:', 0, 0);
$pdf->SetFont('Poppins', '', 10);
$pdf->Cell(0, -20, $firstName . ' ' . $middleName . ' ' . $lastName, 0, 1);

// Age
$pdf->SetFont('Poppins', 'B', 10);
$pdf->Cell(10, 30, 'Age:', 0, 0);
$pdf->SetFont('Poppins', '', 10);
$pdf->Cell(0, 30, $age, 0, 1);

// Sex
$pdf->SetFont('Poppins', 'B', 10);
$pdf->Cell(10, -20, 'Sex:', 0, 0);
$pdf->SetFont('Poppins', '', 10);
$pdf->Cell(0, -20, $sex, 0, 1);

// Weight
$pdf->SetFont('Poppins', 'B', 10);
$pdf->Cell(16, 30, 'Weight:', 0, 0);
$pdf->SetFont('Poppins', '', 10);
$pdf->Cell(0, 30, $weight, 0, 1);

// Phone Number
$pdf->Cell(100); // Move the cursor to the right
$pdf->SetFont('Poppins', 'B', 10);
$pdf->Cell(60, -60, 'Phone Number:', 0, 0, 'R'); // Add the text aligned to the right
$pdf->SetFont('Poppins', '', 10);
$pdf->Cell(0, -60, $contactLineNumber, 0, 1); // Change 0 to 1 for line break

// Birth Date
$pdf->Cell(100); // Move the cursor to the right
$pdf->SetFont('Poppins', 'B', 10);
$pdf->Cell(60, 70, 'Birth Date:', 0, 0, 'R'); // Add the text aligned to the right
$pdf->SetFont('Poppins', '', 10);
$pdf->Cell(0, 70, $birthDate, 0, 1); // Change 0 to 1 for line break
// Address label
$pdf->SetFont('Poppins', 'B', 8);
$pdf->Cell(0, -30, 'Address:', 0, 1); // Add the label
$pdf->SetFont('Poppins', '', 8);
// Address
$pdf->Ln(20); // Add some space after the address

// Draw form field box with background color
$pdf->SetFillColor(234, 239, 246); // Set background color to eaeff6    
$pdf->MultiCell(60,10, $address, 0, 'L', true); // Add address inside the box with background color
$pdf->SetX(75);
$pdf->SetFont('Poppins', 'B', 8);
$pdf->Cell(0, -30, 'City:', 0, 1); // Add the label
$pdf->SetFont('Poppins', '', 8);


$pdf->SetFillColor(234, 239, 246); // Set background color to eaeff6    

$pdf->Ln(20);
$pdf->SetX(75);
$pdf->MultiCell(60,10, $city, 0, 'L', true); // Add address inside the box with background color
$pdf->SetX(140);
$pdf->SetFont('Poppins', 'B', 8);
$pdf->Cell(0, -30, 'Province:', 0, 1); // Add the label
$pdf->SetFont('Poppins', '', 8);
$pdf->SetFillColor(234, 239, 246); // Set background color to eaeff6    

$pdf->Ln(20);
$pdf->SetX(140);
$pdf->MultiCell(60,10, $province, 0, 'L', true); // Add address inside the box with background color
$pdf->Ln(20);
$pdf->SetFont('Poppins', 'B', 8);
$pdf->Cell(0, -30, 'In case of Emergency, notify', 0, 1); // Add the label
$pdf->SetFont('Poppins', '', 8);
// Address
$pdf->Ln(20); // Add some space after the address

// Draw form field box with background color
$pdf->SetFillColor(234, 239, 246); // Set background color to eaeff6    
$pdf->MultiCell(60,10, $emergencyContactFullName, 0, 'L', true); // Add address inside the box with background color
$pdf->SetX(75);
$pdf->SetFont('Poppins', 'B', 8);
$pdf->Cell(0, -30, 'Relationship', 0, 1); // Add the label
$pdf->SetFont('Poppins', '', 8);

$pdf->SetFillColor(234, 239, 246); // Set background color to eaeff6    

$pdf->Ln(20);
$pdf->SetX(75);
$pdf->MultiCell(60,10, $emergencyContactRelationship, 0, 'L', true); // Add address inside the box with background color
$pdf->SetX(140);
$pdf->SetFont('Poppins', 'B', 8);
$pdf->Cell(0, -30, 'Phone Number', 0, 1); // Add the label
$pdf->SetFont('Poppins', '', 8);
$pdf->SetFillColor(234, 239, 246); // Set background color to eaeff6    

$pdf->Ln(20);
$pdf->SetX(140);
$pdf->MultiCell(60,10, $emergencyContactLineNumber, 0, 'L', true); // Add address inside the box with background color

$pdf->Ln(15);

$pdf->SetLineWidth(0.5); // Set line width
$pdf->SetDrawColor(0, 0, 0); // Set line color (black)
$pdf->Line(10,135, 200, 135); // Draw line (adjust coordinates as needed)
$pdf->SetFont('Poppins', 'B', 12);
$pdf->SetTextColor($red, $green, $blue); // Color #0449A6
$pdf->Cell(50, 0, 'Bite Exposure Details', 0, 1, 'C');
$pdf->Ln(22);

$pdf->SetTextColor(0, 0, 0); // Reset text color
$pdf->SetFont('Poppins', 'B', 8);

// Draw form field box with background color
$pdf->Cell(0, -30, 'Date of Exposure', 0, 1); // Add the label
$pdf->SetFont('Poppins', '', 8);
// Address
$pdf->Ln(20); // Add some space after the address
$pdf->SetFillColor(234, 239, 246); // Set background color to eaeff6    
$pdf->MultiCell(90,10, $exposureDate, 0, 'L', true); // Add address inside the box with background color
$pdf->SetX(110);
$pdf->SetFont('Poppins', 'B', 8);
$pdf->Cell(0, -30, 'Date of Treatment', 0, 1); // Add the label
$pdf->SetFont('Poppins', '', 8);

$pdf->SetFillColor(234, 239, 246); // Set background color to eaeff6    

$pdf->Ln(20);
$pdf->SetX(110);
$pdf->MultiCell(90,10, $dateOfTreatment, 0, 'L', true); // Add address inside the box with background color

$pdf->Ln(20);
$pdf->SetFont('Poppins', 'B', 8);
$pdf->Cell(0, -30, 'Type of Exposure', 0, 1); // Add the label
$pdf->SetFont('Poppins', '', 8);
// Address
$pdf->Ln(20); // Add some space after the address

// Draw form field box with background color
$pdf->SetFillColor(234, 239, 246); // Set background color to eaeff6    
$pdf->MultiCell(43,10, $exposureType, 0, 'L', true); // Add address inside the box with background color
$pdf->SetX(58);
$pdf->SetFont('Poppins', 'B', 8);
$pdf->Cell(0, -30, 'Exposure By', 0, 1); // Add the label
$pdf->SetFont('Poppins', '', 8);

$pdf->SetFillColor(234, 239, 246); // Set background color to eaeff6    

$pdf->Ln(20);
$pdf->SetX(58);
$pdf->MultiCell(44,10, $exposureMethod, 0, 'L', true); // Add address inside the box with background color
$pdf->SetX(107);
$pdf->SetFont('Poppins', 'B', 8);
$pdf->Cell(0, -30, 'Type of Animal', 0, 1); // Add the label
$pdf->SetFont('Poppins', '', 8);
$pdf->SetFillColor(234, 239, 246); // Set background color to eaeff6    

$pdf->Ln(20);
$pdf->SetX(107);

$pdf->MultiCell(44,10, $animalType, 0, 'L', true); // Add address inside the box with background color

$pdf->SetX(156);
$pdf->SetFont('Poppins', 'B', 8);
$pdf->Cell(0, -30, 'Bite Location', 0, 1); // Add the label
$pdf->SetFont('Poppins', '', 8);
$pdf->SetFillColor(234, 239, 246); // Set background color to eaeff6    

$pdf->Ln(20);
$pdf->SetX(156);
$pdf->MultiCell(44,10, $biteLocation, 0, 'L', true); // Add address inside the box with background color
$pdf->Ln(20);

$pdf->SetLineWidth(0.5); // Set line width
$pdf->SetDrawColor(0, 0, 0); // Set line color (black)
$pdf->Line(10,195, 200, 195); // Draw line (adjust coordinates as needed)
$pdf->SetFont('Poppins', 'B', 12);
$pdf->SetTextColor($red, $green, $blue); // Color #0449A6
$pdf->Cell(43, 0, 'Treatment Given', 0, 1, 'C');
$pdf->Ln(21);
$pdf->SetTextColor(0, 0, 0); // Reset text color
$pdf->SetFont('Poppins', 'B', 8);

// Draw form field box with background color
$medicineNames = array();
foreach ($medicines as $medicine) {
    $medicineNames[] = $medicine['MedicineName'];
}
$medicineNamesString = implode(', ', $medicineNames);
$pdf->Cell(0, -30, 'Type of Medicine', 0, 1); // Add the label
$pdf->SetFont('Poppins', '', 8);
// Address
$pdf->Ln(20); // Add some space after the address
$pdf->SetFillColor(234, 239, 246); // Set background color to eaeff6    
$pdf->MultiCell(90,10, $medicineNamesString, 0, 'L', true); // Add address inside the box with background color
$pdf->SetX(110);
$pdf->SetFont('Poppins', 'B', 8);
$pdf->Cell(0, -30, 'Medicine Given', 0, 1); // Add the label
$pdf->SetFont('Poppins', '', 8);
$medicineBrands = array();
foreach ($medicines as $medicine) {
    $medicineBrands[] = $medicine['MedicineBrand'];
}
$medicineBrandsString = implode(', ', $medicineBrands);
$pdf->SetFillColor(234, 239, 246); // Set background color to eaeff6    

$pdf->Ln(20);
$pdf->SetX(110);
$pdf->MultiCell(90,10, $medicineBrandsString, 0, 'L', true); // Add address inside the box with background color

$pdf->Ln(20);
$pdf->SetFont('Poppins', 'B', 8);
$pdf->Cell(0, -30, 'Treatment Category', 0, 1); // Add the label
$pdf->SetFont('Poppins', '', 8);
// Address
$pdf->Ln(20); // Add some space after the address

// Draw form field box with background color
$pdf->SetFillColor(234, 239, 246); // Set background color to eaeff6    
$pdf->MultiCell(60,10, $category, 0, 'L', true); // Add address inside the box with background color
$pdf->SetX(75);
$pdf->SetFont('Poppins', 'B', 8);
$pdf->Cell(0, -30, 'Sessions', 0, 1); // Add the label
$pdf->SetFont('Poppins', '', 8);

$pdf->SetFillColor(234, 239, 246); // Set background color to eaeff6    

$pdf->Ln(20);
$pdf->SetX(75);
$pdf->MultiCell(60,10, $session, 0, 'L', true); // Add address inside the box with background color
$pdf->SetX(140);
$pdf->SetFont('Poppins', 'B', 8);
$pdf->Cell(0, -30, 'Route', 0, 1); // Add the label
$pdf->SetFont('Poppins', '', 8);
$pdf->SetFillColor(234, 239, 246); // Set background color to eaeff6    
$routes = array();
foreach ($medicines as $medicine) {
    $routes[] = $medicine['Route'];
}
$routesString = implode(', ', $routes);
$pdf->Ln(20);
$pdf->SetX(140);
$pdf->MultiCell(60,10, $routesString, 0, 'L', true); // Add address inside the box with background color
$pdf->Ln(20); // Add some space after the address
// Draw form field box with background color
$pdf->SetFont('Poppins', 'B', 8);
$pdf->Cell(0, -30, 'Doctor Remarks', 0, 1); // Add the label
$pdf->SetFont('Poppins', '', 8);
// Address
$pdf->Ln(20); // Add some space after the address
$pdf->SetFillColor(234, 239, 246); // Set background color to eaeff6    

$pdf->MultiCell(190,20, $recommendation ?? "None", 0,'L', true); // Add address inside the box with background color



// Address

$pdf->Output();

?>
