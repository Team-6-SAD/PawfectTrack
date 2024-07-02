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
    $lastName = $row['lastname'];
    $adminPhoto = $row['adminphoto'];

    // Now fetch one appointment per patient
   $sql = "
    SELECT
        p.PatientID,
        CONCAT(p.FirstName, ' ', p.LastName) AS FullName,
        MAX(ai.SessionDays) AS CurrentSession,
        MAX(ai.AppointmentDate) AS AppointmentDate,
        MAX(ai.Status) AS Status,
        MAX(bd.ExposureType) AS ExposureType
    FROM
        patient p
    LEFT JOIN
        treatment t ON p.PatientID = t.PatientID
    LEFT JOIN
        (
            SELECT
                ai1.PatientID,
                ai1.SessionDays,
                ai1.AppointmentDate,
                ai1.Status,
                ai1.TreatmentID
            FROM
                appointmentinformation ai1
            WHERE
                ai1.Status = 'Pending'
        ) ai ON t.TreatmentID = ai.TreatmentID
    LEFT JOIN
        bitedetails bd ON bd.PatientID = p.PatientID
    WHERE
        p.ActiveStatus = 'Active'
    GROUP BY
        p.PatientID, p.FirstName, p.LastName  -- Group by all non-aggregated columns in SELECT
    ORDER BY
        MAX(ai.AppointmentDate) ASC; -- Ensure the earliest pending appointment date is selected
";

    
    
    $patients_result = mysqli_query($conn, $sql);
    
    
} else {
    // Admin information not found
    echo "Admin information not found!";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
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

  <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"  rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="css/hamburgers.css" rel="stylesheet">
  <link href="css/userdashboard.css" rel="stylesheet">
  <link rel='stylesheet' href='https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css'>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <title>Patient List</title>
  <style>
    table.dataTable.no-footer {
    border-bottom: none !important;
}
    table.dataTable thead .sorting:before, table.dataTable thead .sorting_asc:before, table.dataTable thead .sorting_desc:before, table.dataTable thead .sorting_asc_disabled:before, table.dataTable thead .sorting_desc_disabled:before {
        content: "\F148" !important; /* Font Awesome icon for ascending sort */
        font-family: "bootstrap-icons";
        right: 0.8em !important;
        top: 40% !important;
        font-size: 14px !important;
}
table.dataTable thead .sorting {
    background-image: none !important;
}
table.dataTable thead .sorting_asc {
    background-image: none !important;
}
table.dataTable thead .sorting:after, table.dataTable thead .sorting_asc:after, table.dataTable thead .sorting_desc:after, table.dataTable thead .sorting_asc_disabled:after, table.dataTable thead .sorting_desc_disabled:after {
    
    content: "\F128" !important; /* Font Awesome icon for descending sort */
    font-family: 'bootstrap-icons';
        right: 0.2em !important;
        top: 40% !important;
        font-size: 14px !important;
}

          h3, small {
            margin: 0; /* Remove default margins */
            padding: 0; /* Remove default padding */
        }

        h3 {
            margin-bottom: -5px; /* Adjust the bottom margin as needed */
        }
    
   
        .table thead th{
            border-bottom: none;
        }
        #example thead th:first-child::after {
    display: none;
}
#example thead th:first-child::before {
    display: none;
}
.table td, .table th{
    border-top: none;
}
tbody tr:nth-child(odd) {
        background-color: #F7F8FA !important;
    }

    tbody tr:nth-child(even) {
        background-color: #FFFFFF;
    }   
    .btn{
        border-radius: 8px !important;
        font-size: 12px;
        font-weight: bold;
    }
    .form-group{
      font-weight:normal !important;
    }
    .select2-selection__placeholder{
    color: #ECECEC !important;
      font-size:12px;
}
.select2-container--default .select2-selection--single{
  border-radius: 0px;
    }
