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
// Check if the patientID is set in the URL

    $sql = "SELECT 
            ai.AppointmentID, 
            ai.AppointmentDate, 
            ai.SessionDays,
            ai.Status,
            GROUP_CONCAT(DISTINCT mu.MedicineName SEPARATOR ', ') AS MedicineNames
        FROM 
            appointmentinformation AS ai
        JOIN 
            medicineusage AS mu ON ai.TreatmentID = mu.TreatmentID
        WHERE 
            ai.PatientID = ?
        GROUP BY 
            ai.AppointmentID, ai.AppointmentDate, ai.SessionDays, ai.Status";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $patientID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
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
  <link rel="icon" href="img/Favicon 2.png" type="image/png">
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel='stylesheet' href='https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css'>
  <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"  rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet"> <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
<link href="css/hamburgers.css" rel="stylesheet">
<link href="patient.css" rel="stylesheet">
  <link href="css/userdashboard.css" rel="stylesheet">
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
table.dataTable thead .sorting{
    background-image: none;
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
            content: " Appointment ID";
        }
          /*
          Label the data
          */
          td:nth-of-type(2):before {
              content: "Service";
          }

          td:nth-of-type(3):before {
              content: "Session";
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
  <title>Patient Details - Appointments</title>
  
</head>
<body>
<div class="container-fluid">
    <div class="main-container">
        <!-- Header and Sidebar -->
        <?php include 'patient_header.php'; ?>


<!--Profile Picture and Details-->
        <div class="content" id="content">
    <div class="row mt-4 justify-content-center d-flex mb-5">
        <div class="card p-5">
        <div class="card-body p-3">
            <div class="col-md-12 justify-content-center d-flex mb-4">
            <img src="ABC-Vax-Header.png" width="400px">
</div>

        
<div class="row d-flex justify-content-center">

    <div class="col-md-12">

      
        <table id="example1" class="table table-striped text-center">
    <thead class="table-header mb-5">
        <tr>
            <th class="text-center">Appointment ID</th>
            <th class="text-center">Service</th>
            <th class="text-center">Session</th>
            <th class="text-center">Appointment Date</th>  
            <th class="text-center">Status</th>
        </tr>
    </thead>
    <tbody>
<?php
// Iterate over the result set obtained from the database query
while ($row = mysqli_fetch_assoc($result)) {
    // Extract appointment details from the current row
    $appointmentID = $row['AppointmentID'];
    $service = $row['MedicineNames'];
    $session = $row['SessionDays'];
    $appointmentDate = $row['AppointmentDate'];
    $status = $row['Status'];

    // Apply appointment details to table rows dynamically
    echo "<tr style='background-color: white;'>";
    echo "<td>$appointmentID</td>";
    echo "<td>$service</td>";
    echo "<td>$session</td>";
    echo "<td>$appointmentDate</td>";
    echo "<td><button class='btn btn-table status-button " . ($status == 'Done' ? 'green' : 'yellow') . "' data-appointment-id='$appointmentID'>$status</button></td>";
    echo "</tr>";
}
?>
    </tbody>
</table>

            </form>
        </div>
        </div>
    </div>
    </div>
</div>
</div>        
</div>










                       
    
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    
    <!-- Data Table JS -->
<script src='https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js'></script>

    <script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>


 
  
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <script>
$(document).ready(function() {
    var appointmentID; // Define appointmentID variable outside the functions
    var status;

    // Event listener for form submission
    $('#changeStatusForm').submit(function(event) {
        event.preventDefault(); // Prevent the default form submission behavior

        // Serialize the form data
        var formData = $(this).serialize();

        // Submit the form data via AJAX
        $.ajax({
            type: 'POST',
            url: 'backend/update_status.php',
            data: formData,
            success: function(response) {
                // Handle success response
                location.reload();
            },
            error: function(xhr, status, error) {
                // Handle error response
            }
        });
    });

    // Event listener for clicking the status button
    $('.status-button').click(function() {
        $('#medicalDetails').empty(); // Clear previous medical details
        status = $(this).text().trim();
        appointmentID = $(this).data('appointment-id');

        // Update the appointment ID input field in the modal
        $('#appointmentID').val(appointmentID);

        // Arrays to store medicine brands, quantities, and treatment IDs
        var medicineBrand = [];
        var quantity = [];
        var treatmentID = [];
        var dosage = [];
        var medicineName = [];
        // Check if the status is 'Pending'
        if (status === 'Pending') {
            // Show the change status modal
            $('#changeStatusModal').modal('show');

            // Fetch medical details via AJAX
            $.ajax({
                type: 'POST',
                url: 'backend/fetch_medical_details.php',
                data: { appointmentID: appointmentID },
                dataType: 'json',
                success: function(response) {
                    // Append form fields for each medicine brand with quantity inputs
                    $.each(response, function(index, item) {
                        dosage.push(item.dosage);
                        medicineBrand.push(item.medicineBrand); // Push medicineBrand to array
                        quantity.push(item.quantity); // Push quantity to array
                        treatmentID.push(item.treatmentID); // Push treatmentID to array
                        medicineName.push(item.medicineName);
                        
                        var formField = '<div class="form-group">';
                        formField += '<input type="hidden" name="medicineBrand[]" value="' + item.medicineBrand + '">';
                        formField += '<input type="hidden" name="treatmentID[]" value="' + item.treatmentID + '">';
                        formField += '<input type="hidden" name="dosage[]" value="' + item.dosage + '">';
                        formField += '<input type="hidden" name="medicineName[]" value="' + item.medicineName + '">';
                        formField += '<label>' + item.medicineBrand + '</label>';
                        formField += '<input type="number" class="form-control" name="quantity[]" value="' + item.quantity + '" min="0">';
                        formField += '</div>';

                        $('#medicalDetails').append(formField);
                    });
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
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
    $('#example1 tbody').on('change', '.select-checkbox', function () {
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
    // DataTable initialization
    var table = $('#example1').DataTable({
        paging: false,
        responsive: true,
        searching: true,
        
       
        "dom": 
     
        "<'row'<'col-sm-12 p-0 m-0't>>" + // Place table in a row
        "<'row'<'col-sm-12 d-flex justify-content-between'<'col-sm-6'l><'col-sm-6'p>>>" // Place length menu and pagination in the same row
    ,
       
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
                text: '<img style="width:25px; height:25px;" src="pdf_image.png" alt="PDF">',
                titleAttr: 'PDF',
                className: 'btn-img'
            }
        ],
        pageLength: 5,
        lengthMenu: [ [5,10, 25, 50, -1], [5,10, 25, 50, "All"] ],
        language: { "lengthMenu": "Display _MENU_ "
           
        }
    });

    // Hide buttons initially
    $('.btn-img').hide();

    // Toggle button visibility
    $('#toggleButtons').on('click', function () {
        $('.btn-img').toggle();
    });

    // Link custom search input with DataTable
    var customSearchInput = $('#customSearchInput');

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
    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
        $('#content').toggleClass('active');
    });
});






});
</script>
</body>
</html>
