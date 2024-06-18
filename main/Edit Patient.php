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
 
    $lastName = $row['lastname'];
    $adminPhoto = $row['adminphoto'];

    // Now you can use these variables to display the admin information in your HTML
} else {
    // Admin information not found
    echo "Admin information not found!";
}
if (isset($_GET['patientID'])) {
  $patientID = $_GET['patientID'];

  // SQL query to fetch patient details
  $sql = "
  SELECT 
      p.LastName, 
      p.FirstName, 
      p.MiddleName, 
      TIMESTAMPDIFF(YEAR, p.BirthDate, CURDATE()) AS Age, 
      p.BirthDate, 
      p.Weight, 
      p.Sex, 
      ci.LineNumber AS ContactNumber, 
      ci.EmailAddress, 
      pa.Province, 
      pa.City, 
      pa.Address, 
      ec.FullName AS EmergencyContactName, 
      ec.Relationship AS EmergencyContactRelationship, 
      ec.LineNumber AS EmergencyContactNumber
  FROM 
      patient p
  LEFT JOIN 
      contactinformation ci ON p.PatientID = ci.PatientID
  LEFT JOIN 
      patientaddress pa ON p.PatientID = pa.PatientID
  LEFT JOIN 
      emergencycontact ec ON p.PatientID = ec.PatientID
  WHERE 
      p.PatientID = ?
  ";

  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "i", $patientID);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if ($row = mysqli_fetch_assoc($result)) {
      // Store patient details in variables
      $lastName = $row['LastName'];
      $firstName = $row['FirstName'];
      $middleName = $row['MiddleName'];
      $age = $row['Age'];
      $birthDate = $row['BirthDate'];
      $weight = $row['Weight'];
      $sex = $row['Sex'];
      $contactNumber = $row['ContactNumber'];
      $emailAddress = $row['EmailAddress'];
      $province = $row['Province'];
      $city = $row['City'];
      $address = $row['Address'];
      $emergencyContactName = $row['EmergencyContactName'];
      $emergencyContactRelationship = $row['EmergencyContactRelationship'];
      $emergencyContactNumber = $row['EmergencyContactNumber'];
  } else {
      echo "No patient found with the given ID.";
      exit(); // Terminate the script
  }
} else {
  echo "No PatientID provided.";
  exit(); // Terminate the script
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
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet"> <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="css/hamburgers.css" rel="stylesheet">
  <link href="css/userdashboard.css" rel="stylesheet">
  <title>Edit Patient</title>
<style>
  .red{
    color:red;
  }
  .error-message{
    color:red;
    font-size: 11px;
    font-weight: bold;
  }
  .error-border {
  border: 1px solid red !important;
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
    <div class="row justify-content-center mb-5 mt-4">

        <div class="col-md-11"> 
            <div class="card" style="background-color: #f5f5f5;">
                <div class="card-header card-header-patient-form text-center" style="background-color: #5E6E82;">
                    <h3> <b>EDIT PATIENT</b> <h3>
                </div>
                <div class="card-body px-5">
               

                



<form id="multi-step-form" enctype="multipart/form-data">
  <div class="step active" id="step1">
    <div class="row justify-content-center mt-3 px-3 ">
    <input type="hidden" name="patientID" value="<?php echo $patientID; ?>">
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="fName">First Name<span class="red">*</span></label>
        <input type="text" id="fName" name="fName" placeholder="First Name" value="<?php echo $firstName; ?>" class="form-control" oninput="preventLeadingSpace(event)" >
      </div>
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="mName">Middle Name</label>
        <input type="text" id="mName" name="mName" placeholder="Middle Name" value="<?php echo $middleName; ?> "class="form-control" oninput="preventLeadingSpace(event)" >
      </div>
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="lName">Last Name<span class="red">*</span></label>
        <input type="text" id="lName" name="lName" placeholder="Last Name"class="form-control" value="<?php echo $lastName; ?>" oninput="preventLeadingSpace(event)" >
      </div>
    </div>
    <div class="row justify-content-center px-3">
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="birthDate">Birth Date<span class="red">*</span></label>
        <input type="date" id="birthDate" name="birthDate" placeholder="Birth Date" class="form-control" value="<?php echo $birthDate; ?>"  max="<?php echo date('Y-m-d', strtotime('-1 year')); ?>"onkeydown="return false">

      </div>
  
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="age">Age<span class="red">*</span></label>
        <input type="tel" id="age" name="age" placeholder="Age" value="<?php echo $age; ?>" class="form-control" >
      </div>
   
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="sex">Sex<span class="red">*</span></label>
        <input type="text" id="sex" name="sex" class="form-control" value="<?php echo $sex; ?>">
      </div>
    </div>
    <div class="row justify-content-center px-3 mb-0">
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="weight">Weight (kg)<span class="red">*</span></label>
        <input type="tel" id="weight" name="weight" placeholder="Weight (kg)" value="<?php echo $weight; ?>" class="form-control">
      </div>
         <div class="col-lg-4 form-group px-4 mb-3">
                                <label for="phoneNumber">Phone Number<span class="red">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="color: white; background-color: #5E6E82; font-size: 14px;"><b>PH </b></span>
                                    </div>

                                    <input type="tel" id="phoneNumber" name="phoneNumber" class="form-control" placeholder="09123456789" style="min-width: 140px" value="<?php echo $contactNumber; ?>" >
                                   <div>

                                   </div>
                                </div>
                                <small id="phone-number-error" class="error-message"></small>
                            
                            </div>
                            
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="email">Email Address<span class="red">*</span></label>
        <input type="email" id="email" name="email" placeholder="Email Address" class="form-control" value="<?php echo $emailAddress; ?>" >
      </div>
    </div>
    <div class="row justify-content-center px-3 mb-0">
      <div class="col-lg-4 form-group px-4 mb-3">
      <label for="province">Province<span class="red">*</span></label>
      <input type="text" id="province" name="province" class="form-control" placeholder="Province" value="<?php echo $province; ?>">

      </div>
      <div class="col-lg-4 form-group px-4 mb-3">
      <label for="city">City<span class="red">*</span></label>
      <input type="text" id="city" name="city" class="form-control" placeholder="City" value="<?php echo $city; ?>">

      </div>
      <div class="col-lg-4 form-group px-4 mb-3">
      <label for="address">Address<span class="red">*</span></label>
        <input type="text" id="address" name="address" class="form-control" placeholder="Address" value="<?php echo $address; ?>">
      </div>
    </div>
    <div class="row justify-content-center px-3 mb-3">
      <div class="col-lg-4 form-group px-4">
        <label for="emergencyContact">In case of Emergency, notify<span class="red">*</span></label>
        <input type="text" id="emergencyContact" name="emergencyContact" placeholder="Full Name" class="form-control" value="<?php echo $emergencyContactName; ?>"   oninput="preventLeadingSpace(event)">
      </div>
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="relationship">Relationship<span class="red">*</span></label>
        <input type="text" id="relationship" name="relationship" placeholder="Relationship" class="form-control" value="<?php echo $emergencyContactRelationship; ?>"   oninput="preventLeadingSpace(event)">
      </div>
      <div class="col-lg-4 form-group px-4 mb-3">
                                <label for="emergencyPhoneNumber">Phone Number<span class="red">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="color: white; background-color: #5E6E82; font-size: 14px;"><b>PH </b></span>
                                    </div>

                                    <input type="tel" id="emergencyPhoneNumber" name="emergencyPhoneNumber" class="form-control" placeholder="09123456789" value="<?php echo $emergencyContactNumber?> " style="min-width: 140px">
                                   <div>

                                   </div>
                                </div>
                                <small id="emergency-phone-number-error" class="error-message"></small>
                            
                            </div>
    <div class="row justify-content-center">
    <button type="submit" id="submit-button" class="btn-customized" style="background-color: #5E6E82;">Edit</button>
</div>
  </div>
  </div>


  </form>
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
  // Select all number input fields
const numberInputs = document.querySelectorAll('input[type="number"]');

// Loop through each number input field
numberInputs.forEach(function(input) {
    // Add an event listener for the input event
    input.addEventListener('input', function() {
        // Get the current value of the input field
        let value = parseFloat(this.value);

        // If the value is negative, set it to 0
        if (value < 0 || isNaN(value)) {
            this.value = 0;
        }
    });
});

  // Get the weight input element
const weightInput = document.getElementById('weight');

// Add an event listener for the keydown event
weightInput.addEventListener('keydown', function(event) {
    const keyCode = event.keyCode;

    if (
        [8, 9, 27, 13].includes(keyCode) || // Allow: backspace, delete, tab, escape, enter
        (keyCode === 65 && event.ctrlKey === true) || // Allow: Ctrl+A
        (keyCode >= 35 && keyCode <= 40) // Allow: home, end, left, right, down, up
    ) {
        return;
    }

    if ((event.shiftKey || (keyCode < 48 || keyCode > 57)) && (keyCode < 96 || keyCode > 105)) {
        event.preventDefault();
    }
});

// Get the email input element
const emailInput = document.getElementById('email');

// Add an event listener for the keydown event
emailInput.addEventListener('keydown', function(event) {
    // Get the key code of the pressed key
    const keyCode = event.keyCode;

    // If the pressed key is the spacebar, prevent the default behavior
    if (keyCode === 32) {
        event.preventDefault();
    }
});

// Get the age input element
const ageInput = document.getElementById('age');

// Add an event listener for the keydown event
ageInput.addEventListener('keydown', function(event) {
    // Get the key code of the pressed key
    const keyCode = event.keyCode;

    // Allow special keys like backspace, delete, arrow keys, etc.
    if (
        // Allow: backspace, delete, tab, escape, enter
        [8, 9, 27, 13].includes(keyCode) ||
        // Allow: Ctrl+A
        (keyCode === 65 && event.ctrlKey === true) ||
        // Allow: home, end, left, right, down, up
        (keyCode >= 35 && keyCode <= 40)
    ) {
        // Let it happen, don't do anything
        return;
    }

    // Ensure that it is a number and stop the keypress if it isn't
    if ((event.shiftKey || (keyCode < 48 || keyCode > 57)) && (keyCode < 96 || keyCode > 105)) {
        event.preventDefault();
    }
});
// Get the phoneNumber and emergencyPhoneNumber input elements
const phoneNumberInput = document.getElementById('phoneNumber');
const emergencyPhoneNumberInput = document.getElementById('emergencyPhoneNumber');

// Add an event listener for the keydown event on phoneNumber input
phoneNumberInput.addEventListener('keydown', function(event) {
    const keyCode = event.keyCode;

    if (
        [8, 9, 27, 13].includes(keyCode) || // Allow: backspace, delete, tab, escape, enter
        (keyCode === 65 && event.ctrlKey === true) || // Allow: Ctrl+A
        (keyCode >= 35 && keyCode <= 40) // Allow: home, end, left, right, down, up
    ) {
        return;
    }

    if ((event.shiftKey || (keyCode < 48 || keyCode > 57)) && (keyCode < 96 || keyCode > 105)) {
        event.preventDefault();
    }
});

// Add an event listener for the keydown event on emergencyPhoneNumber input
emergencyPhoneNumberInput.addEventListener('keydown', function(event) {
    const keyCode = event.keyCode;

    if (
        [8, 9, 27, 13].includes(keyCode) || // Allow: backspace, delete, tab, escape, enter
        (keyCode === 65 && event.ctrlKey === true) || // Allow: Ctrl+A
        (keyCode >= 35 && keyCode <= 40) // Allow: home, end, left, right, down, up
    ) {
        return;
    }

    if ((event.shiftKey || (keyCode < 48 || keyCode > 57)) && (keyCode < 96 || keyCode > 105)) {
        event.preventDefault();
    }
});







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




// Add event listener to submit button
document.getElementById('submit-button').addEventListener('click', function(e) {
    e.preventDefault();


        // If no errors, proceed with form submission using AJAX
        const formData = new FormData(document.getElementById('multi-step-form'));

        $.ajax({
            url: 'backend/edit-existing.php', // Replace 'submit.php' with your actual form submission endpoint
            method: 'POST',
            data: formData,
            contentType: false, // Important for file uploads
            processData: false, // Important for file uploads
            success: function(response) {
                // Handle success response
                console.log(response);
                // Optionally, you can reset the form after successful submission
                window.location.href = 'patient-list.php'; // Replace 'patient-list.php' with the actual URL of your Patient List page
            },
            error: function(xhr, status, error) {
                // Handle error response
                console.error(error);
            }
        });
 
        // If there are errors, prevent form submission
        console.log('Please fill in all required fields correctly.');
    
});







</script>
<script>
function preventLeadingSpace(event) {
    const input = event.target;
    if (input.value.startsWith(' ')) {
        input.value = input.value.trim(); // Remove leading space
    }
    // Replace multiple consecutive spaces with a single space
    input.value = input.value.replace(/\s{2,}/g, ' ');
}

function preventSpaces(event) {
        const input = event.target;
        if (input.value.includes(' ')) {
            input.value = input.value.replace(/\s/g, ''); // Remove all spaces
        }
    }




</script>
<!-- Existing JavaScript and closing body tag -->


</body>
</html>
