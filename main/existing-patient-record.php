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
  <title>Add Patient</title>
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
                <div class="card-header card-header-patient-form text-center">
                    <h3> <b>ADD PATIENT</b> <h3>
                </div>
                <div class="card-body px-5">
                <div class="form-container">
  <div class="step-indicator" id="step-indicator">
    <div class="step-item">
      <span class="number active">1</span>
    </div>
    <div class="line-container">
      <hr class="lines active">
    </div>
    <div class="step-item">
      <span class="number">2</span>
    </div>
    <div class="line-container">
      <hr class="lines">
    </div>
    <div class="step-item">
      <span class="number">3</span>
    </div>
  </div>
</div>

                <div class="form-container">
  <div class="step-indicator" id="step-indicator">
    <div class="step-item mr-5">
      <div class="text-indicator active">Patient Details</div>
    </div>
  
    <div class="step-item ml-2 mr-5">
      <div class="text-indicator">Bite Exposure Details</div>
    </div>

    <div class="step-item ml-1">
      <div class="text-indicator">Treatment Given</div>
    </div>
  </div>
</div>



<form id="multi-step-form" enctype="multipart/form-data">
  <div class="step active" id="step1">
    <div class="row justify-content-center mt-3 px-3 ">
    <input type="hidden" name="patientID" value="<?php echo $patientID; ?>">
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="fName">First Name<span class="red">*</span></label>
        <input type="text" id="fName" name="fName" placeholder="First Name" value="<?php echo $firstName; ?>" class="form-control" oninput="preventLeadingSpace(event)" readonly>
      </div>
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="mName">Middle Name</label>
        <input type="text" id="mName" name="mName" placeholder="Middle Name" value="<?php echo $middleName; ?> "class="form-control" oninput="preventLeadingSpace(event)"readonly >
      </div>
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="lName">Last Name<span class="red">*</span></label>
        <input type="text" id="lName" name="lName" placeholder="Last Name"class="form-control" value="<?php echo $lastName; ?>" oninput="preventLeadingSpace(event)" readonly>
      </div>
    </div>
    <div class="row justify-content-center px-3">
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="birthDate">Birth Date<span class="red">*</span></label>
        <input type="date" id="birthDate" name="birthDate" placeholder="Birth Date" class="form-control" value="<?php echo $birthDate; ?>"  max="<?php echo date('Y-m-d', strtotime('-1 year')); ?>"onkeydown="return false" readonly>

      </div>
  
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="age">Age<span class="red">*</span></label>
        <input type="tel" id="age" name="age" placeholder="Age" value="<?php echo $age; ?>" class="form-control" readonly>
      </div>
   
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="sex">Sex<span class="red">*</span></label>
        <input type="text" id="sex" name="sex" class="form-control" value="<?php echo $sex; ?>" readonly >
      </div>
    </div>
    <div class="row justify-content-center px-3 mb-0">
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="weight">Weight (kg)<span class="red">*</span></label>
        <input type="tel" id="weight" name="weight" placeholder="Weight (kg)" value="<?php echo $weight; ?>" class="form-control"   readonly>
      </div>
         <div class="col-lg-4 form-group px-4 mb-3">
                                <label for="phoneNumber">Phone Number<span class="red">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="color: white; background-color: #5E6E82; font-size: 14px;"><b>PH </b></span>
                                    </div>

                                    <input type="tel" id="phoneNumber" name="phoneNumber" class="form-control" placeholder="09123456789" style="min-width: 140px" value="<?php echo $contactNumber; ?>"  readonly>
                                   <div>

                                   </div>
                                </div>
                                <small id="phone-number-error" class="error-message"></small>
                            
                            </div>
                            
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="email">Email Address<span class="red">*</span></label>
        <input type="email" id="email" name="email" placeholder="Email Address" class="form-control" value="<?php echo $emailAddress; ?>"  readonly>
      </div>
    </div>
    <div class="row justify-content-center px-3 mb-0">
      <div class="col-lg-4 form-group px-4 mb-3">
      <label for="province">Province<span class="red">*</span></label>
      <input type="text" id="province" name="province" class="form-control" placeholder="Province" value="<?php echo $province; ?>"   readonly>

      </div>
      <div class="col-lg-4 form-group px-4 mb-3">
      <label for="city">City<span class="red">*</span></label>
      <input type="text" id="city" name="city" class="form-control" placeholder="City" value="<?php echo $city; ?>"   readonly>

      </div>
      <div class="col-lg-4 form-group px-4 mb-3">
      <label for="address">Address<span class="red">*</span></label>
        <input type="text" id="address" name="address" class="form-control" placeholder="Address" value="<?php echo $address; ?>"  readonly>
      </div>
    </div>
    <div class="row justify-content-center px-3 mb-3">
      <div class="col-lg-4 form-group px-4">
        <label for="emergencyContact">In case of Emergency, notify<span class="red">*</span></label>
        <input type="text" id="emergencyContact" name="emergencyContact" placeholder="Full Name" class="form-control" value="<?php echo $emergencyContactName; ?>"   oninput="preventLeadingSpace(event)" readonly >
      </div>
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="relationship">Relationship<span class="red">*</span></label>
        <input type="text" id="relationship" name="relationship" placeholder="Relationship" class="form-control" value="<?php echo $emergencyContactRelationship; ?>"   oninput="preventLeadingSpace(event)" readonly >
      </div>
      <div class="col-lg-4 form-group px-4 mb-3">
                                <label for="emergencyPhoneNumber">Phone Number<span class="red">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="color: white; background-color: #5E6E82; font-size: 14px;"><b>PH </b></span>
                                    </div>

                                    <input type="tel" id="emergencyPhoneNumber" name="emergencyPhoneNumber" class="form-control" placeholder="09123456789" value="<?php echo $emergencyContactNumber?> " style="min-width: 140px"  readonly>
                                   <div>

                                   </div>
                                </div>
                                <small id="emergency-phone-number-error" class="error-message"></small>
                            
                            </div>
    <div class="row justify-content-center">
    <button type="button" class="btn-customized" onclick="nextStep(1)">Next</button>
