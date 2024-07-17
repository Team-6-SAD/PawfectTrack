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
WITH LatestAppointments AS (
    SELECT
        p.PatientID,
        CONCAT(p.FirstName, ' ', p.LastName) AS FullName,
        t.Category,
        ai.SessionDays AS CurrentSession,
        ai.Status AS Status,
        ai.AppointmentDate AS AppointmentDate,
        ROW_NUMBER() OVER (
            PARTITION BY p.PatientID
            ORDER BY 
                CASE WHEN ai.Status = 'Done' THEN 1 ELSE 0 END,  -- Done sessions last
                ai.AppointmentDate ASC  -- Earliest appointment first
        ) AS rn
    FROM
        patient p
    LEFT JOIN (
        SELECT
            t1.PatientID,
            t1.TreatmentID,
            t1.Category
        FROM
            treatment t1
        WHERE
            t1.TreatmentID IN (
                SELECT MAX(t2.TreatmentID)
                FROM treatment t2
                GROUP BY t2.PatientID
            )
    ) t ON p.PatientID = t.PatientID
    LEFT JOIN appointmentinformation ai ON t.TreatmentID = ai.TreatmentID
    WHERE
        p.ActiveStatus = 'Inactive'
)

SELECT *
FROM LatestAppointments
WHERE rn = 1
ORDER BY PatientID DESC, AppointmentDate;



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


        <div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999; position:fixed;"></div>
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


    <!-- Additional buttons next to Edit -->
    <div class="d-flex flex-row flex-wrap align-items-center">
     
      
        <button id="deleteButton" class="btn btn-custom btn-blue-color btn-outline-info mr-2 ml-3" style="white-space: nowrap; color:white;"  >Restore</button>

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
    echo '<th scope="col">Patient ID</th>';
    echo '<th scope="col">Full Name</th>';
    echo '<th scope="col">Current Session</th>';
    echo '<th scope="col">Appointment Date</th>';
    echo '<th scope="col">Type of Exposure</th>';
    echo '<th scope="col">Status</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    // Loop through each patient and insert data into table rows
    while ($patient = mysqli_fetch_assoc($patients_result)) {
        echo "<tr>";
        echo "<td class='pl-3'>" . $patient['PatientID'] . "</td>";
        echo "<td class='pl-3'><a href='patientdetails-profile.php?patientID=" . $patient['PatientID'] . "'>" . $patient['FullName'] . "</a></td>";

        echo "<td class='pl-3'>" . 'Day' . " " .  $patient['CurrentSession'] . "</td>";
    
            echo "<td class='pl-3'>" . $patient['AppointmentDate'] . "</td>"; // Otherwise, display the appointment date
        
    
        echo "<td class='pl-3'>" . $patient['Category'] . "</td>";
        echo "<td class='pl-3'>" . $patient['Status'] . "</td>";

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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Data Table JS -->
<script src='https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js'></script>

    <script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.js"></script>


    <!-- ... (your existing script imports) ... -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="js/notifications.js"></script>
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
$(document).ready(function() {
    // DataTable initialization
    var table = $('#example').DataTable({
        order: [[0, 'desc']],
        paging: true,
        responsive: true,
        searching: true,
        pageLength: 5,
        lengthMenu: [[5, 25, 50, -1], [5, 25, 50, "All"]],
        dom: "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-12 ml-5 mt-3'>><<'col-sm-12'lp>>",
        language: {
            lengthMenu: "Display _MENU_"
        },
        select: {
            style: 'multi'
        }
    });

    // Handle row selection event
    table.on('select', function (e, dt, type, indexes) {
        if (type === 'row') {
            var selectedData = table.rows({ selected: true }).data();
            var selectedIDs = [];
            for (var i = 0; i < selectedData.length; i++) {
                selectedIDs.push(selectedData[i][0]); // Assuming PatientID is in the first column
            }
            console.log('Selected PatientIDs: ' + selectedIDs.join(', '));
            // Perform your action with selected IDs here
        }
    });

    // Handle row deselection event
    table.on('deselect', function (e, dt, type, indexes) {
        if (type === 'row') {
            var selectedData = table.rows({ selected: true }).data();
            var selectedIDs = [];
            for (var i = 0; i < selectedData.length; i++) {
                selectedIDs.push(selectedData[i][0]); // Assuming PatientID is in the first column
            }
            console.log('Selected PatientIDs: ' + selectedIDs.join(', '));
            // Perform your action with selected IDs here
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


    // Flag to track edit mode status
    var editMode = false;








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
   
});


// Function to delete selected rows
function deleteSelectedRows() {
    var table = $('#example').DataTable();
    var selectedRows = table.rows({ selected: true }).data().toArray();
    var selectedIDs = selectedRows.map(function(row) {
        return row[0]; // Assuming PatientID is in the first column
    });

    // Validate and perform deletion here if needed
    if (selectedIDs.length === 0) {
        alert('No rows selected for archival.');
        return;
    }

    // Assuming you have a form with id "deleteForm" and a hidden input "selectedRowsInput"
    $('#selectedRowsInput').val(selectedIDs.join(','));
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



</body>
</html>
