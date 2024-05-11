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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel='stylesheet' href='https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css'>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="hamburgers.css" rel="stylesheet">
  <link href="patient.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
<script type="text/javascript" src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">


  <title>Patient Dashboard</title>
  <style>
    table.dataTable thead .sorting:before, table.dataTable thead .sorting_asc:before, table.dataTable thead .sorting_desc:before, table.dataTable thead .sorting_asc_disabled:before, table.dataTable thead .sorting_desc_disabled:before {
        content: "\e5d8" !important; /* Font Awesome icon for ascending sort */
        font-family: 'Material Icons';
        right: 1em !important;
    }

    table.dataTable thead .sorting:after, table.dataTable thead .sorting_asc:after, table.dataTable thead .sorting_desc:after, table.dataTable thead .sorting_asc_disabled:after, table.dataTable thead .sorting_desc_disabled:after {
        content: "\e5db" !important; /* Font Awesome icon for descending sort */
        font-family: 'Material Icons';
        right: 0.5em !important;
    }
  </style>
</head>
<body style="margin: 0;">
    <div class="container-fluid">
        <div class="main-container">
            <!-- Header and Sidebar -->
            <?php include 'patient_header.php'; ?>
            <!-- Content -->
            <div class="content" id="content">
           
                <div class="row justify-content-center d-flex" >
                    <div class="col-md-10 mt-0 pt-0 ">
                        <div class="card p-5">
                        <div class="card-body  p-0 align-items-center">
                            <section  id="my-section"> 
                        <div id="pdfContent" class="html-content" style="margin-top: 0;">
                            <div class="col-md-12 justify-content-center align-items-center d-flex pb-4" style="border-bottom: 1px solid black;">
                          <img src="ABC-Vax-Header.png">
                            </div>
                            <div class="col-md-12">
                                <div class="my-3">
                        <h4 style="color:#0449A6;"> <b>  Personal Information </b> <br></h4>
                        </div>
                                <div class="row d-flex">
                                    
                                <div class="col-md-6 mb-4">
                              
                                <b>Patient Name:</b> <?php echo $firstName . ' ' . $middleName . ' ' . $lastName; ?><br>
    <b>Age:</b> <?php echo $age; ?><br>
    <b>Sex:</b> <?php echo $sex; ?><br>
    <b>Weight:</b> <?php echo $weight; ?><br>
                                </div>
                                <div class="col-md-6 justify-content-end d-flex">
    <div>
        <b>Phone Number:</b> <?php echo $contactLineNumber; ?><br>
        <b>Birth Date:</b> <?php echo $birthDate; ?><br>
    </div>
</div>


<div class="col-lg-4 form-group m-0">
    <label for="Province">Province</label>
    <input type="text" id="Province" name="Province" class="form-control" placeholder="Province" value="<?php echo $province; ?>" required readonly><br><br>
</div>
<div class="col-lg-4 form-group m-0">
    <label for="city">City</label>
    <input type="text" id="city" name="city" class="form-control" placeholder="City" value="<?php echo $city; ?>" required readonly><br><br>
</div>
<div class="col-lg-4 form-group m-0">
    <label for="address">Address</label>
    <input type="text" id="address" name="address" class="form-control" placeholder="Address" value="<?php echo $address; ?>" required readonly><br><br>
</div>

<div class="col-lg-4 form-group m-0">
    <label for="emergencyContact">In case of Emergency, notify</label>
    <input type="text" id="emergencyContact" name="emergencyContact" class="form-control" placeholder="Full Name" value="<?php echo $emergencyContactFullName; ?>" required readonly><br><br>
</div>
<div class="col-lg-4 form-group m-0">
    <label for="Relationship">Relationship</label>
    <input type="text" id="Relationship" name="Relationship" class="form-control" placeholder="Relationship" value="<?php echo $emergencyContactRelationship; ?>" required readonly><br><br>
</div>
<div class="col-lg-4 form-group m-0">
    <label for="phoneNumber">Phone Number<span class="red">*</span></label>
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text" style="color: white; background-color: #5E6E82; font-size: 14px;"><b>PH </b></span>
        </div>
        <input type="tel" id="phoneNumber" name="phoneNumber" class="form-control" placeholder="09123456789" style="min-width: 140px" value="<?php echo $emergencyContactLineNumber; ?>" required readonly>
    </div>
    <small id="phone-number-error" class="error-message"></small>
</div>
            

      <div class="col-md-12 py-4 " style="border-top: 1px solid black; color:#0449a6;">
                        
      <h4><b> Bite Exposure Details</h4></b>