</div>
  </div>
  </div>


  <div class="step" id="step2">
    <div class="row justify-content-center mt-3">
        <div class="col-lg-5 form-group mx-auto p-0">
            <label for="exposureDate">Date of Exposure</label>
            <input type="date" id="exposureDate" name="exposureDate" class="form-control" placeholder="Date of Exposure" max="<?php echo date('Y-m-d'); ?>" required>
        </div>
        <div class="col-lg-5 form-group  mx-auto p-0">
            <label for="exposureBy">Exposure by</label>
            <select id="exposureBy" name="exposureBy" class="form-control" required>
    <option value="">Select Option</option>
    <option value="Bite">Bite</option>
    <option value="Scratch">Scratch</option>
    <option value="Saliva Contact with Open Wound">Saliva Contact with Open Wound</option>
    <option value="Saliva Contact with Mucous Membrane">Saliva Contact with Mucous Membrane</option>
</select>
        </div>
        </div>
 
    <div class="row justify-content-center mt-3">
    <div class="col-lg-3 form-group mx-auto mb-0 p-0">
    <label for="exposureType">Type of Exposure</label>
<select id="exposureType" name="exposureType" class="form-control" required>
    <option value="">Select Type of Exposure</option>
    <option value="Category I">Category I</option>
    <option value="Category II">Category II</option>
    <option value="Category III">Category III</option>
    <option value="Category IV">Category IV</option>
</select>

        </div>
        <div class="col-lg-3 form-group mx-auto mb-0 p-0">
            <label for="animalType">Type of Animal</label>
            <input type="text" id="animalType" name="animalType" placeholder="Type of Animal" class="form-control" oninput="preventLeadingSpace(event)" required>
        </div>
        <div class="col-lg-3 form-group mx-auto mb-0 p-0">
            <label for="biteLocation">Bite Location</label>
            <input type="text" id="biteLocation" name="biteLocation" placeholder="Bite Location" class="form-control" oninput="preventLeadingSpace(event)" required>
        </div>
        </div>
        <div class="row justify-content-center mt-0">
    <div class="col-lg-11 form-group patient-form mx-auto p-0">
            <label for="uploadImage">Upload Image</label>
            <input type="file" id="uploadImage" name="uploadImage" class="form-control" accept="image/jpeg, image/png">
        </div>

        </div>
        <div class="row mt-0 mx-auto justify-content-center">
    <button type="button" class="prev mr-5 btn btn-outline-custom" onclick="prevStep(2)">Previous</button>
    <button type="button" onclick="nextStep(2)" class="btn-customized">Next</button>
    </div>
