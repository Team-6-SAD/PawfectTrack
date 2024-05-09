<?php
session_start();

// Check if the 'admin' session variable is not set or is false (user not logged in)
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true || !isset($_SESSION['adminID'])) {
    // Redirect the user to the login page
    header("Location: Admin Login.php");
    exit(); // Terminate the script
}

// Include your database connection file
require_once 'pawfect_connect.php';

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
    $patientID = $_GET['patientID'];

    // Prepare and execute the SQL query to fetch patient and bite details
    $sqlPatientBite = "SELECT p.FirstName, p.MiddleName, p.LastName, p.Sex,
                              c.LineNumber, c.EmailAddress,
                              b.AnimalType, b.ExposureType, b.ExposureDate, b.BiteLocation, b.ExposureMethod, b.BitePicture,
                              t.DateofTreatment
                       FROM patient AS p
                       LEFT JOIN contactinformation AS c ON p.PatientID = c.PatientID
                       LEFT JOIN bitedetails AS b ON p.PatientID = b.PatientID
                       LEFT JOIN treatment AS t ON p.PatientID = t.PatientID
                       WHERE p.PatientID = ?";
    
    $stmtPatientBite = mysqli_prepare($conn, $sqlPatientBite);
    mysqli_stmt_bind_param($stmtPatientBite, "i", $patientID);
    mysqli_stmt_execute($stmtPatientBite);
    $resultPatientBite = mysqli_stmt_get_result($stmtPatientBite);

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
        $bitePicture = $rowPatientBite['BitePicture'];
        $dateofTreatment = $rowPatientBite['DateofTreatment'];

        // Now you can use these variables to display the patient and bite details in your HTML
    } else {
        // Patient and bite details not found
        echo "Patient and bite details not found!";
    }
    
    // Close the statement for patient and bite details
    mysqli_stmt_close($stmtPatientBite);
}
// Close the database connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
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
<link href="hamburgers.css" rel="stylesheet">
  <link href="userdashboard.css" rel="stylesheet">
  <title>Patient Details - Bite Details</title>
  
</head>
<body>
<div class="container-fluid">
    <div class="main-container">
        <!-- Header and Sidebar -->
        <?php include 'admin_header.php'; ?>
        <div class="sidebar">
            <?php include 'sidebar.php'; ?>
        </div>


<!--Profile Picture and Details-->
        <div class="content" id="content">
    <div class="row mt-4">
        <div class="col-md-12"> 
        <h3 class="text-center main-font-color mt-2"><b>PATIENT DETAILS</b></h3>
       <div class="row justify-content-center d-flex">
        <div class="col-md-11">
        <div class="row mt-4">
    <div class="col-md-3 patient-navigation-active text-center">
        <a href="patientdetails-profile.php?patientID=<?php echo $patientID?>" class="text-center link-text">
            <img src="profile-gray.png" class="mr-3 nav-logo">Profile
            <hr class="profile-nav">
        </a>
    </div>
    <div class="col-md-3 patient-navigation text-center">
    <a href="patientdetails-bitedetails.php?patientID=<?php echo $patientID?>" class="text-center link-text-active">
            <img src="paw-blue.png" class="mr-3 nav-logo">Bite Exposure Details
            <hr class="profile-nav-active">
        </a>
    </div>
    <div class="col-md-3 patient-navigation text-center">
        <a href="patientdetails-treatmenthistory.php?patientID=<?php echo $patientID?>" class="text-center link-text">
            <img src="injection-gray.png" class="mr-3 nav-logo">Treatment History
            <hr class="profile-nav">
        </a>
    </div>
    <div class="col-md-3 patient-navigation text-center">
        <a href="patientdetails-appointments.php?patientID=<?php echo $patientID?>" class="text-center link-text">
            <img src="calendar-gray.png" class="mr-3 nav-logo">Appointments
            <hr class="profile-nav">
        </a>
    </div>
</div>
</div>
    <div class="row justify-content-center d-flex">
    <div class="col-md-11">
    <div class="row">
    <div class="col-md-7">
        <div class="card mt-4">
            
            <div class="card-body p-5">
                <h5 class="main-font-color"><b> Bite Exposure Details</b> </h5>
                <div class="profile-content-container mb-4">
                    <div class="col-sm-6 col-md-4">
                        <div class="profile-category">Type of Animal</div>
                        <div class="profile-category-content"><?php echo $animalType ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="profile-category">Type of Exposure</div>
                        <div class="profile-category-content"><?php echo $exposureType ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="profile-category">Date of Exposure</div>
                        <div class="profile-category-content"><?php echo $exposureDate ?></div>
                    </div>
                </div>
                <div class="profile-content-container">
                    <div class="col-md-4">
                        <div class="profile-category">Bite Location</div>
                        <div class="profile-category-content"><?php echo $biteLocation ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="profile-category">Exposure by</div>
                        <div class="profile-category-content"><?php echo $exposureMethod?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="profile-category">Date of Treatment</div>
                        <div class="profile-category-content"><?php echo $dateofTreatment ?></div>
                    </div>
                </div>
            </div>
        </div>
        </div>
     
        <div class="col-md-5 pt-4 d-flex justify-content-center" style="height: 340px; width:450px;">
        <?php
// Check if $bitePicture is empty
if ($bitePicture === "uploads/") {
    // If empty, set the path to the placeholder image
    $placeholderImagePath = "uploads/placeholder.png";
} else {
    // If not empty, use the value of $bitePicture
    $placeholderImagePath = $bitePicture;
}
?>