</div>
               <div class="row d-flex ">
                        <div class="col-lg-6 form-group ">
            <label for="exposureDate">Date of Exposure</label>
            <input type="date" id="exposureDate" name="exposureDate" class="form-control" placeholder="Date of Exposure" value="<?php echo $exposureDate ?>" max="<?php echo date('Y-m-d'); ?>" required readonly>
        </div>
        <div class="col-lg-6 form-group">
            <label for="TreatmentDate   ">Date of Treatment</label>
            <input type="date" id="TreatmentDate" name="TreatmentDate" class="form-control" placeholder="Date of Exposure"  value="<?php echo $dateOfTreatment ?>"  max="<?php echo date('Y-m-d'); ?>" required readonly>
        </div>
        
        <div class="col-lg-3 form-group m-0">
            <label for="animalType">Type of Exposure</label>
            <input type="tel" id="animalType" name="animalType" placeholder="Type of Animal" class="form-control"  value="<?php echo $exposureType ?>" required readonly><br><br>
        </div>
        <div class="col-lg-3 form-group m-0">
            <label for="animalType">Exposure By</label>
            <input type="text" id="animalType" name="animalType" placeholder="Type of Animal" class="form-control"  value="<?php echo $exposureMethod ?>" required readonly><br><br>
        </div>
 
        <div class="col-lg-3 form-group m-0">
            <label for="animalType">Type of Animal</label>
            <input type="text" id="animalType" name="animalType" placeholder="Type of Animal" class="form-control"  value="<?php echo $animalType?>" required readonly><br><br>
        </div>
        <div class="col-lg-3 form-group m-0 ">
            <label for="biteLocation">Bite Location</label>
            <input type="text" id="biteLocation" name="biteLocation" placeholder="Bite Location" class="form-control" value= "<?php echo $biteLocation ?>" required readonly><br><br>
        </div>
        </div>
 
        <div class="col-md-12 py-4 " style="border-top: 1px solid black; color:#0449a6;">
                        
                        <h4><b> Treatment Given</h4></b>
                  </div>
        <div class="row d-flex ">
        <?php
$medicineNames = array();
foreach ($medicines as $medicine) {
    $medicineNames[] = $medicine['MedicineName'];
}
$medicineNamesString = implode(', ', $medicineNames);
?>
<div class="col-lg-6 form-group">
    <label for="medicineGiven">Type of Medicine</label>
    <input type="text" id="medicineGiven" name="medicineGiven" class="form-control" placeholder="Type of Medicine" value="<?php echo $medicineNamesString; ?>" readonly>
</div>
        <?php
$medicineBrands = array();
foreach ($medicines as $medicine) {
    $medicineBrands[] = $medicine['MedicineBrand'];
}
$medicineBrandsString = implode(', ', $medicineBrands);
?>
<div class="col-lg-6 form-group">
    <label for="medicineGiven">Medicine Given</label>
    <input type="text" id="medicineGiven" name="medicineGiven" class="form-control" placeholder="Medicine Given" value="<?php echo $medicineBrandsString; ?>" readonly>
</div>
        
 
        <div class="col-lg-4 form-group m-0">
            <label for="animalType">Treatment Category</label>
            <input type="text" id="animalType" name="animalType" placeholder="Type of Animal" class="form-control" value="<?php echo $category ?>" required readonly><br><br>
        </div>
 
        <div class="col-lg-4 form-group m-0">
            <label for="animalType">Sessions</label>
            <input type="text" id="animalType" name="animalType" placeholder="Type of Animal" class="form-control"  value="<?php echo $session ?>"required readonly><br><br>
        </div>
        <?php
$routes = array();
foreach ($medicines as $medicine) {
    $routes[] = $medicine['Route'];
}
$routesString = implode(', ', $routes);
?>
<div class="col-lg-4 form-group m-0">
    <label for="biteLocation">Route</label>
    <input type="text" id="biteLocation" name="biteLocation" class="form-control" placeholder="Route" value="<?php echo $routesString; ?>" readonly>
</div>
        <div class="col-lg-12 form-group">
        <label for="doctorRemarks">Doctor Remarks</label>
        <textarea id="doctorRemarks" name="doctorRemarks" placeholder="Doctor Remarks" class="form-control w-100" readonly><?php echo $recommendation ?? "None"; ?></textarea>

    </div>
        </div>
        </div>
        <div class="col-lg-12 d-flex justify-content-center">
        <button id="generatePdfButton" onclick="CreatePDFfromHTML()" style="background-color:#0449a6; border-radius:9px;" >
    <img src="ph_download-fill.png" alt="Download Icon">
    Download PDF
