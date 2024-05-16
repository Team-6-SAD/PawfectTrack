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

// Get the AdminID from the sessionw
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
$sqlUsername ="SELECT * FROM admincredentials WHERE AdminID = ?";
$stmtUsername = mysqli_prepare($conn, $sqlUsername);
mysqli_stmt_bind_param($stmtUsername, "i", $adminID);
mysqli_stmt_execute($stmtUsername);
$resultUsername = mysqli_stmt_get_result($stmtUsername);
if ($usernameGet = mysqli_fetch_assoc($resultUsername)) {
    $username = $usernameGet['AdminUsername'];
} else {
    // Admin information not found
    echo "Admin information not found!";
}
$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
unset($_SESSION['success_message']); // Unset the session variable to prevent displaying the same message again
$errorMessage = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['error_message']); // Unset the session variable to prevent displaying the same message again

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

  <link rel='stylesheet' href='https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css'>
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet"> <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="css/hamburgers.css" rel="stylesheet">
  <link href="css/userdashboard.css" rel="stylesheet">
  <title>My Settings</title>
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
        <?php include 'includes/admin_header.php'; ?>
        <div class="sidebar">
            <?php include 'includes/sidebar.php'; ?>
        </div>


<!--Profile Picture and Details-->
        <div class="content" id="content">
    <div class="row justify-content-center align-items-center d-flex">

        <div class="col-md-12"> 
        <h3 class="text-center main-font-color mt-2"><b>MY SETTINGS</b></h3>
</div>
<div class="col-md-10 "> 
            <div class="card mt-4 p-5">
            <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $successMessage; ?>
        </div>
    <?php endif; ?>

    <!-- Display error message if exists -->
    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $errorMessage; ?>
        </div>
    <?php endif; ?>
            <form id="profileForm" action="backend/settings-backend.php" method="POST" enctype="multipart/form-data">
            <div class="card-body">
    <div id="details" class="row">
    <?php
// Check if adminPhoto is empty
if (!empty($adminPhoto)) {
    // Display the admin photo
    echo '<img src="uploads/' . $adminPhoto . '" alt="Admin Photo" class="admin-photo">';
} else {
    // Display the placeholder image
    echo '<img src="uploads/placeholder.png" alt="Placeholder Image" class="admin-photo">';
}
?>

        <div class="settings-container ml-4">
            <!-- Label associated with file input -->
            <label for="profile_picture" class="custom-file-upload">Upload  New Photo</label>
            <!-- Actual file input element -->
          <!-- Actual file input element with accept attribute -->
<input id="profile_picture" name="profile_picture" class="form-control-file" type="file" accept=".jpg, .jpeg, .png">

            <!-- Optional: Element to display selected file name -->
            <span class="gray">Allowed JPG or PNG. Max size of 800k </span>
            <div class="file-selected" id="file-selected"></div>
        </div>
  
    

                           
                            
                        
        <div class="col text-right mt-3">
                            <img src="img/img-dashboard/ABC-Sign.png" width="120px">
</div>

                        <div class="col-md-12 line-divider">  
                        <hr class="my-custom-line"> 
</div>
</div>

    <div class="row justify-content-between">

<div class="col-md-4 form-group">
    <label for="fName">First Name</label>
    <input type="text" class="form-control" id="fName" name="fName" placeholder="<?php echo $firstName ?>" >
</div>

<div class="col-md-4 form-group">
    <label for="mName">Middle Name</label>
    <input type="text" class="form-control" id="mName" name="mName" placeholder="<?php echo $middleName ?>" >
</div>



<div class="col-md-4 form-group">
    <label for="lName">Last Name</label>
        <input type="text" class="form-control" id="lName" name="lName" placeholder="<?php echo $lastName?>"  >
        </div>
    </div>


    <div class="row justify-content-between">

<div class="col-md-4 form-group">
    <label for="username">Username</label>
    <input type="text" class="form-control" id="username" name="username" placeholder="<?php echo $username ?>" >
</div>

<div class="col-md-4 form-group">
    <label for="email">Email</label>
    <input type="email" class="form-control" id="email" name="email" placeholder="<?php echo $email ?>" >
</div>



<div class="col-md-4 form-group">
    <label for="phoneNumber">Phone Number</label>
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">PH</span>
        </div>
        <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" placeholder="<?php echo $phoneNumber?>" >
    </div>
</div>






</div>

</div>
<div class="button-containers">
<button type="button" id="resetButton" class="btn btn-lg  btn-outline-danger btn-border-custom px-5 py-1 mr-3" style="font-size:13px; border-radius:6px;"><b>Reset </b></button>
<button type="submit" class="btn  btn-lg green pl-3 pr-3 ml-3" style="font-size:13px; border-radius:6px;">Save Changes</button>
</div>
</div>
</div>



<div class="col-md-10 mb-5"> 
    <div class="card mt-4 p-3 pb-5">
        <h6 class="text-left gray mt-2"><b>Delete My Account</b></h6>
        <div class="card-body">
      
                <input type="checkbox" id="accountDeactivation" name="accountDeactivation">
                <label for="accountDeactivation"> I understand this action cannot be undone.</label>
    
        </div>
        <button  type="button" id="deactivateButton" class="btn-settings-2 bg-danger ml-3" style="font-size:13px; border-radius:6px;">Delete Account</button>
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
<img src="img/img-alerts/caution-mark.png">
</div>
<h2 style="letter-spacing: -1px; color:#5e6e82;"class="text-center m-0"><b>DELETE ACCOUNT</b></h2>
<div class="justify-content-center d-flex">
    <div>
