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
} else {
    // Admin information not found
    echo "Admin information not found!";
}

// Check if the patientID is set in the URL
if(isset($_GET['patientID'])) {
    $patientID = $_GET['patientID'];
    $sqlPatientDetails = "SELECT p.FirstName, p.MiddleName, p.LastName, p.Sex, c.LineNumber, c.EmailAddress
                          FROM patient AS p
                          LEFT JOIN contactinformation AS c ON p.PatientID = c.PatientID
                          WHERE p.PatientID = ?";

    $stmtPatientDetails = mysqli_prepare($conn, $sqlPatientDetails);
    mysqli_stmt_bind_param($stmtPatientDetails, "i", $patientID);
    mysqli_stmt_execute($stmtPatientDetails);
    $resultPatientDetails = mysqli_stmt_get_result($stmtPatientDetails);

    // Check if there is a row returned for patient details
    if ($rowPatientDetails = mysqli_fetch_assoc($resultPatientDetails)) {
        // Patient details retrieved successfully
        $pfirstName = $rowPatientDetails['FirstName'];
        $pmiddleName = $rowPatientDetails['MiddleName'];
        $plastName = $rowPatientDetails['LastName'];
        $sex = $rowPatientDetails['Sex'];
        $lineNumber = $rowPatientDetails['LineNumber'];
        $emailAddress = $rowPatientDetails['EmailAddress'];
    } else {
        // No data found for the patient ID
        $error = "No patient details found for the given ID.";
    }

    // Close the statement
    mysqli_stmt_close($stmtPatientDetails);
} else {
    $error = "Invalid patient ID.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="img/Favicon 2.png" type="image/png">
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <link rel='stylesheet' href='https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css'>
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 
<link href="css/hamburgers.css" rel="stylesheet">
  <link href="css/userdashboard.css" rel="stylesheet">
  <style>
/* styles.css */
.custom-select-wrapper {
    position: relative;
    display: inline-block;
    width: 100%;
}
.fixed-size {
    height: 305px !important;
    width: 370px !important;
    object-fit:fill; /* Ensures the image covers the container */
}

.custom-select {
    width: 100%;
    padding-left: 20px;
    padding-right: 20px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    appearance: none;
    background-color: #fff;
    background-image: url('img/img-dashboard/clarity_calendar-solid.png'), url('img/img-dashboard/211614_down_b_arrow_icon.png');
    background-repeat: no-repeat, no-repeat;
    background-position: left 10px center, right 10px center;
    background-size: 20px 20px, 10px 10px;
    padding-left: 40px; /* Adjust to give space for the calendar icon */
}

.custom-select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

    </style>
  <title>Patient Details - Bite Details</title>
  
</head>
<body>
<div class="container-fluid">
    <div class="main-container">
        <!-- Header and Sidebar -->
        <?php include 'includes/admin_header.php'; ?>
        <div class="sidebar">
            <?php include 'includes/sidebar.php'; ?>
        </div>
        <div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999; position:fixed;"></div>


<!--Profile Picture and Details-->
        <div class="content" id="content">
    <div class="row mt-4">
        <div class="col-md-12"> 
        <h3 class="text-center main-font-color mt-2"><b>PATIENT DETAILS</b></h3>
       <div class="row justify-content-center d-flex">
        <div class="col-md-11 p-0">
        <div class="row mt-4">
    <div class="col-md-3 patient-navigation-active text-center">
        <a href="patientdetails-profile.php?patientID=<?php echo $patientID?>" class="text-center link-text">
            <img src="img/img-patient-details/profile-gray.png" class="mr-3 nav-logo">Profile
            <hr class="profile-nav">
        </a>
    </div>
    <div class="col-md-3 patient-navigation text-center">
    <a href="patientdetails-bitedetails.php?patientID=<?php echo $patientID?>" class="text-center link-text-active">
            <img src="img/img-patient-details/paw-blue.png" class="mr-3 nav-logo">Bite Exposure Details
            <hr class="profile-nav-active">
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
<div class="row justify-content-center d-flex">
    <div class="col-md-11">
        <div class="row">
            <div class="col-md-7 pl-0 h-100">
                <div class="card mt-4">
                    <div class="card-body p-4">
                    <div class="container mt-0 mb-5">
        <div class="row align-items-center">
            <div class="col-auto">
                <h5 class="main-font-color"><b>Bite Exposure Details</b></h5>
            </div>
            <div class="col">
            <div class="custom-select-wrapper">
    <select class="custom-select" id="biteDetailsDate" onchange="loadBiteDetails(this.value)">
        <option value="" disabled selected>Select Date</option>
        <?php
        // Loop through all the bite details dates and populate the dropdown
        // Prepare and execute the SQL query to fetch distinct bite details dates
        $sqlDistinctDates = "SELECT DISTINCT ExposureDate FROM bitedetails WHERE PatientID = ?";
        $stmtDistinctDates = mysqli_prepare($conn, $sqlDistinctDates);
        mysqli_stmt_bind_param($stmtDistinctDates, "i", $patientID);
        mysqli_stmt_execute($stmtDistinctDates);
        $resultDistinctDates = mysqli_stmt_get_result($stmtDistinctDates);

        // Check if there are any bite details dates fetched
        if (mysqli_num_rows($resultDistinctDates) > 0) {
            // Loop through the distinct dates and populate the dropdown
            while ($rowDistinctDates = mysqli_fetch_assoc($resultDistinctDates)) {
                echo "<option value='" . $rowDistinctDates['ExposureDate'] . "'>" . $rowDistinctDates['ExposureDate'] . "</option>";
            }
        } else {
            echo "<option value=''>No Dates Found</option>";
        }
        ?>
    </select>
</div>


            </div>
        </div>
    </div>
                        <div class="profile-content-container mb-4">
                            <div class="col-sm-6 col-md-4">
                                <div class="profile-category">Type of Animal</div>
                                <div class="profile-category-content" id="animalType"><?php echo isset($animalType) ? $animalType : ''; ?></div>
                            </div>
                            <div class="col-md-4">
                                <div class="profile-category">Type of Exposure</div>
                                <div class="profile-category-content" id="exposureType"><?php echo isset($exposureType) ? $exposureType : ''; ?></div>
                            </div>
                            <div class="col-md-4">
                                <div class="profile-category">Date of Exposure</div>
                                <div class="profile-category-content" id="exposureDate"><?php echo isset($exposureDate) ? $exposureDate : ''; ?></div>
                            </div>
                        </div>
                        <div class="profile-content-container">
                            <div class="col-md-4">
                                <div class="profile-category">Bite Location</div>
                                <div class="profile-category-content" id="biteLocation"><?php echo isset($biteLocation) ? $biteLocation : ''; ?></div>
                            </div>
                            <div class="col-md-4">
                                <div class="profile-category">Exposure by</div>
                                <div class="profile-category-content" id="exposureMethod"><?php echo isset($exposureMethod) ? $exposureMethod : ''; ?></div>
                            </div>
                            <div class="col-md-4">
                                <div class="profile-category text-nowrap">Date of Treatment</div>
                                <div class="profile-category-content" id="dateofTreatment"><?php echo isset($dateofTreatment) ? $dateofTreatment : ''; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5 pt-4 d-flex justify-content-center flex-wrap" style="height: 340px; width: 450px; overflow-y: auto;">
            <div id="biteCarousel" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner" id="carouselInner">
        <?php
        // Placeholder image path
        $placeholderImagePath = "uploads/placeholder.png";

        // Check if $bitePictures is set and not empty
        if (isset($bitePictures) && is_array($bitePictures) && !empty($bitePictures)) {
            // Loop through each bite picture path and display the image in the carousel
            $activeClass = 'active'; // First image is active
            foreach ($bitePictures as $bitePicture) {
                // Set the path to the image or use the placeholder if the path is invalid
                $imagePath = (trim($bitePicture) !== "" && trim($bitePicture) !== "uploads/") ? $bitePicture : $placeholderImagePath;
                echo '<div class="carousel-item ' . $activeClass . '">';
                echo '<img src="' . $imagePath . '" class="d-block fixed-size"  alt="Bite Picture">';
                echo '</div>';
                $activeClass = ''; // Remove active class after the first item
            }
        } else {
            // If no images are available, display the placeholder image
            echo '<div class="carousel-item active">';
            echo '<img src="' . $placeholderImagePath . '" class="d-block fixed-size"  style="height:300px; width:300px;" alt="Placeholder Image">';
            echo '</div>';
        }
        ?>
    </div>
    <a class="carousel-control-prev" href="#biteCarousel" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#biteCarousel" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>
                                    </div>

        </div>
    </div>
    <div class="col-md-11 mt-4 pl-0 mb-5">
        <div class="card">
            <div class="card-body">
                <div class="profile-content-container p-3">
                    <div class="col-md-3">
                        <div class="profile-category">Full Name</div>
                        <div class="profile-category-content"><?php echo isset($pfirstName) ? $pfirstName . ' ' . $pmiddleName . ' ' . $plastName : ''; ?></div>
                    </div>
                    <div class="col-md-2">
                        <div class="profile-category">Patient ID</div>
                        <div class="profile-category-content"><?php echo isset($patientID) ? $patientID : ''; ?></div>
                    </div>
                    <div class="col-md-2">
                        <div class="profile-category">Gender</div>
                        <div class="profile-category-content"><?php echo isset($sex) ? $sex : ''; ?></div>
                    </div>
                    <div class="col-md-2">
                        <div class="profile-category">Contact Number</div>
                        <div class="profile-category-content"><?php echo isset($phoneNumber) ? $phoneNumber : ''; ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="profile-category">Email Address</div>
                        <div class="profile-category-content"><?php echo isset($emailAddress) ? $emailAddress : ''; ?></div>
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

  <script src="js/notifications.js"></script>
<!-- Include jQuery -->
<script>
    // Function to load bite details based on selected date
    function loadBiteDetails(date) {
        // Check if date is not empty
        if (date !== "") {
            // Get the patientID from the URL
            var patientID = <?php echo isset($patientID) ? $patientID : 'null'; ?>;

            // AJAX call to fetch bite details for the selected date and patientID
            $.ajax({
                url: 'backend/fetch-bite-details.php',
                type: 'GET',
                data: { date: date, patientID: patientID },
                dataType: 'json',
                success: function(response) {
                    // Update other HTML elements with the fetched data (e.g., animal type, exposure type, etc.)
                    $('#animalType').text(response.AnimalType);
                    $('#exposureType').text(response.ExposureType);
                    $('#exposureDate').text(response.ExposureDate);
                    $('#biteLocation').text(response.BiteLocation);
                    $('#exposureMethod').text(response.ExposureMethod);
                    $('#dateofTreatment').text(response.DateofTreatment);

                    // Clear existing carousel items
                    $('#carouselInner').empty();

                    // Check if there are bite pictures
                    if (response.BitePictures.length > 0) {
                        // Update the carousel items with bite pictures
                        $.each(response.BitePictures, function(index, bitePicture) {
                            var imageElement = '<div class="carousel-item' + (index === 0 ? ' active' : '') + '">' +
                                                '<img src="' + bitePicture + '" class="d-block fixed-size"  alt="Bite Picture">' +
                                                '</div>';
                            $('#carouselInner').append(imageElement);
                        });
                    } else {
                        // If no images are available, display the placeholder image
                        var placeholderImage = '<div class="carousel-item active">' +
                                                '<img src="uploads/placeholder.png" class="d-block fixed-size"  alt="Placeholder Image">' +
                                                '</div>';
                        $('#carouselInner').append(placeholderImage);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('Error fetching bite details. Please try again.');
                }
            });
        }
    }

    // Initial call to load bite details for the first date in the dropdown (if available)
    $(document).ready(function() {
        var initialDate = $('#biteDetailsDate').val();
        if (initialDate !== "") {
            loadBiteDetails(initialDate);
        }
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
