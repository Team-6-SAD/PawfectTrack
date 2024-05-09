<?php
session_start();

// Check if the 'admin' session variable is not set or is false (user not logged in)
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true || !isset($_SESSION['adminID'])) {
    // Redirect the user to the login page
    header("Location: Admin Login.php");
    exit(); // Terminate the script
}

// Include your database connection file
require_once 'backend/pawfect_connect.php';

// Get the AdminID from the session
$adminID = $_SESSION['adminID'];

// Prepare and execute the SQL query to fetch admin information
$sql = "SELECT * FROM admininformation WHERE AdminID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $adminID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check if there is a row returned
if ($row = mysqli_fetch_assoc($result)) {
    // Admin information retrieved successfully
    $firstName = $row['firstname'];
    $middleName = $row['middlename'];
    $lastName = $row['lastname'];
    $email = $row['email'];
    $phoneNumber = $row['phonenumber'];
    $adminPhoto = $row['adminphoto'];

    // Now you can use these variables to display the admin information in your HTML
} else {
    // Admin information not found
    echo "Admin information not found!";
}

// Check if the patientID is set in the URL
if(isset($_GET['patientID'])) {
    // Retrieve the patientID from the URL
    $patientID = $_GET['patientID'];

    // Query to fetch data from multiple tables using JOIN
    $sql = "SELECT p.LastName, p.FirstName, p.MiddleName, p.Age, p.BirthDate, p.Weight, p.Sex,
                   c.LineNumber AS ContactLineNumber, c.EmailAddress,
                   a.Address, a.City, a.Province,
                   ec.FullName AS EmergencyContactFullName, ec.Relationship, ec.LineNumber AS EmergencyContactLineNumber,
                   t.Recommendation
            FROM patient AS p
            LEFT JOIN contactinformation AS c ON p.PatientID = c.PatientID
            LEFT JOIN patientaddress AS a ON p.PatientID = a.PatientID
            LEFT JOIN emergencycontact AS ec ON p.PatientID = ec.PatientID
            LEFT JOIN treatment AS t ON p.PatientID = t.PatientID
            WHERE p.PatientID = ?";

    // Prepare and execute the SQL query
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $patientID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if there are any rows returned
    if(mysqli_num_rows($result) > 0) {
        // Fetch the data
        $row = mysqli_fetch_assoc($result);

        // Close the statement
        mysqli_stmt_close($stmt);

        // Close the database connection
        mysqli_close($conn);
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="Favicon 2.png" type="image/png">
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <link rel='stylesheet' href='https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css'>
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet"> <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="css/hamburgers.css" rel="stylesheet">
  <link href="css/userdashboard.css" rel="stylesheet">
  <title>Patient Details - Profile</title>
  
</head>
<body>
<div class="container-fluid">
    <div class="main-container">
        <!-- Header and Sidebar -->
        <?php include 'includes/admin_header.php'; ?>
        <div class="sidebar">
            <?php include 'includes/sidebar.php'; ?>
        </div>


<!--Profile Picture and Details-->
        <div class="content" id="content">
    <div class="row mt-4">

        <div class="col-md-12"> 
        <h3 class="text-center main-font-color mt-2"><b>PATIENT DETAILS</b></h3>
</div>
<div class="col-sm-12">
       <div class="row justify-content-center d-flex">
        <div class="col-md-11 justify-content-center">
        <div class="row mt-4">
    <div class="col-md-3 patient-navigation-active text-center">
        <a href="patientdetails-profile.php?patientID=<?php echo $patientID?>" class="text-center link-text-active">
            <img src="img/img-patient-details/profile-blue.png" class="mr-3 nav-logo">Profile
            <hr class="profile-nav-active">
        </a>
    </div>
    <div class="col-md-3 patient-navigation text-center">
        <a href="patientdetails-bitedetails.php?patientID=<?php echo $patientID?>" class="text-center link-text">
            <img src="img/img-patient-details/paw-gray.png" class="mr-3 nav-logo">Bite Exposure Details
            <hr class="profile-nav">
        </a>
    </div>
    <div class="col-md-3 patient-navigation text-center">
        <a href="patientdetails-treatmenthistory.php?patientID=<?php echo $patientID?>" class="text-center link-text">
            <img src="img/img-patient-details/injection-gray.png" class="mr-3 nav-logo">Treatment History
            <hr class="profile-nav">
        </a>
    </div>
    <div class="col-md-3 patient-navigation text-center">
        <a href="patientdetails-appointments.php?patientID=<?php echo $patientID?>" class="text-center link-text">
            <img src="img/img-patient-details/calendar-gray.png" class="mr-3 nav-logo">Appointments
            <hr class="profile-nav">
        </a>
    </div>
</div>
</div>
</div>
</div>
</div>

<div class="col-md-12 d-flex align-items-end justify-content-end pr-5">
<button id="editButton" class="btn btn-blue-color btn-custom mb-2 mb-sm-0 mr-sm-2 pt-2 pb-2" style="color: #FFFFFF;">
    <a href="backend/create-account.php?patientID=<?php echo $patientID; ?>" style="color:inherit; text-decoration:none;">
        <b>Create Patient Account</b>
    </a>
</button>

        </div>
    
        <div class="row justify-content-center  align-items-center d-flex">
        <div class="col-sm-11 col-md-11  ">
            <div class="card mt-4">
         
                <div class="card-body p-5">
                        <h5 class="main-font-color"><b> Personal Information</b> </h5> 
                    

<div class="row d-flex mb-4"> 
    <div class= "mr-0 pl-0 col-md-2">
        <div class="profile-category">First Name</div>
        <div class="profile-category-content"><?php echo $row['FirstName']; ?></div>
    </div>
    <div class= "m-0 col-md-2 p-0">
    <div class="profile-category">Middle Name</div>
        <div class="profile-category-content"><?php echo $row['MiddleName']; ?></div>
    </div>

    <div class= "mr-0 col-md-2 p-0">
    <div class="profile-category">Last Name</div>
        <div class="profile-category-content"><?php echo $row['LastName']; ?></div>
    </div>
    <div class= " col-md-2 p-0">
    <div class="profile-category">Birth Date</div>
        <div class="profile-category-content"><?php echo $row['BirthDate']; ?></div>
    </div>
    <div class= "col-md-1 p-0">
    <div class="profile-category">Age</div>
        <div class="profile-category-content"><?php echo $row['Age']; ?></div>
    </div>
    <div class= "col-md-2 p-0">
    <div class="profile-category">Sex</div>
        <div class="profile-category-content"><?php echo $row['Sex']; ?></div>
    </div>
    <div class= "col-md-1 p-0">
    <div class="profile-category">Weight</div>
        <div class="profile-category-content"><?php echo $row['Weight']; ?></div>
    </div>
</div>
<div class="row d-flex">
   <div class= "mr-0 col-md-2 pr-0 pl-0">
        <div class="profile-category">Phone Number</div>
        <div class="profile-category-content"><?php echo $row['ContactLineNumber']; ?></div>
    </div>
    <div class="col-md-4 p-0">
        <div class="profile-category">Email Address</div>
        <div class="profile-category-content"><?php echo $row['EmailAddress']; ?></div>
    </div> 

     
    <div class="col-md-3 p-0">
        <div class="profile-category">Address</div>
        <div class="profile-category-content"><?php echo $row['Address']; ?></div>
    </div>
    <div class="col-md-2 m-0 p-0">
        <div class="profile-category">City</div>
        <div class="profile-category-content"><?php echo $row['City']; ?> </div>
    </div>
    <div class="col-md-1 m-0 p-0">
        <div class="profile-category">Province</div>
        <div class="profile-category-content"><?php echo $row['Province']; ?></div>
    </div>
</div>
</div>
</div>
</div>
</div>
<div class="row justify-content-center d-flex align-items-center">
<div class="col-md-11">
<div class="row mt-4">
   
    <div class="col-md-6">
        <div class="card pl-5 pr-5 pt-4 pb-5">
            <h5 class="main-font-color"><b>Emergency Contact</b></h5>
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6 d-flex">
                                    <span class="emergency-contact">Full Name:</span>
                                    <span class="emergency-contact-content ml-auto"><?php echo $row['EmergencyContactFullName']; ?></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 d-flex">
                                    <span class="emergency-contact">Relationship:</span>
                                    <span class="emergency-contact-content ml-auto"><?php echo $row['Relationship']; ?></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 d-flex">
                                    <span class="emergency-contact">Phone Number:</span>
                                    <span class="emergency-contact-content ml-auto"><?php echo $row['EmergencyContactLineNumber']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            </div>
 





    
      
    <div class="col-md-6">
        <div class="card pl-5 pr-5 pt-4 pb-4 h-100">
            <h5 class="main-font-color"><b>Doctor Remarks</b></h5>
            <div class="row">
                <div class="col-md-12">
                            
                            <span> <?php echo $row['Recommendation']; ?></span>
                       
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                </div>

                <div class="modal fade" id="addEquipmentSuccessModal" tabindex="-1" role="dialog" aria-labelledby="usernamePasswordMismatchModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="usernamePasswordMismatchModalLabel"></h5>
        <i data-feather="x-circle" class="text-end featherer" data-dismiss="modal">

</i>
      </div>
      <div class="modal-body">

<h2 style="letter-spacing: -1px; color:#5e6e82;"class="text-center m-0"><b>PATIENT ACCOUNT</b></h2>
<h2 style="letter-spacing: -1px; color:#5e6e82;"class="text-center m-0"><b>CREATED</b></h2>
<div class="text-center">
<small style="letter-spacing: -1px; color:#5e6e82;">Username and password has been<br></small>
<small style="letter-spacing: -1px; color:#5e6e82;">sent via email of the patient.<br></small>
</div>
<div class="align-items-center justify-content-center d-flex mb-3 mt-3">
<button type="button" style="background-color: #1DD1A1; border:none;" class="btn btn-success px-5 py-2" data-dismiss="modal"><b>OK</b></button>
</div>
</div>
</div>
</div>
</div>
<div class="modal fade" id="addEquipmentFailureModal" tabindex="-1" role="dialog" aria-labelledby="usernamePasswordMismatchModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="usernamePasswordMismatchModalLabel"></h5>
        <i data-feather="x-circle" class="text-end featherer" data-dismiss="modal">

</i>
      </div>
      <div class="modal-body">

<h2 style="letter-spacing: -1px; color:#5e6e82;"class="text-center m-0"><b>ACCOUNT CREATION</b></h2>
<h2 style="letter-spacing: -1px; color:#5e6e82;"class="text-center m-0"><b>FAILED</b></h2>
<div class="text-center">
<small style="letter-spacing: -1px; color:#5e6e82;">An account already exists for the patient.<br></small>
</div>
<div class="align-items-center justify-content-center d-flex mb-3 mt-3">
<button type="button" style="background-color: #1DD1A1; border:none;" class="btn btn-success px-5 py-2" data-dismiss="modal"><b>OK</b></button>
</div>
</div>
</div>
</div>
</div>



                       
    
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    
    <!-- Data Table JS -->
<script src='https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js'></script>
<script src='https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js'></script>
    <script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>


 
  
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


<!-- Include jQuery -->

<script>
  $(document).ready(function() {
    <?php if(isset($_SESSION['success_message'])) { ?>
      $('#addEquipmentSuccessModal').modal('show');
      <?php unset($_SESSION['success_message']); ?> // Unset the session variable
    <?php } ?>
  });
  $(document).ready(function() {
    <?php if(isset($_SESSION['account-exists'])) { ?>
      $('#addEquipmentFailureModal').modal('show');
      <?php unset($_SESSION['account-exists']); ?> // Unset the session variable
    <?php } ?>
  });
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



</body>
</html>

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


  // DataTable initialization

  $(document).ready(function () {
    // Link custom length menu with DataTables
    $('#lengthInput').on('change', function () {
        table.page.len($(this).val()).draw();
    });

    // DataTable initialization
    var table = $('#example').DataTable({
        responsive: true,
        searching: false,
        pageLength: 5,
        dom: 'lBfrtip', // Include 'l' for length menu, 'B' is for Buttons
        buttons: [
            {
                extend: 'copyHtml5',
                text: '<img style="width:25px; height:25px;" src="copy_image.png" alt="Copy">',
                titleAttr: 'Copy',
                className: 'btn-img'
            },
            {
                extend: 'excelHtml5',
                text: '<img style="width:25px; height:25px;" src="excel_image.png" alt="Excel">',
                titleAttr: 'Excel',
                className: 'btn-img'
            },
            {
                extend: 'csvHtml5',
                text: '<img style="width:25px; height:25px;" src="csv_image.png" alt="CSV">',
                titleAttr: 'CSV',
                className: 'btn-img'
            },
            {
                extend: 'pdfHtml5',
                text: '<img style="width:25px; height:25px;"src="pdf_image.png" alt="PDF">',
                titleAttr: 'PDF',
                className: 'btn-img'
            }
        ],
        columnDefs: [
            { orderable: false, targets: 0 } // Disable ordering for the first column with checkboxes
        ],
        
        language: {
            // customize pagination prev and next buttons: use arrows instead of words
            "sortAscending": "&#9660;", // ▼
            "sortDescending": "&#9650;" // ▲
        }
    });
    $('.btn-img').hide();

// Toggle button visibility
$('#toggleButtons').on('click', function () {
    $('.btn-img').toggle();
});

});
   

    // Link custom search input with DataTable
    var customSearchInput = $('#searchInput');

    // Add an input event listener to trigger DataTables search on input
    customSearchInput.on('input', function () {
        table.search(this.value).draw();
    });

    // Button click event for exporting to Excel
    $('#button-excel').on('click', function () {
        // Trigger the DataTables Excel export
        table.buttons('excelHtml5').trigger();
    });


    // Toggle sidebar functionality
  

</script>
</body>
</html>