<small style="letter-spacing: -1px; color:#5e6e82;">Are you sure you want to delete your <br></small>
<div>
    <small style="letter-spacing: -1px; color:#5e6e82; display: block; margin: 0 auto; text-align: center;">account <br></small>
  </div>
<small style="letter-spacing: -1px; color:#5e6e82;">&bull; Deleting your account will remove all<br></small>
<small style="letter-spacing: -1px; color:#5e6e82;">your information in the database<br></small>
<small style="letter-spacing: -1px; color:#5e6e82;">&bull; This action is permanent and cannot<br></small>
<small style="letter-spacing: -1px; color:#5e6e82;">be undone<br></small>
</div>
</div>
<div class="align-items-center justify-content-center d-flex mb-3 mt-3">
<button type="button" style="background-color: #C1C1C1; border:none;" class="btn btn-success px-3 mr-2 py-2" data-dismiss="modal"><b>Cancel</b></button>
<button type="button" style="background-color: #EE5253; border:none;" class="btn btn-success px-3 py-2" id="deactivateButtonConfirm"><b>Delete</b></button>
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
  $('#deactivateButton').click(function(event) {
    var confirmDeactivation = $("#accountDeactivation").prop("checked");
    if (confirmDeactivation) {
      event.preventDefault();
      $('#removalConfirmationModal').modal('show');
    } else {
      alert("Please confirm account deactivation.");
    }
  });
});


 
    </script>
<script>
    // Add an event listener to the "Reset" button
    document.getElementById('resetButton').addEventListener('click', function() {
        // Reset all form fields to their default values
        document.getElementById('profileForm').reset();
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
// Optional: Display selected file name
const fileInput = document.getElementById('profile_picture');
const fileDisplay = document.getElementById('file-selected');

fileInput.addEventListener('change', function() {
  if (fileInput.files.length > 0) {
    fileDisplay.textContent = `Selected File: ${fileInput.files[0].name}`;
  } else {
    fileDisplay.textContent = '';
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
<script>
    // Function to validate email format
    function validateEmail(email) {
        if (email.trim() === '') return true; // Allow empty value
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Function to validate username length
    function validateUsername(username) {
        if (username.trim() === '') return true; // Allow empty value
        return username.length <= 16;
    }

    // Function to validate phone number format
    function validatePhoneNumber(phoneNumber) {
        if (phoneNumber.trim() === '') return true; // Allow empty value
        const phoneRegex = /^09\d{9}$/;
        return phoneRegex.test(phoneNumber);
    }

    // Function to validate the entire form
   // Function to validate the entire form
function validateForm() {
    // Get form input values
    const email = document.getElementById('email').value;
    const username = document.getElementById('username').value;
    const phoneNumber = document.getElementById('phoneNumber').value;
    const profilePicture = document.getElementById('profile_picture').value; // Get the filename of the uploaded image
    const firstName = document.getElementById('fName').value;
const middleName = document.getElementById('mName').value;
const lastName = document.getElementById('lName').value;
    // Validation results
    const isEmailValid = validateEmail(email);
    const isUsernameValid = validateUsername(username);
    const isPhoneNumberValid = validatePhoneNumber(phoneNumber);

    // Image file extension validation
 

    // Display error messages if validation fails
    if (!isEmailValid) {
        alert('Please enter a valid email address.');
        return false; // Prevent form submission
    }
    if (!isUsernameValid) {
        alert('Username must be 16 characters or less.');
        return false; // Prevent form submission
    }
    if (!isPhoneNumberValid) {
        alert('Phone number must start with 09 and be 11 digits long.');
        return false; // Prevent form submission
    }

    // Check if any field is filled
    if (email.trim() === '' && username.trim() === '' && phoneNumber.trim() === '' && profilePicture.trim() === '' && firstName.trim() === '' && lastName.trim() === '' && middleName.trim() === '') {
        alert('Please fill in at least one field.');
        return false; // Prevent form submission
    }

    // If all validations pass, allow form submission
    return true;
}


    // Add form submission event listener
    document.getElementById('profileForm').addEventListener('submit', function(event) {
    // Check if the submit button clicked is not the "Deactivate" button
    if (event.submitter !== document.getElementById('deactivateButton')) {
        // Perform form validation before submission
        if (!validateForm()) {
            event.preventDefault(); // Prevent form submission
        }
    }
});$(document).ready(function() {
    $("#deactivateButtonConfirm").on("click", function(event) {
        event.preventDefault(); // Prevent the default form submission behavior
        
        console.log("Deactivate button clicked"); // Log a message to the console
        var confirmDeactivation = $("#accountDeactivation").prop("checked");

        // Check if the deactivation checkbox is checked
        if (confirmDeactivation) {
            // Make an AJAX request to deactivate.php
            $.ajax({
                url: "backend/deactivate.php", // URL to the server-side script
                type: "POST", // HTTP method
                success: function(response) {
                    // Handle the response from the server
                    console.log("Deactivation successful:", response);
                    window.location.href = "Admin Login.php";
                    // Optionally, update the UI based on the response
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error("Error:", error);
                }
            });
        } else {
            alert("Please confirm account deactivation.");
        }
    });
});







</script>




</body>
</html>