</style>
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
        <div class="row  mr-5 ml-3 ">
        <div class="col-md-12">
                    <div class="card mx-auto  table-card">
                    
                        <h4 class="card-title text-center main-font-color p-3 table-header-1" style="background-color:#faf9fd;  border-radius: 8px 8px 0 0;"><b>LIST OF PATIENTS</b></h4>


                        <div id="buttonContainer" class="d-flex flex-column flex-md-row align-items-center mb-2 ml-2 mt-1 pl-1 pr-3">
    <!-- Edit button on the left -->
   
    <button id="editButton" class="btn btn-gray-color btn-custom  mr-3 px-3 ml-3 py-2" style="color:white;  border-radius: 6px; font-weight: 400;">
  Action <span style="font-size: 8px; vertical-align: middle;"> &#9654; </span>
</button>
    <!-- Additional buttons next to Edit -->
    <div class="d-flex flex-row flex-wrap align-items-center">
     
        <button id="viewButton" class="btn btn-custom btn-blue-color btn-outline-info mr-3 px-4 py-2" style="white-space: nowrap; color:white; margin-bottom:1px;">View </button>
        <button id="deleteButton" class="btn btn-custom btn-blue-color btn-outline-info mr-3 px-3 py-2" style="white-space: nowrap; color:white; margin-bottom:1px;" >Archive</button>
        <button id="updateButton" class="btn btn-custom btn-blue-color btn-outline-info mr-3 px-4 py-2" style="white-space: nowrap; color:white; margin-bottom:1px;" >Edit</button>

    </div>


                    
                        <!-- Spacer to push custom search and Excel export buttons to the right -->
                            <div class="flex-grow-1"></div>
                            <button id="existingPatient" class="btn greener  mb-2 mb-sm-0 mr-sm-2 px-2"> <img src="img/img-dashboard/white-add.png" alt="Icon" style="width: 20px; height: 20px; margin-right: 3px; margin-bottom:1px;"> Existing Account</button> 
                            <button id="addPatient" class="btn greener  mb-2 mb-sm-0 mr-sm-2 "><img src="img/img-dashboard/white-add.png" alt="Icon" style="width: 20px; height: 20px; margin-right: 3px; margin-bottom:1px;"> Add Patient</button> 
                        <!-- Custom search on the right -->
                        
                    
                        <!-- Excel export button at the far right -->
                    
                    </div>
                   <!-- Custom search input -->
<div class="custom-search mx-4">

    <input type="text" id="customSearchInput" placeholder="Search..."  class="form-control search-input">
</div>



                    <form id="deleteForm" action="backend/archive-patients.php" method="post">
    <input type="hidden" name="selectedRows[]" id="selectedRowsInput">

         
                <div class="card-body px-4">
                <?php // Check if there are any patients fetched from the database
if (mysqli_num_rows($patients_result) > 0) {
    // Output the table only if there are patients
    echo '<table class="table" id="example">';
    echo '<thead class="table-header">';
    echo '<tr>';
    echo '<th scope="col" class="pl-3"><input type="checkbox" id="selectAllCheckbox"> </th>';
    echo '<th scope="col">Patient ID</th>';
    echo '<th scope="col">Full Name</th>';
    echo '<th scope="col">Current Session</th>';
    echo '<th scope="col">Appointment Date</th>';
    echo '<th scope="col">Type of Exposure</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    // Loop through each patient and insert data into table rows
    while ($patient = mysqli_fetch_assoc($patients_result)) {
        echo "<tr>";
        echo "<td scope='row' class='pl-3'><input type='checkbox' class='select-checkbox' value='" . $patient['PatientID'] . "'></td>";
        echo "<td class='pl-3'>" . $patient['PatientID'] . "</td>";
        echo "<td class='pl-3'>" . $patient['FullName'] . "</td>";
        if ($patient['Status'] != "Pending") {
            echo "<td class='pl-3'>Completed</td>"; // Display "Completed" if status is "Done"
        } else {
        echo "<td class='pl-3'>" . 'Day' . " " .  $patient['CurrentSession'] . "</td>";
    }
       
    if ($patient['Status'] != "Pending") {
        echo "<td class='pl-3'>Completed</td>"; // Display "Completed" if status is "Done"
    } else {
            echo "<td class='pl-3'>" . $patient['AppointmentDate'] . "</td>"; // Otherwise, display the appointment date
        }
    
        echo "<td class='pl-3'>" . $patient['ExposureType'] . "</td>";
        echo "</tr>";
    }
    
    echo '</tbody>';
    echo '</table>';
} else {
    // Output a message if there are no patients
    echo 'No patients found.';
}

