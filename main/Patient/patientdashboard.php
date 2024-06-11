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

$stmt = $conn->prepare("SELECT TreatmentID FROM treatment WHERE PatientID = ?");
$stmt->bind_param("i", $patientID);
$stmt->execute();
$treatmentResult = $stmt->get_result();

if ($treatmentResult->num_rows > 0) {
    // Iterate through each treatment
    while ($treatmentRow = $treatmentResult->fetch_assoc()) {
        $treatmentID = $treatmentRow['TreatmentID'];

        // Fetch all the MedicineName associated with the TreatmentID
        $stmt = $conn->prepare("SELECT MedicineName FROM medicineusage WHERE TreatmentID = ?");
        $stmt->bind_param("i", $treatmentID);
        $stmt->execute();
        $medicineResult = $stmt->get_result();

        if ($medicineResult->num_rows > 0) {
            // Output data of each row
            while ($medicineRow = $medicineResult->fetch_assoc()) {
                // Access medicine name
                $medicineName = $medicineRow['MedicineName'];

                // Output medicine name
            }
        } else {
            echo "No medicines found for this treatment.<br>";
        }
    }
} else {
    echo "No treatments found for this patient.<br>";
}
// Close the database connection
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
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="hamburgers.css" rel="stylesheet">
  <link href="patient.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <title>Patient Dashboard</title>
  <style>
    table.dataTable thead .sorting:before, table.dataTable thead .sorting_asc:before, table.dataTable thead .sorting_desc:before, table.dataTable thead .sorting_asc_disabled:before, table.dataTable thead .sorting_desc_disabled:before {
        display: none !important;
    }

    table.dataTable thead .sorting:after, table.dataTable thead .sorting_asc:after, table.dataTable thead .sorting_desc:after, table.dataTable thead .sorting_asc_disabled:after, table.dataTable thead .sorting_desc_disabled:after {
     display: none !important;
    }

    @media only screen and (max-width: 872px) {
          /* Force table to not be like tables anymore */
          table,
          thead,
          tbody,
          th,
          td,
          tr {
              display: block;
          }

          /* Hide table headers (but not display: none;, for accessibility) */
          thead tr,
          tfoot tr {
              position: absolute;
              top: -9999px;
              left: -9999px;
          }

          td {
              /* Behave like a "row" */
              border: none;
              border-bottom: 1px solid #eee;
              position: relative;
              padding-left: 50% !important;
          }

          td:before {
              /* Now like a table header */
              position: absolute;
              /* Top/left values mimic padding */
              top: 6px;
              left: 6px;
              width: 45%;
              padding-right: 10px;
              white-space: nowrap;  
          }

     
          td:nth-of-type(1):before {
            content: "Sessions";
        }
          /*
          Label the data
          */
          td:nth-of-type(2):before {
              content: "Schedules";
          }

          td:nth-of-type(3):before {
              content: "Status";
          }

          td:nth-of-type(4):before {
              content: "Appointment Date";
          }

          td:nth-of-type(5):before {
              content: "Status";
          }

          td:nth-of-type(6):before {
              content: "Start date";
          }

          td:nth-of-type(7):before {
              content: "End Date";
          }

  
          .dataTables_filter {
              display: flex;
              justify-content: space-between;
              align-items: center;
              margin-bottom: 10px;
          }

        

          .dataTables_filter label {
              width: 100%;
          }
          

        
          
      }
      
  </style>