</button>
</div>

    
    </div>
     
    </div>
     
    </div>
     
    </div>
    
</div>

                    </div>
                
            </div> <!-- End of content -->
        </div> <!-- End of main-container -->
    </div> <!-- End of container-fluid -->
    
   
    <!-- Your script tags here -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src='https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js'></script>
<script src='https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js'></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>

document.addEventListener('DOMContentLoaded', function() {
    var pdfContent = document.getElementById('pdfContent');
    pdfContent.style.marginTop = '0'; // Set the top margin to 0
});
</script>
<script>
    function CreatePDFfromHTML() {
        // Hide the button
        var generatePdfButton = document.getElementById('generatePdfButton');
        generatePdfButton.style.display = 'none';

        // Rest of your PDF generation code
        var HTML_Width = $(".html-content").width();
        var HTML_Height = $(".html-content").height();
        var top_left_margin = 15;
        var PDF_Width = HTML_Width + (top_left_margin * 2);
        var PDF_Height = (PDF_Width * 1.5) + (top_left_margin * 2);
        var canvas_image_width = HTML_Width;
        var canvas_image_height = HTML_Height;

        var totalPDFPages = Math.ceil(HTML_Height / PDF_Height) - 1;

        html2canvas($(".html-content")[0]).then(function (canvas) {
            var imgData = canvas.toDataURL("image/jpeg", 1.0);
            var pdf = new jsPDF('p', 'pt', [PDF_Width, PDF_Height]);
            pdf.addImage(imgData, 'JPG', top_left_margin, top_left_margin, canvas_image_width, canvas_image_height);
            for (var i = 1; i <= totalPDFPages; i++) { 
                pdf.addPage(PDF_Width, PDF_Height);
                pdf.addImage(imgData, 'JPG', top_left_margin, -(PDF_Height*i)+(top_left_margin*4),canvas_image_width,canvas_image_height);
            }
            pdf.save("Your_PDF_Name.pdf");

            // Show the button after a delay
            setTimeout(function () {
                showGeneratePDFButton();
            }, 1000); // Adjust the delay as needed
        });
    }

    // Function to show the button
    function showGeneratePDFButton() {
        var generatePdfButton = document.getElementById('generatePdfButton');
        generatePdfButton.style.display = 'block';
    }
</script>


<script>
  document.getElementById("profileDropdown").addEventListener("mousedown", function(event) {
    event.preventDefault(); // Prevent the default action of the mousedown event
    var dropdownContent = document.getElementById("dropdownContent");

    // Check if the clicked element is within the dropdown content
    if (!dropdownContent.contains(event.target)) {
        // Clicked outside the dropdown content, toggle its visibility
        if (dropdownContent.style.display === "block") {
            dropdownContent.style.display = "none";
        } else {
            dropdownContent.style.display = "block";
        }
    }
});

</script>





<script>
 // Get all sidebar items
const sidebarItems = document.querySelectorAll('.sidebar-item');

// Loop through each sidebar item
sidebarItems.forEach(function(sidebarItem) {
    // Find the icon within the sidebar item
    const sidebarIcon = sidebarItem.querySelector('.sidebar-icon');

    // Get the paths to the default and hover icons from data attributes
    const defaultIcon = sidebarItem.dataset.defaultIcon;
    const hoverIcon = sidebarItem.dataset.hoverIcon;

    // Add mouseenter event listener
    sidebarItem.addEventListener('mouseenter', function() {
        // Change the source of the icon to the hover icon upon hover
        sidebarIcon.src = hoverIcon;
    });

    // Add mouseleave event listener
    sidebarItem.addEventListener('mouseleave', function() {
        // Change the source of the icon back to the default icon upon mouse leave
        sidebarIcon.src = defaultIcon;
    });
});

$(document).ready(function () {
    $('#sidebarCollapse1').on('click', function () {
        $('#sidebar').toggleClass('collapsed'); // Toggle 'collapsed' class on #sidebar
        $('#content').toggleClass('collapsed'); // Toggle 'collapsed' class on #content
    });
});


</script>


<script>
    $(document).ready(function(){
        $('.navbar-toggler').on('click', function () {
            $('.navbar').toggleClass('navbar-collapsed', $('.collapse').hasClass('show'));
        });
    });
</script>
</body>
</html>