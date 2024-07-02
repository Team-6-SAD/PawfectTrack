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
        ) ai ON t.TreatmentID = ai.TreatmentID
    LEFT JOIN
        bitedetails bd ON bd.PatientID = p.PatientID
    WHERE
        p.ActiveStatus = 'Inactive'
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
 
<link href="css/hamburgers.css" rel="stylesheet">
  <link href="css/userdashboard.css" rel="stylesheet">
  <link rel='stylesheet' href='https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">

  <title>Archival</title>

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
        <div class="col-md-12 mb-5">
                    <div class="card mx-auto  table-card p-3 ">
                        <div class="table-header-1">
                        <h3 class="card-title text-center main-font-color mt-3 ml-2"><b>LIST OF PATIENTS</b></h3>
</div>


                        <div id="buttonContainer" class="d-flex flex-column flex-md-row align-items-center mb-2 ml-2 mt-1">
    <!-- Edit button on the left -->
    
    <button id="editButton" class="btn btn-gray-color btn-custom mb-2 mb-sm-0 mr-sm-2 ml-3" style="color:white">Action</button>

    <!-- Additional buttons next to Edit -->
    <div class="d-flex flex-row flex-wrap align-items-center">
     
        <button id="viewButton" class="btn btn-custom btn-blue-color btn-outline-info mr-2" style="white-space: nowrap; color:white;">View </button>
        <button id="deleteButton" class="btn btn-custom btn-blue-color btn-outline-info mr-2" style="white-space: nowrap; color:white;"  >Restore</button>

    </div>


                    
                        <!-- Spacer to push custom search and Excel export buttons to the right -->
                            <div class="flex-grow-1"></div>
                        <!-- Custom search on the right -->
                        
                    
                        <!-- Excel export button at the far right -->
                    
                    </div>
                   <!-- Custom search input -->
<div class="custom-search mx-4">

    <input type="text" id="customSearchInput" placeholder="Search..."  class="form-control search-input">
</div>



                    <form id="deleteForm" action="backend/restore-patients.php" method="post">
    <input type="hidden" name="selectedRows[]" id="selectedRowsInput">

         
                <div class="card-body">
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
        echo "<td class='pl-3'>" . 'Day' . " " .  $patient['CurrentSession'] . "</td>";
        echo "<td class='pl-3'>" . $patient['AppointmentDate'] . "</td>";
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
</tbody>
  
</table>
                
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
<div class="justify-content-center d-flex">
<img src="img/img-alerts/caution-mark.png" style="height:60px; width:auto;">
</div>
<h2 style="letter-spacing: -1px; color:#5e6e82;"class="text-center m-0"><b>RESTORE</b></h2>
<h2 style="letter-spacing: -1px; color:#5e6e82;"class="text-center m-0"><b>RECORDS</b></h2>
<div class="text-center">
<small style="letter-spacing: -1px; color:#5e6e82;">Are you sure you want to restore<br></small>
<small style="letter-spacing: -1px; color:#5e6e82;">the selected record/s?<br></small>
</div>
<div class="align-items-center justify-content-center d-flex mb-3 mt-3">
<button type="button" style="background-color: none; border:none;" class="btn px-3 mr-4 py-2" data-dismiss="modal">Cancel</button>
<button type="button" style="background-color: #1dd1a1; border:none; border-radius:27.5px !important;" class="btn btn-success px-3 py-2 font-weight-bold" onclick="deleteSelectedRows()">Restore</button>
</div>
</div>  
</div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <!-- Data Table JS -->
    
    <!-- Data Table JS -->
<script src='https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js'></script>

    <script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>


    <!-- ... (your existing script imports) ... -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
        $('.select-checkbox').hide();
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
    
    function redirectToEdit() {
        // Assuming PatientID is stored in a variable called patientID
        // Redirect to Edit Patient.php with the PatientID parameter
        window.location.href = "Edit Patient.php?PatientID=" + patientID;
    }
</script>

<script>

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
            $('#updateButton, #deleteButton').show();
            $('#viewButton').hide();
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

    // Toggle sidebar functionality
    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
        $('#content').toggleClass('active');
    });

</script>

<script>

    document.getElementById("addPatient").addEventListener("click", function() {
        // Redirect to Add Patient.php
        window.location.href = "Add Patient.php";
    });
</script>

</body>
</html>