</div>
<div class="step" id="step3">
<div class="step3-error-messages"></div>
  <div class="step3-container">
  <div id="medicineItems" >
    <!-- Initial medicine item -->
    <div class="row justify-content-center align-items-end mx-auto pt-4 pb-0 mb-3 medicine-item">

        <div class="col-lg-3 form-group mx-auto p-0 mb-0 pb-0">
            <label for="medicineType">Type of Medicine</label>
            <select name="medicineType[]" class="form-control medicineType" required>
                <option value="">Select Type of Medicine</option>
                <?php
                // Assuming you have a connection to your database
                // Fetch data from the "medicine" table
                $sql = "SELECT * FROM medicine";
                $result = mysqli_query($conn, $sql);
                
                // Loop through the results and generate options for the dropdown
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['MedicineID'] . "'>" . $row['MedicineName'] . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-lg-3 form-group mx-auto p-0 mb-0 pb-0">
            <label for="medicineGiven">Medicine Given</label>
            <select name="medicineGiven[]" class="form-control medicineGiven" required>
            </select>
            <span class="total-quantity" style="color: gray; position: absolute;
    top: 20;
    right: 0; "></span>
        </div>

        <div class="col-lg-1 form-group mx-auto p-0 mb-0 pb-0">
            <label for="dosageQuantity">Dosage</label>
            <input type="number" name="dosageQuantity[]" class="form-control" placeholder="Dosage Quantity" required>
        </div>
        
        <div class="col-lg-1 form-group mx-auto p-0 mb-0 pb-0">
    <label for="route">Route</label>
    <input type="text" name="route[]" placeholder="Route" class="form-control route" readonly>
</div>

        
        <div class="col-lg-1 form-group mx-auto p-0 mb-0 pb-0">
            <label for="quantity">Quantity</label>
            <input type="number" name="quantity[]" class="form-control" placeholder="Quantity" required>
        </div>
        
        <div class="col-lg-1 mx-auto form-group mb-0 pb-0 mt-auto">
            <div class="d-flex justify-content-center">
                <button class="btn btn-add btn-success align-self-end mr-3 addMedicineItem">+</button>
                <button class="btn btn-add btn-danger align-self-end removeMedicineItem">-</button>
            </div>
        </div>
    </div>
</div>

<div id="equipmentItems">
    <!-- Initial equipment item row -->
    <div class="row mx-auto justify-content-center align-items-end mt-3 equipment-item">
        <div class="col-lg-7 form-group mx-auto mb-0 pb-0 pl-0 mr-3">
            <label for="equipmentType">Type of Equipment</label>
            <select name="equipmentType[]" class="form-control equipmentType" required>
                <option value="">Select Type of Equipment</option>
                <?php
                // Assuming you have a connection to your database
                // Fetch data from the "equipment" table
                $sql = "SELECT * FROM equipment";
                $result = mysqli_query($conn, $sql);

                // Loop through the results and generate options for the dropdown
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['EquipmentID'] . "'>" . $row['Name'] . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-lg-3 form-group mx-auto p-0 mb-0 pb-0">
            <label for="equipmentAmount">Equipment Amount</label>
            <input type="number" name="equipmentAmount[]" class="form-control equipmentAmount" placeholder="Equipment Amount" required>
        </div>
        <div class="col-lg-1 mx-auto form-group mb-0 pb-0 mt-auto">
            <div class="d-flex justify-content-center">
                <button class="btn btn-add btn-success align-self-end mr-3 addEquipmentItem">+</button>
                <button class="btn btn-add btn-danger align-self-end removeEquipmentItem">-</button>
            </div>
        </div>
    </div>