<img src="<?php echo $placeholderImagePath; ?>" alt="Placeholder Image" class="img-fluid">

    </div>
</div>
</div>
</div>

<div class="col-md-11 mt-4 pl-0">

<div class="card">
    <div class="card-body">
<div class="profile-content-container  p-3">
                    <div class="col-md-3">
                        <div class="profile-category">Fulll Name</div>
                        <div class="profile-category-content"><?php echo $pfirstName . ' ' . $pmiddleName . ' ' . $plastName ?></div>
                    </div>
                    <div class="col-md-2">
                        <div class="profile-category">Patient ID</div>
                        <div class="profile-category-content">000-001</div>
                    </div>
                    <div class="col-md-2">
                        <div class="profile-category">Gender</div>
                        <div class="profile-category-content">Male</div>
                    </div>
                    <div class="col-md-2">
                        <div class="profile-category">Contact Number</div>
                        <div class="profile-category-content">09898761521</div>
                    </div>
                    <div class="col-md-3">
                        <div class="profile-category">Email Address</div>
                        <div class="profile-category-content">peterwilrexewow0968@gmail.com</div>
                    </div>
              
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

$(document).ready(function () {

    $(".select-checkbox").change(function () {
        var selectedCheckboxValue = $(this).val();
        var dropdown1 = $("select[name='statusUpdate[" + selectedCheckboxValue + "]']");
        if ($(this).prop('checked')) {
            // Add more options to the dropdown dynamically using JavaScript
            dropdown1.append('<option value="Accepted">Accepted</option>');
            dropdown1.append('<option value="Rejected">Rejected</option>');
            // Add more options as needed
        } else {
            // If checkbox is unchecked, remove the added options
            dropdown1.find("option[value='Accepted']").remove();
            dropdown1.find("option[value='Rejected']").remove();
            // Remove more options as needed
        }
    

            var checkboxId = $(this).val();
            var dropdown = $("select[name='statusUpdate[" + checkboxId + "]']");
            dropdown.prop("disabled", !$(this).prop("checked"))
            
            
            
        });
        
    // Function to toggle checkbox visibility
    function toggleCheckboxesVisibility() {
        var checkboxes = $('.select-checkbox');
        checkboxes.toggle();

        // If the checkboxes are being hidden, uncheck all of them
        if (!checkboxes.is(':visible')) {
            checkboxes.prop('checked', false);
        }
    }
    


    // Function to toggle buttons visibility based on the number of checkboxes checked
    function toggleButtonsVisibility() {
        var checkedCheckboxes = $('.select-checkbox:checked');
        if (checkedCheckboxes.length === 1) {
            $('#updateButton').show();
            $('#deleteButton').show();
            $('#viewButton').show();
        } else if (checkedCheckboxes.length > 1) {
            $('#updateButton').show();
            $('#viewButton').hide();
            $('#deleteButton').show();
        } else {
            $('#updateButton, #deleteButton,#viewButton ').hide();
        }
    }

    // Initially hide the Delete and Update buttons
    $('#deleteButton, #updateButton, #selectAllButton,#viewButton ').hide();

    // Handle "Edit" button click
    $('#editButton').on('click', function () {
        toggleCheckboxesVisibility();
        toggleButtonsVisibility(); 

    
        

        // Toggle the visibility and state of the "Select All" button
        $('#selectAllButton').toggle();
        $('#selectAllButton').data('checked', false);

        $('.status-dropdown').prop('disabled', true);

        // Hide "Select All" button if no checkboxes are visible
        if ($('.select-checkbox:visible').length === 0) {
            $('#selectAllButton').hide();
        }
    });

    $("#updateButton").on("click", function () {
        var updates = {};
        $(".select-checkbox:checked").each(function () {
            var applicantID = $(this).val();
            var newStatus = $("select[name='statusUpdate[" + applicantID + "]']").val();
            updates["statusUpdate[" + applicantID + "]"] = newStatus;
        });

        // Set the updates directly as form parameters
        $("#updateForm").find(":input[name^='statusUpdate']").remove();
        $.each(updates, function (name, value) {
            $("#updateForm").append('<input type="hidden" name="' + name + '" value="' + value + '">');
        });

        // Submit the form
        $("#updateForm").submit();
    });
    // Handle "Select All" button click
    $('#selectAllButton').on('click', function () {
        var checkboxes = $('.select-checkbox');
        var allChecked = checkboxes.length === checkboxes.filter(':checked').length;

        // Toggle the state of all checkboxes
        checkboxes.prop('checked', !allChecked);
        checkboxes.trigger('change');
        checkboxes.each(function () {
            var applicantID = $(this).val();
            var dropdown = $("select[name='statusUpdate[" + applicantID + "]']");
            dropdown.prop("disabled", !$(this).prop("checked"));
        });


        // Update buttons visibility
        toggleButtonsVisibility();
    });

    // Handle individual checkboxes
    $('#example tbody').on('change', '.select-checkbox', function () {
        // Update buttons visibility
        toggleButtonsVisibility();
    });




        // Implement your update logic here
        $('#viewButton').on('click', function () {
    var selectedCheckbox = $('.select-checkbox:checked');

    // Check if exactly one checkbox is checked
    if (selectedCheckbox.length === 1) {
        var applicantID = selectedCheckbox.val();

        // Redirect to the view profile page with the selected Applicant ID
        window.location.href = 'viewprofile.php?ApplicantID=' + applicantID;
    } else {
        // If no checkbox or more than one checkbox is checked, show an alert
        alert('Please select exactly one row to view.');
    }
});

  




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
  
});
</script>
</body>
</html>
