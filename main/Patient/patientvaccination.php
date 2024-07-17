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
// Fetch all unique ExposureDate values for a specific PatientID
$stmt = $conn->prepare("SELECT DISTINCT bd.ExposureDate
                        FROM bitedetails bd
                        WHERE bd.PatientID = ?");
$stmt->bind_param("i", $patientID);
$stmt->execute();
$result = $stmt->get_result();
$exposureDates = [];
while ($row = $result->fetch_assoc()) {
    $exposureDates[] = $row['ExposureDate'];
}

$stmtProfilePic = $conn->prepare("SELECT profilepicture FROM patient WHERE PatientID = (SELECT PatientID FROM usercredentials WHERE UserID = ?)");
$stmtProfilePic->bind_param("i", $userID);
$stmtProfilePic->execute();
$resultProfilePic = $stmtProfilePic->get_result();

if ($resultProfilePic->num_rows === 1) {
    $rowProfilePic = $resultProfilePic->fetch_assoc();
    $profilePicture = $rowProfilePic['profilepicture'];
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
<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
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
    .custom-select-wrapper {
    position: relative;
    display: inline-block;
    width: 100%;
}

.custom-select {
    width: 100%;
    padding-left: 20px;
    padding-right: 20px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 8px;
    appearance: none;
    background-color: #F8F8F8;
    background-image: url('../img/img-dashboard/clarity_calendar-solid.png'), url('../img/img-dashboard/211614_down_b_arrow_icon.png');
    background-repeat: no-repeat, no-repeat;
    background-position: left 10px center, right 10px center;
    background-size: 20px 20px, 10px 10px;
    padding-left: 40px; /* Adjust to give space for the calendar icon */
}
#exposureDates option {
        padding: 12px 12px !important; /* Adjust padding as needed */
    }
    
.custom-select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
  </style>
</head>
<body style="margin: 0;">
    <div class="container-fluid mb-5">
        <div class="main-container">
            <!-- Header and Sidebar -->
            <?php include 'patient_header.php'; ?>
            <!-- Content -->
            <div class="content" id="content">
           
                <div class="row justify-content-center d-flex" >
                    
                    <div class="col-md-10 mt-0 pt-0 ">
                        <div class="col-md-12 justify-content-end d-flex pr-0">
                        <div class="col-md-4 mr-0 pr-0">
                            <div class="custom-select-wrapper">
                    <select id="exposureDates" class="form-control mb-3 custom-select ">
            <option value="" disabled selected class="dropdown-item">Select Date</option>
            <!-- Exposure dates will be populated here -->
          </select>
          </div>
          </div>
          </div>
                        <div class="card p-5">
                        <div class="card-body  p-4 px-5 align-items-center">
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
                                    
                                <div class="col-md-6 mb-4" id="patientInfoContainer">
    <!-- Placeholder for patient information -->
</div>

                                <div class="col-md-6 justify-content-end d-flex">
                                <div id="contactDetailsContainer">
    <!-- Placeholder for contact details -->
</div>
</div>


<div class="col-lg-4 form-group m-0">
    <label for="Province">Province</label>
    <input type="text" id="Province" name="Province" class="form-control" placeholder="Province" required readonly><br><br>
</div>
<div class="col-lg-4 form-group m-0">
    <label for="city">City</label>
    <input type="text" id="city" name="city" class="form-control" placeholder="City"  required readonly><br><br>
</div>
<div class="col-lg-4 form-group m-0">
    <label for="address">Address</label>
    <input type="text" id="address" name="address" class="form-control" placeholder="Address"  required readonly><br><br>
</div>

<div class="col-lg-4 form-group m-0">
    <label for="emergencyContact">In case of Emergency, notify</label>
    <input type="text" id="emergencyContact" name="emergencyContact" class="form-control" placeholder="Full Name" required readonly><br><br>
</div>
<div class="col-lg-4 form-group m-0">
    <label for="Relationship">Relationship</label>
    <input type="text" id="Relationship" name="Relationship" class="form-control" placeholder="Relationship"  required readonly><br><br>
</div>
<div class="col-lg-4 form-group m-0">
    <label for="phoneNumber">Phone Number<span class="red">*</span></label>
    <div class="input-group">

        <input type="tel" id="phoneNumber" name="phoneNumber" class="form-control" placeholder="09123456789" style="min-width: 140px"  required readonly>
    </div>
    <small id="phone-number-error" class="error-message"></small>
</div>
            

      <div class="col-md-12 py-4 " style="border-top: 1px solid black; color:#0449a6;">
                        
      <h4><b> Bite Exposure Details</h4></b>
</div>
               <div class="row d-flex ">
                        <div class="col-lg-6 form-group ">
            <label for="exposureDate">Date of Exposure</label>
            <input type="date" id="exposureDate" name="exposureDate" class="form-control" placeholder="Date of Exposure" max="<?php echo date('Y-m-d'); ?>" required readonly>
        </div>
        <div class="col-lg-6 form-group">
            <label for="TreatmentDate   ">Date of Treatment</label>
            <input type="date" id="TreatmentDate" name="TreatmentDate" class="form-control" placeholder="Date of Treatment"   max="<?php echo date('Y-m-d'); ?>" required readonly>
        </div>
        
        <div class="col-lg-3 form-group m-0">
            <label for="animalType">Type of Exposure</label>
            <input type="tel" id="exposureType" name="exposureType" placeholder="Type of Exposure" class="form-control"   required readonly><br><br>
        </div>
        <div class="col-lg-3 form-group m-0">
            <label for="animalType">Exposure By</label>
            <input type="text" id="exposureMethod" name="exposureMethod" placeholder="Exposure by" class="form-control"  required readonly><br><br>
        </div>
 
        <div class="col-lg-3 form-group m-0">
            <label for="animalType">Type of Animal</label>
            <input type="text" id="animalType" name="animalType" placeholder="Type of Animal" class="form-control"   required readonly><br><br>
        </div>
        <div class="col-lg-3 form-group m-0 ">
            <label for="biteLocation">Bite Location</label>
            <input type="text" id="biteLocation" name="biteLocation" placeholder="Bite Location" class="form-control" required readonly><br><br>
        </div>
        </div>
 
        <div id="treatmentDetailsContainer">
    <!-- Placeholder for treatment details -->
</div>
</div>

        <div class="col-lg-12 d-flex justify-content-center">
        <button id="generatePdfButton2" onclick="PrintPDFfromHTML()" style="background-color:#0449a6; border-radius:9px; border-top-right-radius:0px; border-bottom-right-radius:0px; border-right:1px solid black;" >
        <img src="printer.png" alt="Download Icon" class="button-images">
        Print
            </button>
        <button id="generatePdfButton" onclick="handleGeneratePdfButton()" style="background-color:#0449a6; border-radius:9px; border-top-left-radius:0px; border-bottom-left-radius:0px;" >
    <img src="ph_download-fill.png" alt="Download Icon" class="button-images">
    Download
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
    

  <?php include 'patient-footer.php'; ?>
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
    $(document).ready(function() {
      // Fetch exposure dates on page load
      fetchExposureDates();

      $('#exposureDates').change(function() {
  var exposureDate = $(this).val();
  if (exposureDate) {
    $('#dateModal').modal('hide');
    fetchPatientDetails(exposureDate);
  } else {
    alert('Please select a date.');
  }
});

      function fetchExposureDates() {
        $.ajax({
          url: 'fetch_exposure_dates.php',
          type: 'GET',
          success: function(data) {
            var dates = JSON.parse(data);
            $.each(dates, function(index, value) {
              $('#exposureDates').append('<option value="' + value + '">' + value + '</option>');
            });
          }
        });
      }

      function fetchPatientDetails(exposureDate) {
        $.ajax({
          url: 'fetch-by-bite.php',
          type: 'POST',
          data: { exposureDate: exposureDate },
          success: function(data) {
            var details = JSON.parse(data);
            if (details.error) {
                alert(details.error);
            } else {
                var medicineNames = [];
                var medicineBrands = [];
                var routes = [];
                details.Medicines.forEach(function(medicine) {
                    medicineNames.push(medicine.MedicineName);
                    medicineBrands.push(medicine.MedicineBrand);
                    routes.push(medicine.Route);
                });
                // Populate patient details into HTML elements
                $('#patientInfoContainer').html(`
                    <b>Patient Name:</b> ${details.FirstName} ${details.MiddleName} ${details.LastName}<br>
                    <b>Age:</b> ${details.Age}<br>
                    <b>Sex:</b> ${details.Sex}<br>
                    <b>Weight:</b> ${details.Weight}<br>
                    <!-- Add other patient details here -->
                `);
                $('#contactDetailsContainer').html(`
                    <b>Phone Number:</b> ${details.ContactLineNumber}<br>
                    <b>Birth Date:</b> ${details.BirthDate}<br>
                    <!-- Add other contact details here -->
                `);
                $('#Province').val(details.Province);
                $('#city').val(details.City);
                $('#address').val(details.Address);
                $('#emergencyContact').val(details.EmergencyContactFullName);
                $('#Relationship').val(details.EmergencyContactRelationship);
                $('#phoneNumber').val(details.EmergencyContactLineNumber);
                $('#exposureDate').val(details.ExposureDate);
                var treatmentDate = new Date(details.DateofTreatment);
var formattedDate = treatmentDate.toISOString().split('T')[0]; // Extract 'yyyy-MM-dd' part

// Set formattedDate as the value of #TreatmentDate input
$('#TreatmentDate').val(formattedDate);
                $('#exposureType').val(details.ExposureType);
                $('#animalType').val(details.AnimalType);
                $('#exposureMethod').val(details.ExposureMethod);
                $('#biteLocation').val(details.BiteLocation);
                $('#treatmentDetailsContainer').html(`
                    <div class="col-md-12 py-4" style="border-top: 1px solid black; color: #0449a6;">
                        <h4><b>Treatment Given</b></h4>
                    </div>
                    <div class="row d-flex">
                        <div class="col-lg-6 form-group">
                            <label for="medicineGiven">Type of Medicine</label>
                            <input type="text" id="medicineGiven" name="medicineGiven" class="form-control" placeholder="Type of Medicine" value="${medicineNames.join(', ')}" readonly>
                        </div>
                        <div class="col-lg-6 form-group">
                            <label for="medicineGiven">Medicine Given</label>
                            <input type="text" id="medicineGiven" name="medicineGiven" class="form-control" placeholder="Medicine Given" value="${medicineBrands.join(', ')}" readonly>
                        </div>
                        <div class="col-lg-4 form-group m-0">
                            <label for="animalType">Treatment Category</label>
                            <input type="text" id="animalType" name="animalType" placeholder="Type of Animal" class="form-control" value="${details.Category}" required readonly>
                        </div>
                        <div class="col-lg-4 form-group m-0">
                            <label for="animalType">Sessions</label>
                            <input type="text" id="animalType" name="animalType" placeholder="Type of Animal" class="form-control" value="${details.Session}" required readonly>
                        </div>
                        <div class="col-lg-4 form-group m-0">
                            <label for="biteLocation">Route</label>
                            <input type="text" id="biteLocation" name="biteLocation" class="form-control" placeholder="Route" value="${routes.join(', ')}" readonly>
                        </div>
                        <div class="col-lg-12 form-group">
                            <label for="doctorRemarks">Doctor Remarks</label>
                            <textarea id="doctorRemarks" name="doctorRemarks" placeholder="Doctor Remarks" class="form-control w-100" readonly>${details.Recommendation}</textarea>
                        </div>
                    </div>
                `);
            }
        }
    });
      }
    });
  </script>
<script>

  
document.addEventListener('DOMContentLoaded', function() {
    var pdfContent = document.getElementById('pdfContent');
    pdfContent.style.marginTop = '0'; // Set the top margin to 0
});
</script>
<script>
    function handleGeneratePdfButton() {  var exposureDate = $('#exposureDates').val();
    if (!exposureDate) {
      alert('Please select a date.');
      return;
    }else{
            var exposureDate = document.getElementById('exposureDates').value;
            window.location.href = 'pdfmaker.php?exposureDate=' + encodeURIComponent(exposureDate);
        }
        }
    function CreatePDFfromHTML() {
        var exposureDate = $('#exposureDates').val();
    if (!exposureDate) {
      alert('Please select a date.');
      return;
    }
    else{
        // Hide the button
        var generatePdfButton = document.getElementById('generatePdfButton');
        var generatePdfButton2 = document.getElementById('generatePdfButton2');
        generatePdfButton.style.display = 'none';
        generatePdfButton2.style.display = 'none';

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

            // Save the PDF
            pdf.save("Vaccination Card.pdf");

            // Print the PDF

            // Show the button after a delay
            setTimeout(function () {
                showGeneratePDFButton();
            }, 1000); // Adjust the delay as needed
        });
    }
}
function PrintPDFfromHTML() {
    var exposureDate = $('#exposureDates').val();
    if (!exposureDate) {
        alert('Please select a date.');
        return;
    } else {
        // Hide the button
        var generatePdfButton = document.getElementById('generatePdfButton');
        var generatePdfButton2 = document.getElementById('generatePdfButton2');
        generatePdfButton.style.display = 'none';
        generatePdfButton2.style.display = 'none';

        // Margins (in points)
        var top_margin = 40; // Adjust top margin
        var left_margin = 100; // Adjust left margin
        var right_margin = 100; // Adjust right margin
        var bottom_margin = 40; // Adjust bottom margin

        // Rest of your PDF generation code
        var HTML_Width = $(".html-content").width();
        var HTML_Height = $(".html-content").height();
        
        var PDF_Width = HTML_Width + left_margin + right_margin;
        var PDF_Height = HTML_Height + top_margin + bottom_margin;

        var canvas_image_width = HTML_Width;
        var canvas_image_height = HTML_Height;

        var totalPDFPages = Math.ceil(HTML_Height / PDF_Height) - 1;

        html2canvas($(".html-content")[0]).then(function (canvas) {
            var imgData = canvas.toDataURL("image/jpeg", 1.0);
            var pdf = new jsPDF('p', 'pt', [PDF_Width, PDF_Height]);
            pdf.addImage(imgData, 'JPEG', left_margin, top_margin, canvas_image_width, canvas_image_height);
            
            for (var i = 1; i <= totalPDFPages; i++) { 
                pdf.addPage(PDF_Width, PDF_Height);
                pdf.addImage(imgData, 'JPEG', left_margin, -(PDF_Height*i) + top_margin, canvas_image_width, canvas_image_height);
            }

            // Save the PDF (if needed)
            // Example: pdf.save('filename.pdf');

            // Print the PDF
            pdf.autoPrint();
            window.open(pdf.output('bloburl'), '_blank');

            // Show the button after a delay
            setTimeout(function () {
                showGeneratePDFButton();
            }, 1000); // Adjust the delay as needed
        });
    }
}

    // Function to show the button
    function showGeneratePDFButton() {
        var generatePdfButton = document.getElementById('generatePdfButton');
        var generatePdfButton2 = document.getElementById('generatePdfButton2');
        generatePdfButton.style.display = 'block';
        generatePdfButton2.style.display = 'block';
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