</div>





    <div class="row justify-content-center mx-auto pt-4 pb-0 mb-0">
    <div class="col-lg-4 form-group mx-auto p-0 mb-0 pb-0">
    <label for="treatmentCategory">Treatment Category</label>
    <select id="treatmentCategory" name="treatmentCategory" class="form-control" required>
        <option value="">Select Treatment Category</option>
        <option value="pre-exposure">Pre Exposure</option>
        <option value="post-exposure">Post Exposure</option>
    </select>
</div>

        <div class="col-lg-4 form-group mx-auto p-0 mb-0 pb-0">
    <label for="sessions">Sessions</label>
    <select id="sessions" name="sessions" class="form-control" required>
    <option value="">Select Session</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
    </select>
</div>

        <div class="col-lg-3 form-group mx-auto p-0 mb-0 pb-0">
    <label for="treatmentDate">Date of Treatment</label>
    <input type="date" id="treatmentDate" name="treatmentDate" value="<?php echo date('Y-m-d'); ?>" placeholder="Date of Treatment" class="form-control" required readonly>
</div>

    </div>

    <div class="row align-items-end justify-content-center mx-auto pt-4 pb-0 mb-0 ml-1">
    <div class="col-lg-12 form-group mx-auto p-0 mb-0 pb-0">
        <label for="doctorRemarks">Doctor Remarks</label>
        <textarea id="doctorRemarks" name="doctorRemarks" placeholder="Doctor Remarks" class="form-control w-100"></textarea>
    </div>
</div>

    
    <div class="row justify-content-center mt-5">
        <button type="button" class="prev mr-5 btn btn-outline-custom" onclick="prevStep(3)">Previous</button>
        <button type="button" class="btn-customized" id="submit-button">Submit</button>
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




