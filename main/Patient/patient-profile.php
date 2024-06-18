<?php
session_start();

// Check if the 'user' session variable is not set or is false (user not logged in)
if (!isset($_SESSION['user']) || $_SESSION['user'] !== true || !isset($_SESSION['userID'])) {
    // Redirect the user to the login page
    header("Location: Patient Login.php");
    exit(); // Terminate the script
}

// Include your database connection file
require_once '../backend/pawfect_connect.php';

// Get the userID from the session
$userID = $_SESSION['userID'];

// Initialize variables
$username = "";
$emailAddress = "";
$phoneNumber = "";
$profilePicture = "";
$firstName = "";
$lastName = "";
$middleName = "";

// Fetch username from usercredentials table
$stmtUsername = $conn->prepare("SELECT Username FROM usercredentials WHERE UserID = ?");
$stmtUsername->bind_param("i", $userID);
$stmtUsername->execute();
$resultUsername = $stmtUsername->get_result();

if ($resultUsername->num_rows === 1) {
    $rowUsername = $resultUsername->fetch_assoc();
    $username = $rowUsername['Username'];
}

// Fetch patient details (FirstName, LastName, MiddleName) using patientID
$stmtPatientDetails = $conn->prepare("SELECT MiddleName, FirstName, LastName FROM patient WHERE PatientID = (SELECT PatientID FROM usercredentials WHERE UserID = ?)");
$stmtPatientDetails->bind_param("i", $userID);
$stmtPatientDetails->execute();
$resultPatientDetails = $stmtPatientDetails->get_result();

if ($resultPatientDetails->num_rows === 1) {
    $rowPatientDetails = $resultPatientDetails->fetch_assoc();
    $firstName = $rowPatientDetails['FirstName'];
    $lastName = $rowPatientDetails['LastName'];
    $middleName = $rowPatientDetails['MiddleName'];
}

// Fetch email address and phone number from contactinformation table using patientID
$stmtContactInfo = $conn->prepare("SELECT ci.EmailAddress, ci.LineNumber FROM contactinformation ci INNER JOIN usercredentials uc ON ci.PatientID = uc.PatientID WHERE uc.UserID = ?");
$stmtContactInfo->bind_param("i", $userID);
$stmtContactInfo->execute();
$resultContactInfo = $stmtContactInfo->get_result();

if ($resultContactInfo->num_rows === 1) {
    $rowContactInfo = $resultContactInfo->fetch_assoc();
    $emailAddress = $rowContactInfo['EmailAddress'];
    $phoneNumber = $rowContactInfo['LineNumber'];
}

// Fetch profile picture from patient table using patientID
$stmtProfilePic = $conn->prepare("SELECT profilepicture FROM patient WHERE PatientID = (SELECT PatientID FROM usercredentials WHERE UserID = ?)");
$stmtProfilePic->bind_param("i", $userID);
$stmtProfilePic->execute();
$resultProfilePic = $stmtProfilePic->get_result();

if ($resultProfilePic->num_rows === 1) {
    $rowProfilePic = $resultProfilePic->fetch_assoc();
    $profilePicture = $rowProfilePic['profilepicture'];
}

// Close prepared statements
$stmtUsername->close();
$stmtPatientDetails->close();
$stmtContactInfo->close();
$stmtProfilePic->close();

// Close the database connection
$conn->close();
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
<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
  <link href="patient.css" rel="stylesheet">
  <title>My Profile</title>
  <style>
     .admin-photo {
        width: 120px;
        height: 120px;
        object-fit: cover; /* This ensures that the image covers the entire 120x120 area */
    }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="main-container">
        <!-- Header and Sidebar -->
        <?php include 'patient_header.php'; ?>
     


<!--Profile Picture and Details-->
        <div class="content" id="content">
    <div class="row justify-content-center d-flex align-items-center">
        <div class="col-md-12">
    <h3 class="text-center main-font-color mt-2"><b>MY PROFILE</b></h3>
</div>
        <div class="col-md-10"> 
            <div class="card mt-4">
                <div class="card-body p-5">
                          <div id="details" class="row">
                          <?php
// Check if adminPhoto is empty
if (!empty($profilePicture)) {
    // Display the admin photo
    echo '<img src="../uploads/'. $profilePicture . '" alt="Admin Photo" class="admin-photo">';
} else {
    // Display the placeholder image
    echo '<img src="../uploads/placeholder.png" alt="Placeholder Image" class="admin-photo">';
}
?>

            
                            <div class="text-container ml-4 mt-middle ">
                            <h6> <b><?php echo "$firstName" . " ". "$middleName" . " " . "$lastName" ?? "User"; ?></b> </h6>
                            <h6>Patient </h6>
                            
                            </div>
                            <div class="col text-right mt-3">
                            <img src="../img/img-dashboard/ABC-Sign.png" width="120px">
</div>

                        <div class="col-md-12 line-divider">  
                        <hr class="my-custom-line"> 
</div>
</div>

    <div class="row">
<div class="col-md-4 form-group ">
    <label for="fName">Username</label>
    <input type="text" class="form-control" id="fName" placeholder="First Name" value="<?php echo $username ?>" readonly>
</div>

<div class="col-md-4 form-group ">
    <label for="email">Email</label>
    <input type="email" class="form-control" id="email" placeholder="Email"  value="<?php echo $emailAddress ?>" readonly>
</div>

<div class="col-md-4 form-group ">
    <label for="phoneNumber">Phone Number</label>
    
    <div class="input-group">
    <div class="input-group-append">
            <span class="input-group-text">PH</span> <!-- Replace +1 with your desired suffix -->
        </div>
        <input type="text" class="form-control" id="phoneNumber" placeholder="Phone Number" value=<?php echo $phoneNumber ?> readonly>
        </div>
    </div>

</div>
</div>
</div>
</div>
</div>
</div>
</div>


       
      
<?php include 'patient-footer.php'; ?>
                       
    
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