?>

                
</div>
                
            </div>
         
            </form>
        </div>
        </div>
    </div>
    </div>
</div>
    </div>
</div>
       
</div>
<div class="modal fade" id="removalConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="usernamePasswordMismatchModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="usernamePasswordMismatchModalLabel"></h5>
        <i data-feather="x-circle" class="text-end featherer" data-dismiss="modal">

</i>
      </div>
      <div class="modal-body">
<div class="justify-content-center d-flex mb-2">
<img src="img/img-alerts/Archive alert.png" width="70px" height="60">
</div>
<h2 style="letter-spacing: -1px; color:#5e6e82;"class="text-center m-0"><b>ARCHIVE</b></h2>
<div class="text-center">
<small style="letter-spacing: -1px; color:#5e6e82;">Are you sure you want to archive the<br></small>
<small style="letter-spacing: -1px; color:#5e6e82;"> selected patient record/s?<br></small>
</div>
<div class="align-items-center justify-content-center d-flex mb-3 mt-3">
<button type="button" style="background-color: #C1C1C1; border:none;" class="btn btn-success px-3 mr-2 py-2" data-dismiss="modal"><b>Cancel</b></button>
<button type="button" style="background-color: #2e86de; border:none;" class="btn btn-success px-3 py-2" onclick="deleteSelectedRows()"><b>Archive</b></button>
</div>
</div>  
</div>
</div>
</div>
<!-- Patient Modal -->
<div id="patientModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="patientModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title p-3" id="patientModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group px-5">
                      <label for="patientSelect"><small>Existing Account of Patient</small></label>
                        <select class="form-control" id="patientSelect" style="width: 100%;">
                            <option value="" disabled selected>Select Existing Patient</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn font-weight-bold" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" style="background-color:#1BB58D; border-radius:27.5px !important; border:none;">Add Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
  <div class="modal fade" id="addStockSuccessModal" tabindex="-1" role="dialog" aria-labelledby="usernamePasswordMismatchModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="usernamePasswordMismatchModalLabel"></h5>
                                <i data-feather="x-circle" class="text-end featherer" data-dismiss="modal">

                                </i>
                            </div>
                            <div class="modal-body">
                                <div class="justify-content-center d-flex">
                                    <img src="img/img-alerts/check-mark.png">
                                </div>
                                <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>RECORD</b></h2>
                                <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>ADDED</b></h2>
                                <div class="text-center">
                                    <small style="letter-spacing: -1px; color:#5e6e82;">Record for the patient has been saved.<br></small>
                                </div>
                                <div class="align-items-center justify-content-center d-flex mb-3 mt-3">
                                    <button type="button" style="background-color: #1DD1A1; border:none;" class="btn btn-success px-5 py-2" data-dismiss="modal"><b>OK</b></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
  <div class="modal fade" id="addEditSuccessModal" tabindex="-1" role="dialog" aria-labelledby="usernamePasswordMismatchModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="usernamePasswordMismatchModalLabel"></h5>
                                <i data-feather="x-circle" class="text-end featherer" data-dismiss="modal">

                                </i>
                            </div>
                            <div class="modal-body">
                                <div class="justify-content-center d-flex">
                                    <img src="img/img-alerts/check-mark.png">
                                </div>
                                <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>UPDATE</b></h2>
                                <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>SUCESSFUL</b></h2>
                                <div class="text-center">
                                    <small style="letter-spacing: -1px; color:#5e6e82;">Your changes are saved.<br></small>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Data Table JS -->
<script src='https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js'></script>

    <script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>


    <!-- ... (your existing script imports) ... -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
                <script>
                    $(document).ready(function() {
                        <?php if (isset($_SESSION['successPatientModal'])) { ?>
                            $('#addStockSuccessModal').modal('show');
                            <?php unset($_SESSION['successPatientModal']); ?> // Unset the session variable
                        <?php } ?>
                    });
                </script>
                <script>
                    $(document).ready(function() {
                        <?php if (isset($_SESSION['successEditModal'])) { ?>
                            $('#addEditSuccessModal').modal('show');
                            <?php unset($_SESSION['successEditModal']); ?> // Unset the session variable
                        <?php } ?>
                    });
                </script>