document.addEventListener("DOMContentLoaded", function() {
    var animalTypeInput = document.getElementById("animalType");
    var biteLocationInput = document.getElementById("biteLocation");

    // Function to validate input and allow only letters and spaces
    function validateInput(inputField) {
        inputField.addEventListener("keypress", function(event) {
            var keyCode = event.keyCode;
            // Allow letters and spaces (ASCII codes: 65-90, 97-122, 32)
            if ((keyCode >= 65 && keyCode <= 90) || (keyCode >= 97 && keyCode <= 122) || keyCode === 32) {
                return true;
            } else {
                event.preventDefault();
                return false;
            }
        });
    }

    // Apply input validation to animalType and biteLocation inputs
    validateInput(animalTypeInput);
    validateInput(biteLocationInput);
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



  const inputFields = document.querySelectorAll('input, select');
inputFields.forEach(function(field) {
  field.addEventListener('input', function() {
    if (field.checkValidity()) {
      removeError(field);
    }
  });
});
let currentStep = 1;
const stepIndicator = document.querySelectorAll('.step-indicator span');
const formSteps = document.querySelectorAll('.step');
const lines = document.querySelectorAll('.lines');
const textindicator = document.querySelectorAll('.text-indicator');
// Function to validate required fields in the current step
function validateStep(step) {
  const fields = formSteps[step -1].querySelectorAll('input[required], select[required], textarea[required]');
  for (let i = 0; i < fields.length; i++) {
    if (!fields[i].value) {
      alert('Please fill in all required fields before proceeding.');
      return false; // Validation failed
    }
  }
  return true; // Validation passed
}



// Modify nextStep function to include validation
function nextStep(step) {
  let isValid = true;

  // Check for empty required fields in the current step
  const currentStepFields = formSteps[currentStep - 1].querySelectorAll('input[required], select[required]');
  currentStepFields.forEach(function(field) {
    if (!field.value.trim()) {
      showError(field, 'This field is required.');
      isValid = false;
    } else {
      removeError(field);
    }
  });

  // If there are empty required fields, stop and show error messages
  if (!isValid) {
    return;
  }

  // Validate specific fields based on the current step
  if (currentStep === 1) {
    validateBirthDateField();
    validateAgeField();
    validateWeightField();

  }

  // If there are validation errors in the current step, stop and show error messages
  if (formSteps[currentStep - 1].querySelector('.error')) {
    return;
  }

  // Proceed to the next step if all fields are valid
  if (step < formSteps.length) {
    formSteps[currentStep - 1].classList.remove('active');
    stepIndicator[currentStep - 1].classList.remove('active');
    textindicator[currentStep - 1].classList.remove('active');

    currentStep++;
    formSteps[currentStep - 1].classList.add('active');
    stepIndicator[currentStep - 1].classList.add('active');
    textindicator[currentStep - 1].classList.add('active');

    if (currentStep <= lines.length) {
      lines[currentStep - 1].classList.add('active');
    }
  }
}



// Modify prevStep function to exclude validation
function prevStep(step) {
  if (step > 1) {
    // Proceed to the previous step
    formSteps[currentStep - 1].classList.remove('active');
    stepIndicator[currentStep - 1].classList.remove('active');
    textindicator[currentStep - 1].classList.remove('active');
    currentStep--;
    formSteps[currentStep - 1].classList.add('active');
    stepIndicator[currentStep - 1].classList.add('active');
    textindicator[currentStep - 1].classList.add('active');
    if (currentStep <= lines.length) {
      lines[currentStep - 1].classList.add('active');
    }
  }
}
function showErrorBorder(field) {
  field.classList.add('error-border');
}

// Function to remove error border
function removeErrorBorder(field) {
  field.classList.remove('error-border');
}
// Function to validate Step 3 fields
function validateStep3Fields() {
    // Remove previous error messages and borders
    const errorContainer = document.querySelector('.step3-error-messages');
    errorContainer.innerHTML = '';

    const inputFields = document.querySelectorAll('.step3-container input, .step3-container select, .step3-container textarea');
    inputFields.forEach(function(field) {
        field.classList.remove('error-border');
    });

    // Check if all required input fields have a value
    const requiredFields = document.querySelectorAll('.step3-container input[required], .step3-container select[required], .step3-container textarea[required]');
    const missingFields = [];

    requiredFields.forEach(function(field) {
        if (!field.value.trim()) {
            missingFields.push(field);
        }
    });

    // Display error messages and add error borders
    if (missingFields.length > 0) {
        const errorMessage = document.createElement('div');
        errorMessage.classList.add('error-message');
        errorMessage.textContent = 'Incorrect field(s): ' + missingFields.map(field => field.name).join(', ');
        errorContainer.appendChild(errorMessage);
        
        // Add error border to missing fields
        missingFields.forEach(function(field) {
            field.classList.add('error-border');
        });
    }
}


// Add event listener to submit button
document.getElementById('submit-button').addEventListener('click', function(e) {
    e.preventDefault();

    // Validate Step 3 fields
    validateStep3Fields();

    // Check if there are still errors
    const errorMessages = document.querySelectorAll('.step3-error-messages .error-message');
    if (errorMessages.length === 0) {
        // If no errors, proceed with form submission using AJAX
        const formData = new FormData(document.getElementById('multi-step-form'));

        $.ajax({
            url: 'backend/submit-existing.php', // Replace 'submit.php' with your actual form submission endpoint
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
    } else {
        // If there are errors, prevent form submission
        console.log('Please fill in all required fields correctly.');
    }
});






  // Function to validate birth date
  function validateBirthDate(birthDate) {
    const currentDate = new Date();
    const selectedDate = new Date(birthDate);
    const oneYearAgo = new Date(currentDate.getFullYear() - 1, currentDate.getMonth(), currentDate.getDate());

    return selectedDate <= oneYearAgo;
  }

  // Function to validate age
  function validateAge(age) {
    return age >= 1 && age <= 123;
  }

  // Function to validate phone number format
  function validatePhoneNumber(phoneNumber) {
    const phoneNumberRegex = /^09\d{9}$/;
    return phoneNumberRegex.test(phoneNumber);
  }
  function validateEmergencyPhoneNumber(emergencyPhoneNumber) {
    const phoneNumberRegex = /^09\d{9}$/;
    return phoneNumberRegex.test(emergencyPhoneNumber);
  }

  // Function to validate weight
  function validateWeight(weight) {
    return weight >= 0 && weight <= 650;
  }

  function validateEmail(email) {
    const emailRegex = /^[a-zA-Z0-9._%+-]+@(gmail|yahoo|outlook)\.com$/;
    return emailRegex.test(email);
}





// Get the email input element

  // Function to display error message and apply red border
// Function to display error message and apply red border
function showError(field, message) {
  if (field.id === 'phoneNumber' || field.id === 'emergencyPhoneNumber') {
    const errorElementId = field.id === 'phoneNumber' ? 'phone-number-error' : 'emergency-phone-number-error';
    const errorElement = document.getElementById(errorElementId);
    if (errorElement) {
      errorElement.textContent = message;
    }
    field.classList.add('error-border');
  } else {
    const errorContainer = field.nextElementSibling;
    if (!errorContainer || !errorContainer.classList.contains('error-message')) {
      field.classList.add('error-border');
      const errorMessage = document.createElement('div');
      errorMessage.classList.add('error-message');
      errorMessage.textContent = message;
      field.parentNode.insertBefore(errorMessage, field.nextElementSibling);
    } else {
      errorContainer.textContent = message;
    }
    field.classList.add('error');
  }
}




// Function to remove error message and red border
function removeError(field) {
  if (field.id === 'phoneNumber') {
    const errorContainer = document.getElementById('phone-number-error');
    if (errorContainer) {
      errorContainer.textContent = '';
    }
  } else if (field.id === 'emergencyPhoneNumber') {
    const errorContainer = document.getElementById('emergency-phone-number-error');
    if (errorContainer) {
      errorContainer.textContent = '';
    }
  } else {
    const errorContainer = field.nextElementSibling;
    if (errorContainer && errorContainer.classList.contains('error-message')) {
      field.parentNode.removeChild(errorContainer);
    }
  }
  field.classList.remove('error');
  field.classList.remove('error-border');
}

  // Function to validate birth date field
  function validateBirthDateField() {
    const birthDate = document.getElementById('birthDate').value;
    const isValid = validateBirthDate(birthDate);
    if (!isValid) {
      showError(document.getElementById('birthDate'), 'Birth date should be at least one year earlier than the current date.');
    } else {
      removeError(document.getElementById('birthDate'));
    }
  }

  // Function to validate age field
  function validateAgeField() {
    const age = parseInt(document.getElementById('age').value, 10);
    const isValid = validateAge(age);
    if (!isValid) {
      showError(document.getElementById('age'), 'Age should be between 1 and 123.');
    } else {
      removeError(document.getElementById('age'));
    }
  }



  // Function to validate weight field
  function validateWeightField() {
    const weight = parseFloat(document.getElementById('weight').value);
    const isValid = validateWeight(weight);
    if (!isValid) {
      showError(document.getElementById('weight'), 'Weight should be between 0 and 650 kg.');
    } else {
      removeError(document.getElementById('weight'));
    }
  }

  // Function to validate email field



  // Add event listeners to input fields to validate them as the user types
  document.getElementById('birthDate').addEventListener('input', validateBirthDateField);
  document.getElementById('age').addEventListener('input', validateAgeField);


  document.getElementById('weight').addEventListener('input', validateWeightField);

  $(document).ready(function() {
    // Function to fetch brands and quantity based on selected medicine type
    function fetchBrandsAndQuantity($medicineTypeDropdown, $medicineGivenDropdown, $totalQuantityContainer) {
        var medicineId = $medicineTypeDropdown.val();
        console.log("Sending AJAX request with medicineType:", medicineId);
        $.ajax({
            url: 'backend/fetch-brands.php', // Path to your PHP script to fetch brands
            method: 'POST',
            data: { medicineType: medicineId },
            dataType: 'json',
            success: function(response) {
                $medicineGivenDropdown.empty(); // Clear previous options
                $medicineGivenDropdown.append('<option value="">Select Brand</option>'); // Add the default option
                $.each(response, function(index, value) {
                    // Check if the option is already selected in other fields
                    var isDuplicate = $medicineGivenDropdown.find('option[value="' + value.MedicineBrandID + '"]').length > 0;
                    if (!isDuplicate) {
                        $medicineGivenDropdown.append('<option value="' + value.MedicineBrandID + '">' + value.BrandName + '</option>');
                    }
                   
                });
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                console.log(`Response Text: ${xhr.responseText}`);
                // Handle errors
            }
        });

        // Event listener for the change event on medicine given dropdown
        $medicineGivenDropdown.on('change', function() {
          var $routeInput = $(this).closest('.medicine-item').find('.route');
            var selectedBrandID = $(this).val();
            fetchQuantity(selectedBrandID, $totalQuantityContainer, $routeInput);
        });
    }

    // Function to fetch quantity based on selected brand
    function fetchQuantity(medicineBrandID, $totalQuantityContainer, $routeInput) {
        $.ajax({
            url: 'backend/fetch-quantity.php', // Path to your PHP script to fetch quantity
            method: 'POST',
            data: { medicineBrandID: medicineBrandID },
            dataType: 'json',
            success: function(response) {
                // Update the UI to display the fetched total quantity
                $totalQuantityContainer.text('Total Quantity: ' + response.TotalQuantity);
                $routeInput.val(response.Route);
                // Validate the quantity input
                validateQuantity($totalQuantityContainer);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                console.log(`Response Text: ${xhr.responseText}`);
                // Handle errors
            }
        });
    }

   // Function to validate quantity input for a specific medicine item
function validateQuantity($inputField, totalQuantity) {
    var enteredQuantity = parseInt($inputField.val());
    if (isNaN(enteredQuantity) || enteredQuantity < 0 || enteredQuantity > totalQuantity) {
        $inputField.addClass('is-invalid');
        $inputField.closest('.form-group').find('.invalid-feedback').text('Quantity must be between 0 and ' + totalQuantity);
    } else {
        $inputField.removeClass('is-invalid');
        $inputField.closest('.form-group').find('.invalid-feedback').text('');
    }
}

// Event listener for the change event on medicine type dropdown
$(document).on('change', '.medicineType', function() {
    var $medicineItem = $(this).closest('.medicine-item');
    var $medicineGivenDropdown = $medicineItem.find('.medicineGiven');
    var $totalQuantityContainer = $medicineItem.find('.total-quantity');
    fetchBrandsAndQuantity($(this), $medicineGivenDropdown, $totalQuantityContainer);
});

// Event listener for the input event on quantity input
$(document).on('input', 'input[name="quantity[]"]', function() {
    var $inputField = $(this);
    var $medicineItem = $inputField.closest('.medicine-item');
    var $totalQuantityContainer = $medicineItem.find('.total-quantity');
    var totalQuantity = parseInt($totalQuantityContainer.text().replace('Total Quantity: ', ''));
    validateQuantity($inputField, totalQuantity);
});

// Function to apply input validation to a specific medicine item
function applyInputValidation($medicineItem) {
    $medicineItem.find('input[name="quantity[]"]').each(function() {
        var $inputField = $(this);
        var $totalQuantityContainer = $medicineItem.find('.total-quantity');
        var totalQuantity = parseInt($totalQuantityContainer.text().replace('Total Quantity: ', ''));
        validateQuantity($inputField, totalQuantity);
    });
}

var totalQuantityCounter = 1;

// Function to add a new medicine item
function addMedicineItem() {
    var $newMedicineItem = $('#medicineItems .medicine-item').first().clone();
    $newMedicineItem.find('input').val('');

    // Clear the total quantity text of the cloned item
    $newMedicineItem.find('.total-quantity').text('Total Quantity: ');

    var uniqueIdentifier = 'total-quantity-' + totalQuantityCounter;
    $newMedicineItem.find('.total-quantity').attr('id', uniqueIdentifier);
    totalQuantityCounter++;

    $('#medicineItems').append($newMedicineItem);
    $newMedicineItem.find('.medicineType').trigger('change');
    applyInputValidation($newMedicineItem);
}



// Function to add a new equipment item
function addEquipmentItem() {
    // Clone the template of the equipment item
    var $newEquipmentItem = $('#equipmentItems .equipment-item').first().clone();

    // Clear the values of input fields in the cloned item
    $newEquipmentItem.find('input').val('');

    // Append the cloned item to the container
    $('#equipmentItems').append($newEquipmentItem);

    // Reapply the event listeners for input validation on the cloned item
    applyInputValidation($newEquipmentItem);
}

// Function to apply input validation event listeners
function applyInputValidation($element) {
    // Event listener for the "keydown" event on dosage quantity input
    $element.find('input[name="dosageQuantity[]"]').on('keydown', function(event) {
        const key = event.key;
        // Allow only numbers and dot
        if (!/[0-9.]/.test(key) && key !== 'Backspace' && key !== 'Delete' && key !== 'ArrowLeft' && key !== 'ArrowRight') {
            event.preventDefault();
        }
    });

    // Event listener for the "keydown" event on quantity input
    $element.find('input[name="quantity[]"]').on('keydown', function(event) {
        const key = event.key;
        // Allow only numbers
        if (!/[0-9]/.test(key) && key !== 'Backspace' && key !== 'Delete' && key !== 'ArrowLeft' && key !== 'ArrowRight') {
            event.preventDefault();
        }
    });

    // Event listener for the "keydown" event on equipment amount input
    $element.find('input[name="equipmentAmount[]"]').on('keydown', function(event) {
        const key = event.key;
        // Allow only numbers
        if (!/[0-9]/.test(key) && key !== 'Backspace' && key !== 'Delete' && key !== 'ArrowLeft' && key !== 'ArrowRight') {
            event.preventDefault();
        }
    });
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
}

// Event listener for the "Add Medicine" button
$(document).on('click', '.addMedicineItem', function() {
    addMedicineItem();
});

// Event listener for the "Add Equipment" button
$(document).on('click', '.addEquipmentItem', function() {
    addEquipmentItem();
});

// Event listener for the "Remove Equipment" button
$(document).on('click', '.removeEquipmentItem', function() {
    // Check if there's more than one equipment item
    if ($('.equipment-item').length > 1) {
        // Remove the clicked equipment item
        $(this).closest('.equipment-item').remove();
    } else {
        // Alert the user or perform any other action indicating that there must be at least one item
        alert("At least one equipment item is required.");
    }
});
$(document).on('click', '.removeMedicineItem', function() {
    // Check if there's more than one medicine item
    if ($('.medicine-item').length > 1) {
        // Remove the clicked medicine item
        $(this).closest('.medicine-item').remove();
    } else {
        // Alert the user or perform any other action indicating that there must be at least one item
        alert("At least one medicine item is required.");
    }
});
});

// Get the dosage quantity input element
const dosageQuantityInput = document.querySelector('input[name="dosageQuantity[]"]');

// Add an event listener for the keydown event
dosageQuantityInput.addEventListener('keydown', function(event) {
    const keyCode = event.keyCode;

    // Allow numbers, dot, backspace, delete, arrow keys, and decimal point (if not already present)
    if (!((keyCode >= 48 && keyCode <= 57) || (keyCode >= 96 && keyCode <= 105) || keyCode === 190 || keyCode === 110 || keyCode === 8 || keyCode === 46 || (keyCode >= 37 && keyCode <= 40))) {
        // Prevent "@" symbol
        if (event.key === '@') {
            event.preventDefault();
        }
        else {
            event.preventDefault();
        }
    }

    // Prevent entering more than one dot
    const currentValue = event.target.value;
    if ((keyCode === 190 || keyCode === 110) && currentValue.includes('.')) {
        event.preventDefault();
    }
});


// Get the quantity input element
const quantityInput = document.querySelector('input[name="quantity[]"]');

// Add an event listener for the keydown event
quantityInput.addEventListener('keydown', function(event) {
    const keyCode = event.keyCode;

    // Allow only numbers, backspace, and delete
    if (!((keyCode >= 48 && keyCode <= 57) || (keyCode >= 96 && keyCode <= 105) || keyCode === 8 || keyCode === 46)) {
        event.preventDefault();
    }
});

// Get the equipment amount input element
const equipmentAmountInput = document.querySelector('input[name="equipmentAmount[]"]');

// Add an event listener for the keydown event
equipmentAmountInput.addEventListener('keydown', function(event) {
    const keyCode = event.keyCode;

    // Allow only numbers, backspace, and delete
    if (!((keyCode >= 48 && keyCode <= 57) || (keyCode >= 96 && keyCode <= 105) || keyCode === 8 || keyCode === 46)) {
        event.preventDefault();
    }
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