</head>
<body>
<div class="container-fluid">
        <div class="main-container">
            <!-- Header and Sidebar -->
            <?php include 'patient_header.php'; ?>

            <!-- Content -->
            <div class="content" id="content">
                <div class="row mr-5 ml-3 mt-0 pt-0 justify-content-center">
                    <div class="col-10 mt-0 pt-0 pr-2">
                        <div class="card-body card-image p-0 align-items-center">
                            <div class="row logo-font-color mt-0 pl-2">
                                <div class="col-md-6 text-left mt-5 ml-4">
                                    <h3 class="mt-1"><b>Welcome, Patient!</b></h3>
                                    <small style="word-wrap: break-word; "><i>We prioritized your well-being and the safety of your dear ones.</i></small>
                                </div>
                                <div class="col-md-5 text-right mt-4 pt-2">
                                    <img src="../img/img-dashboard/ABC-Sign-White.png" alt="Description of the image" class="img-logo ml-auto">
                                </div>
                                </div>
                                </div>
                                </div>
                                </div>
                         
                
                         
                     
               
                    <div class="row mr-5 ml-3 mt-0 pt-0 mt-3 mb-5 justify-content-center">
                    <div class="col-10 justify-content-center m-0 p-0">
                        <div class="row justify-content-center mr-2 ml-3 equal-height-columns">
                            <div class="col-md-12 col-lg-6 mt-2 pl-0 pr-3">
                                <div class="card p-3 pt-5 mx-auto table-card" style="height: 380px;">
                                    <div class="table-header-1 d-flex justify-content-between align-items-center">
                                        <div class="col-md-6">
                                            <h6 class="card-title"><b>My Vaccination:</b></h6>
                                            <h6 class="card-title"><?php echo $medicineName . "<br>"; ?></h6>
                                        </div>
                                        <div class="col-md-6 px-3 justify-content-end d-flex">
                                            <button id="editButton" class="btn btn-lg btn-primary" style="color:white; background-color:#0449A6;" onclick="redirectToAppointments()">View History</button>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <input type="hidden" name="selectedRows[]" id="selectedRowsInput">
                                        <div class="card-body">
                                            <table id="example" class="table">
                                                <thead class="table-header">
                                                    <tr>
                                                        <th>Sessions</th>
                                                        <th>Schedules</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    // Fetch and display appointment information
                                                    $stmt = $conn->prepare("SELECT PatientID FROM usercredentials WHERE UserID = ?");
                                                    $stmt->bind_param("i", $userID);
                                                    $stmt->execute();
                                                    $result = $stmt->get_result();

                                                    if ($result->num_rows === 1) {
                                                        // Fetch the PatientID
                                                        $row = $result->fetch_assoc();
                                                        $patientID = $row['PatientID'];

                                                        // Prepare and execute a query to retrieve appointment information using the PatientID
                                                        $stmt = $conn->prepare("SELECT SessionDays, Status, AppointmentDate FROM appointmentinformation WHERE PatientID = ? AND Status ='Pending'");
                                                        $stmt->bind_param("i", $patientID);
                                                        $stmt->execute();
                                                        $result = $stmt->get_result();

                                                        if ($result->num_rows > 0) {
                                                            // Output data of each row
                                                            while ($row = $result->fetch_assoc()) {
                                                                // Access appointment details
                                                                $sessionDays = $row['SessionDays'];
                                                                $status = $row['Status'];
                                                                $appointmentDate = $row['AppointmentDate'];
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo $sessionDays; ?></td>
                                                                    <td><?php echo $appointmentDate; ?></td>
                                                                    <td>
                                                                        <button class="btn btn-table status-button <?php echo ($status == 'Done' ? 'green' : 'yellow'); ?>" data-appointment-id="<?php echo $appointmentID; ?>">
                                                                            <?php echo $status; ?>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                            }
                                                        } else {
                                                            echo "<tr><td colspan='3'>No appointments found for this patient.</td></tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='3'>User not found or multiple users found (should not happen).</td></tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 col-lg-6 mt-2 pl-4 pr-0 justify-content-end d-flex">
                                <div id="carouselExampleSlidesOnly" class="carousel slide h-100" data-ride="carousel">
                                    <!-- Carousel Indicators -->
                                    <ol class="carousel-indicators">
                                        <li data-target="#carouselExampleSlidesOnly" data-slide-to="0" class="active"></li>
                                        <li data-target="#carouselExampleSlidesOnly" data-slide-to="1"></li>
                                        <li data-target="#carouselExampleSlidesOnly" data-slide-to="2"></li>
                                        <li data-target="#carouselExampleSlidesOnly" data-slide-to="3"></li>
                                    </ol>
                                    <!-- Carousel Slides -->
                                    <div class="carousel-inner h-100">
                                        <div class="carousel-item active h-100" >
                                            <img src="Image 0.png" class="d-block img-fluid" alt="Slide 1" style="border-radius: 11px; height: 380px;">
                                        </div>
                                        <div class="carousel-item h-100">
                                            <img src="Image 1.png" class="d-block img-fluid" alt="Slide 2" style="border-radius: 11px; height: 380px;">
                                        </div>
                                        <div class="carousel-item h-100">
                                            <img src="Image 2.png" class="d-block img-fluid" alt="Slide 3"style="border-radius: 11px; height: 380px;">
                                        </div>
                                        <div class="carousel-item h-100">
                                            <img src="Image 3.png" class="d-block img-fluid" alt="Slide 4"style="border-radius: 11px; height: 380px;">
                                        </div>
                                    </div>
                                    <!-- Carousel Controls -->
                                    <a class="carousel-control-prev" href="#carouselExampleSlidesOnly" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#carouselExampleSlidesOnly" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- End of content -->
        </div> <!-- End of main-container -->
    </div> <!-- End of container-fluid -->
                
             
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src='https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js'></script>
    <script src='https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js'></script>
    <script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>




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
    function redirectToAppointments() {
        window.location.href = "patient-appointments.php";
    }
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
        window.location.href = 'patientdetails-appointments.php?PatientID=' + applicantID;
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
        paging: false,
        sorting: false,
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
            info: ""
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

<script>
    $(document).ready(function(){
        $('.navbar-toggler').on('click', function () {
            $('.navbar').toggleClass('navbar-collapsed', $('.collapse').hasClass('show'));
        });
    });
</script>
</body>
</html>