<script>
$(document).ready(function() {
    // Function to initialize Select2 within the modal
    function initializeSelect2() {
        $('#patientSelect').select2({
            placeholder: 'Select Existing Patient',
            allowClear: true,
            dropdownParent: $('#patientModal') // Ensure the dropdown appears within the modal
        });
    }

    // Event listener for the "Existing Account" button
    $('#existingPatient').on('click', function() {
        // Fetch patient data
        $.ajax({
            url: 'backend/get-patients.php', // The PHP script that fetches patient data
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var $patientSelect = $('#patientSelect');
                $patientSelect.empty(); // Clear any existing options
                $patientSelect.append('<option value=""></option>'); // Default option for Select2

                // Populate the dropdown with patient data
                $.each(data, function(index, patient) {
                    $patientSelect.append('<option value="' + patient.PatientID + '">' + patient.FullName + '</option>');
                });

                // Show the modal
                $('#patientModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + status + error);
            }
        });
    });

    // Initialize Select2 after the modal is shown
    $('#patientModal').on('shown.bs.modal', function() {
        initializeSelect2();
    });

    // Destroy Select2 instance when the modal is hidden (optional cleanup)
    $('#patientModal').on('hidden.bs.modal', function() {
        $('#patientSelect').select2('destroy');
    });

    // Handle form submission
    $('#patientModal form').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        // Get the selected patient value
        var selectedPatientID = $('#patientSelect').val();

        // Redirect to the desired URL with the selected patient value
        if (selectedPatientID) {
            window.location.href = 'existing-patient-record.php?patientID=' + selectedPatientID;
        } else {
            alert('Please select a patient.');
        }
    });
});

</script>

