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

$sql = "SELECT 
            ai.AppointmentDate, 
            ai.SessionDays, 
            ai.PatientID, 
            CONCAT(p.FirstName, ' ', p.LastName) AS FullName,
            bd.ExposureType
        FROM 
            appointmentinformation AS ai
        JOIN 
            patient AS p ON ai.PatientID = p.PatientID
        JOIN 
            bitedetails AS bd ON ai.PatientID = bd.PatientID
        WHERE 
            ai.Status = 'Pending'
        ORDER BY 
            ABS(DATEDIFF(NOW(), ai.AppointmentDate))
        LIMIT 5";

// Execute the SQL query
$result = mysqli_query($conn, $sql);
// Close the database connection
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
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel='stylesheet' href='https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css'>
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet"> <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="css/hamburgers.css" rel="stylesheet">
  <link href="css/userdashboard.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
 <title>Admin Dashboard</title>
<style>
    table.dataTable thead .sorting:before, table.dataTable thead .sorting_asc:before, table.dataTable thead .sorting_desc:before, table.dataTable thead .sorting_asc_disabled:before, table.dataTable thead .sorting_desc_disabled:before {
        content: "\F148" !important; /* Font Awesome icon for ascending sort */
        font-family: "bootstrap-icons";
        right: 0.8em !important;
        top: 40% !important;
        font-size: 14px !important;
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
        .card{
        
            border-radius: 5px;
            border:none;
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
            
            <!-- Content -->
            <div class="content" id="content">
                <div class="row mr-5 ml-3 mt-0 pt-0">
                    <div class="col-12 mt-0 pt-0">
                        <div class="card-body card-image p-0 align-items-center">
                            <div class="row logo-font-color mt-0 pl-2">
                                <div class="col-md-6 text-left mt-5 ml-4">
                                    <h3 class="mt-1"><b>Welcome, Admin!</b></h3>
                                    <small style="word-wrap: break-word; "><i>We prioritized your well-being and the safety of your dear ones.</i></small>
                                </div>
                                <div class="col-md-5 text-right mt-4 pt-2">
                                    <img src="img/img-dashboard/ABC-Sign-White.png" alt="Description of the image" class="img-logo ml-auto">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center align-items-center d-flex mr-5 ml-3 mb-3">
                    <div class="col-md-6 mt-3">
                        <div class="card px-3 pb-1">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <img src="img/img-dashboard/Vector.png" alt="Logo" class="img-card-icons mr-2 mt-1">
                                    <div>
                                        <h1 class="text-font-big main-font-color" style="font-size:3rem; margin-bottom:-12px;"><b><?php $sql = "SELECT COUNT(*) AS TotalPatients FROM patient WHERE ActiveStatus = 'Active'";

// Execute the query
$result = mysqli_query($conn, $sql);
// Check if the query was successful
if ($result) {
    // Fetch the result as an associative array
    $row = mysqli_fetch_assoc($result);

    // Get the total patient count from the result
    $totalPatients = $row['TotalPatients'];

    // Output the total patient count

} else {
    // Query failed, handle the error

}echo $totalPatients ?></b></h1>
                                        <p class="small-text mb-0">Patient Count</p>
                                        <h5 class="text-font-medium main-font-color mb-0"><b>Total Number of Patients</b></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mt-3">
                        <div class="card px-3 pb-1">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <img src="img/img-dashboard/injection-blue-db.png" alt="Logo" class="img-card-icons-1 mr-2">
                                    <div>
                                        <h1 class="text-font-big main-font-color" style="font-size:3rem; margin-bottom:-12px;"><b><?php
// Assuming you have already established a database connection

// SQL query to get the total count of treatments
$sql = "SELECT COUNT(*) AS TotalTreatments FROM treatment";

// Execute the query
$result = mysqli_query($conn, $sql);

// Check if the query was successful
if ($result) {
    // Fetch the result as an associative array
    $row = mysqli_fetch_assoc($result);

    // Get the total count of treatments from the result
    $totalTreatments = $row['TotalTreatments'];

    // Output the total count of treatments
    echo $totalTreatments;
} else {
    // Query failed, handle the error
   
}

// Close the database connection

?></b></h1>
                                        <p class="small-text mb-0">Treatment Count</p>
                                        <h5 class="text-font-medium main-font-color mb-0"><b>Total Number of Treatments</b></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center align-items-center d-flex mr-5 ml-3 mb-5">
                    <div class="col-md-12 mt-2 mx-auto">
                        <div class="card mx-auto table-card px-3 mb-5">
                            <div class="table-header-1 d-flex justify-content-between align-items-center">
                                <h5 class="card-title mt-5 ml-4 gray"><b>List of Upcoming Vaccine Sessions</b></h5>
                                <div class="flex-grow-1"></div>
                                <div id="buttonContainer" class="d-flex flex-column flex-sm-row align-items-center mb-2 ml-4 mt-1 mt-4">
                                <button id="editButton" class="btn btn-gray-color btn-custom  mr-3 px-3" style="color:white;  border-radius: 8px">
  Action <span style="font-size: 8px; vertical-align: middle;"> &#9654; </span>
</button>
                                    <!-- Additional buttons next to Edit -->
                                    <div class="d-flex flex-row flex-wrap align-items-center">
                                        <button id="viewButton" style="white-space: nowrap; color: white;  border-radius: 8px;" class="btn btn-custom btn-blue-color btn-outline-info px-4 mr-3" >View</button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive"> <!-- Added this div for responsiveness -->
                                <form id="updateForm" action="update.php" method="post">
                                    <input type="hidden" name="statusUpdate[]" class="status-update-input">
                                    <!-- Other form elements or buttons go here -->
                                </form>

                                <form id="deleteForm" action="delete.php" method="post">
                                    <input type="hidden" name="selectedRows[]" id="selectedRowsInput">
                                    <div class="card-body">
                                 <?php   $sql = "SELECT 
            ai.AppointmentDate, 
            ai.SessionDays, 
            ai.PatientID, 
            CONCAT(p.FirstName, ' ', p.LastName) AS FullName,
            bd.ExposureType
        FROM 
            appointmentinformation AS ai
        JOIN 
            patient AS p ON ai.PatientID = p.PatientID
        JOIN 
            bitedetails AS bd ON ai.PatientID = bd.PatientID
        WHERE 
            ai.Status = 'Pending'
        ORDER BY 
            ABS(DATEDIFF(NOW(), ai.AppointmentDate))
        LIMIT 5";

// Execute the SQL query
$result = mysqli_query($conn, $sql);

// Check if there are any results
if (mysqli_num_rows($result) > 0) {
    ?>
    <table id="example" class="table">
        <thead class="table-header-alt">
            <tr style="font-size:12px;">
                <th></th>
                <th>Patient ID</th>
                <th>Full Name</th>
                <th>Current Session</th>
                <th>Appointment Date</th>
                <th>Type of Exposure</th>
            </tr>
        </thead>
        <tbody>
    <?php
    // Output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr style="font-size:12px;">';
        echo '<td><input type="checkbox" class="select-checkbox" name="selectedRows[]" value="' . $row['PatientID'] . '"></td>';
        echo '<td>' . $row['PatientID'] . '</td>';
        echo '<td>' . $row['FullName'] . '</td>';
        echo '<td>' . $row['SessionDays'] . '</td>';
        echo '<td>' . $row['AppointmentDate'] . '</td>';
        echo '<td>' . $row['ExposureType'] . '</td>';
        echo '</tr>';
    }
    ?>
</tbody>


    </table>
    <?php
} else {
    echo '<p>No pending appointments found.</p>';
}

        ?>
        <!-- ... other rows ... -->
    </tbody>
</table>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- End of content -->
        </div> <!-- End of main-container -->
    </div> <!-- End of container-fluid -->




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
        window.location.href = 'patientdetails-appointments.php?patientID=' + applicantID;
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
</body>
</html>