<script>
    $(document).ready(function() {
  $('#deleteButton').click(function() {
    $('#removalConfirmationModal').modal('show');
  });
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
    
// Function to handle the Edit button click
$(document).on('click', '.editButton', function() {
    var patientID = $(this).data('patientid'); // Get the PatientID from the data attribute
    redirectToEdit(patientID); // Pass the PatientID to the redirect function
});

function redirectToEdit(patientID) {
    if (patientID) {
        window.location.href = "Edit Patient.php?PatientID=" + patientID; // Redirect with PatientID
    } else {
        alert('Patient ID not found.');
    }
}

</script>
<script>
        $('.select-checkbox').hide();
$(document).ready(function() {
    // DataTable initialization
    var table = $('#example').DataTable({
        paging: true,
        responsive: true,
        searching: true,
        pageLength: 5,
        lengthMenu: [[5, 25, 50, -1], [5, 25, 50, "All"]],
        dom: "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-12 ml-5 mt-3'>><<'col-sm-12'lp>>",
        columnDefs: [
            { orderable: false, targets: 0 } // Disable sorting for the first column (index 0)
        ],
        language: {
            lengthMenu: "Display _MENU_"
        }
    });

    // Function to update page information
    function updatePageInfo() {
        var pageInfo = table.page.info();
        var currentPage = pageInfo.page + 1; // Convert zero-based index to one-based index
        var totalPages = pageInfo.pages;
        var pageLengthContainer = $('.dataTables_length');

        // Update the page information
        pageLengthContainer.find('.page-info').remove();
        pageLengthContainer.append('<span class="page-info" style="margin-left: 10px;">Page <b>' + currentPage + '</b> of <b>' + totalPages + '</b></span>');
    }

    // Initial update of page information
    updatePageInfo();

    // Update page information whenever the table is redrawn
    table.on('draw', function() {
        updatePageInfo();
    });

    // Initially hide all checkboxes
    $('.select-checkbox').hide();

    // Flag to track edit mode status
    var editMode = false;

    // Function to toggle checkboxes visibility inside DataTable rows
    function toggleCheckboxesVisibility() {
        var rows = table.rows({ search: 'applied' }).nodes(); // Get all rows, including filtered ones

        $(rows).each(function() {
            var checkbox = $(this).find('.select-checkbox');
            if (editMode) {
                checkbox.show(); // Show checkbox if edit mode is on
            } else {
                checkbox.hide(); // Hide checkbox if edit mode is off
                checkbox.prop('checked', false); // Ensure checkbox is unchecked when hidden
            }
        });

        // Update buttons visibility after toggling checkboxes
        toggleButtonsVisibility();
    }

    // Function to toggle buttons visibility based on number of checkboxes checked
    function toggleButtonsVisibility() {
        var checkedCheckboxes = $('.select-checkbox:checked');
        if (checkedCheckboxes.length === 1) {
            $('#updateButton, #deleteButton, #viewButton').show();
        } else if (checkedCheckboxes.length > 1) {
            $('#deleteButton').show();
            $('#viewButton').hide();
          $('#updateButton').hide();
        } else {
            $('#updateButton, #deleteButton, #viewButton, #selectAllButton').hide();
        }
    }

    // Hide "View", "Delete", "Edit" and "Select All" initially
    $('#viewButton, #deleteButton, #updateButton, #selectAllCheckbox').hide();

    // Handle "Edit" button click
    $('#editButton').on('click', function() {
        editMode = !editMode; // Toggle edit mode

        // Toggle checkboxes visibility
        toggleCheckboxesVisibility();

        // Toggle the visibility and state of the "Select All" button
        $('#selectAllCheckbox').toggle().data('checked', false);

        // Uncheck "Select All" checkbox when edit mode is turned off
        if (!editMode) {
            $('#selectAllCheckbox').prop('checked', false);
        }

        $('.status-dropdown').prop('disabled', true);

        // Hide "Select All" button if no checkboxes are visible
        if ($('.select-checkbox:visible').length === 0) {
            $('#selectAllCheckbox').hide();
        }
    });

    // Handle "Select All" button click
    $('#selectAllCheckbox').on('click', function() {
        var rows = table.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);

        // Toggle state of all checkboxes and disable/enable dropdowns accordingly
        $('.select-checkbox', rows).each(function() {
            var applicantID = $(this).val();
            var dropdown = $("select[name='statusUpdate[" + applicantID + "]']");
            dropdown.prop("disabled", !$(this).prop("checked"));
        });

        // Update buttons visibility
        toggleButtonsVisibility();
    });

    // Handle individual checkboxes
    $('#example tbody').on('change', '.select-checkbox', function() {
        // Update buttons visibility
        toggleButtonsVisibility();
    });

    // Handle "Update" button click
    $('#updateButton').on('click', function() {
        var selectedCheckbox = $('.select-checkbox:checked');

        // Handle update logic
        if (selectedCheckbox.length === 1) {
            var patientID = selectedCheckbox.val();
            window.location.href = 'Edit Patient.php?patientID=' + patientID;
        } else {
            alert('Please select exactly one row to update.');
        }
    });

        // Link custom search input with DataTable
        var customSearchInput = $('#customSearchInput');
    customSearchInput.on('input', function() {
        table.search(this.value).draw();
    });
    // Handle "View" button click
    $('#viewButton').on('click', function() {
        var selectedCheckbox = $('.select-checkbox:checked');

        // Handle view logic
        if (selectedCheckbox.length === 1) {
            var patientID = selectedCheckbox.val();
            window.location.href = 'patientdetails-profile.php?patientID=' + patientID;
        } else {
            alert('Please select exactly one row to view.');
        }
    });
});


// Function to delete selected rows
function deleteSelectedRows() {
    var selectedRows = [];

    $('#example').DataTable().$('tr').each(function() {
        var checkbox = $(this).find('.select-checkbox');
        if (checkbox.is(':checked')) {
            selectedRows.push(checkbox.val());
        }
    });

    // Validate and perform deletion here if needed
    if (selectedRows.length === 0) {
        alert('No rows selected for deletion.');
        return;
    }

    // Assuming you have a form with id "deleteForm" and a hidden input "selectedRowsInput"
    $('#selectedRowsInput').val(selectedRows);
    $('#deleteForm').submit(); // Submit the form to handle deletion
}
</script>

<script>

    document.getElementById("addPatient").addEventListener("click", function() {
        // Redirect to Add Patient.php
        window.location.href = "Add Patient.php";
    });
</script>

</body>
</html>
