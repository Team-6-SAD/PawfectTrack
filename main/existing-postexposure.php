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
      $lastName1 = $row['LastName'];
      $firstName1 = $row['FirstName'];
      $middleName1 = $row['MiddleName'];
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
      $EmergencyContactRelationship = $row['EmergencyContactRelationship'];
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
<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
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
        <div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999; position:fixed;"></div>

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
      <div class="text-indicator">Exposure Details</div>
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
        <input type="text" id="fName" name="fName" placeholder="First Name" value="<?php echo $firstName1; ?>" class="form-control" oninput="preventLeadingSpace(event)" readonly>
      </div>
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="mName">Middle Name</label>
        <input type="text" id="mName" name="mName" placeholder="Middle Name" value="<?php echo $middleName1; ?> "class="form-control" oninput="preventLeadingSpace(event)"readonly >
      </div>
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="lName">Last Name<span class="red">*</span></label>
        <input type="text" id="lName" name="lName" placeholder="Last Name"class="form-control" value="<?php echo $lastName1; ?>" oninput="preventLeadingSpace(event)" readonly>
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
        <input type="text" id="relationship" name="relationship" placeholder="Relationship" class="form-control" value="<?php echo $EmergencyContactRelationship; ?>"   oninput="preventLeadingSpace(event)" readonly >
      </div>
      <div class="col-lg-4 form-group px-4 mb-3">
                                <label for="emergencyPhoneNumber">Phone Number<span class="red">*</span></label>
                                <div class="input-group">

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
            <input type="date" id="exposureDate" name="exposureDate" class="form-control" placeholder="Date of Exposure" onkeydown="return false" max="<?php echo date('Y-m-d'); ?>" >
        </div>
        <div class="col-lg-5 form-group  mx-auto p-0">
            <label for="exposureBy">Exposure by</label>
            <select id="exposureBy" name="exposureBy" class="form-control" >
    <option value="" disabled selected>Select Option</option>
    <option value="Bite">Bite</option>
    <option value="Scratch">Scratch</option>
    <option value="Saliva Contact with Open Wound">Saliva Contact with Open Wound</option>
    <option value="Saliva Contact with Mucous Membrane">Saliva Contact with Mucous Membrane</option>
</select>
        </div>
        </div>
 
    <div class="row justify-content-center mt-3 mb-4">
    <div class="col-lg-3 form-group mx-auto mb-0 p-0">
    <label for="exposureType">Type of Exposure</label>
<select id="exposureType" name="exposureType" class="form-control" >
    <option value="" disabled selected>Select Type of Exposure</option>
    <option value="Category I">Category I</option>
    <option value="Category II">Category II</option>
    <option value="Category III">Category III</option>
    <option value="Category IV">Category IV</option>
</select>

        </div>
        <div class="col-lg-3 form-group mx-auto mb-0 p-0">
    <label for="animalType">Type of Animal</label>
    <select id="animalType" name="animalType" class="form-control" onchange="checkOtherOption()">
        <option value="" disabled selected>Select Animal Type</option>
        <option value="Bat">Bat</option>
        <option value="Dog">Dog</option>
        <option value="Cat">Cat</option>
        <option value="Cow">Cow</option>
        <option value="Other">Other</option>
    </select>
    <input type="text" id="otherAnimalType" name="otherAnimalType" placeholder="Type of Animal" class="form-control mt-2 d-none" maxlength="50">
</div>
        <div class="col-lg-3 form-group mx-auto mb-0 p-0">
            <label for="biteLocation">Exposed Location</label>
            <input type="text" id="biteLocation" name="biteLocation" placeholder="Bite Location" class="form-control" oninput="preventLeadingSpace(event)"  maxlength="50">
        </div>
        </div>
        <div class="row justify-content-center mt-0">
    <div class="col-lg-11 form-group patient-form mx-auto p-0 mb-2">
            <label for="uploadImages">Upload Image of Exposure</label>
            <input type="file" id="uploadImages" name="uploadImages[]" class="form-control" accept="image/jpeg, image/png" multiple>
        </div>

        </div>
        <div class="row my-3 mx-auto justify-content-center">
    <button type="button" class="prev mr-5 btn btn-outline-custom py-0" style="border-radius: 27.5px !important; font-size:15px;" onclick="prevStep(2)">Previous</button>
    <button type="button" onclick="nextStep(2)" style="border-radius: 27.5px !important; font-size:15px;" class="btn-customized">Next</button>
    </div>
</div>
<div class="step" id="step3">
<div class="step3-error-messages"></div>
  <div class="step3-container">
  <div id="medicineItems" >
    <!-- Initial medicine item -->
    <div class="row justify-content-center align-items-end mx-auto pt-4 pb-0 mb-3 medicine-item">

        <div class="col-lg-3 form-group mx-auto p-0 mb-0 pb-0">
            <label for="medicineType">Type of Medicine<span class="red">*</span></label>
            <select name="medicineType[]" class="form-control medicineType" required>
                <option value="" disabled selected>Select Type of Medicine</option>
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
            <label for="medicineGiven">Medicine Given<span class="red">*</span></label>
            <select name="medicineGiven[]" class="form-control medicineGiven" required>
               <option value="" disabled selected>Select Brand</option>
            </select>
            <span class="total-quantity" style="color: gray; position: absolute;
    top: 20;
    right: 0; "></span>
        </div>

        <div class="col-lg-1 form-group mx-auto p-0 mb-0 pb-0">
            <label for="dosageQuantity">Dosage<span class="red">*</span></label>
            <input type="number" name="dosageQuantity[]" class="form-control" placeholder="mL" max="9999" oninput="validateLength(this, 4)" required>
        </div>
        
        <div class="col-lg-1 form-group mx-auto p-0 mb-0 pb-0">
    <label for="route">Route<span class="red">*</span></label>
    <input type="text" name="route[]" placeholder="Route" class="form-control route" readonly>
</div>

        
        <div class="col-lg-1 form-group mx-auto p-0 mb-0 pb-0">
            <label for="quantity">Quantity<span class="red">*</span></label>
            <input type="number" name="quantity[]" class="form-control" placeholder="vl" oninput="validateLength(this, 4)" required>
        </div>
        
        <div class="col-lg-1 mx-auto form-group mb-0 pb-0 mt-auto">
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-add btn-success align-self-end mr-3 addMedicineItem">+</button>
                <button type="button" class="btn btn-add btn-danger align-self-end removeMedicineItem">-</button>
            </div>
        </div>
    </div>
</div>

<div id="equipmentItems">
    <!-- Initial equipment item row -->
    <div class="row mx-auto justify-content-center align-items-end mt-3 equipment-item">
    <div class="col-lg-7 form-group mx-auto mb-0 pb-0 pl-0 mr-3">
        <label for="equipmentType">Type of Equipment<span class="red">*</span></label>
        <select name="equipmentType[]" class="form-control equipmentType" required>
            <option value="" disabled selected>Select Type of Equipment</option>
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
        <label for="equipmentAmount">Equipment Quantity<span class="red">*</span></label>
        <input type="number" name="equipmentAmount[]" class="form-control equipmentAmount" placeholder="Equipment Quantity(pcs)" oninput="validateLength(this, 4)" required>
        <div class="invalid-feedback"></div>
    </div>
    <div class="col-lg-1 mx-auto form-group mb-0 pb-0 mt-auto">
        <div class="d-flex justify-content-center">
            <button type="button" class="btn btn-add btn-success align-self-end mr-3 addEquipmentItem">+</button>
            <button type="button" class="btn btn-add btn-danger align-self-end removeEquipmentItem">-</button>
        </div>
    </div>
</div>
</div>






    <div class="row justify-content-center mx-auto pt-4 pb-0 mb-0">
    <div class="col-lg-4 form-group mx-auto p-0 mb-0 pb-0">
    <label for="treatmentCategory">Treatment Category<span class="red">*</span></label>
    <select id="treatmentCategory" name="treatmentCategory" class="form-control" required readonly>
        <option value="Post-exposure" selected>Post Exposure</option>
    </select>
</div>

        <div class="col-lg-4 form-group mx-auto p-0 mb-0 pb-0">
    <label for="sessions">Sessions<span class="red">*</span></label>
    <select id="sessions" name="sessions" class="form-control" required>
    <option value="" disabled selected>Select Session</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
    </select>
</div>

        <div class="col-lg-3 form-group mx-auto p-0 mb-0 pb-0">
    <label for="treatmentDate">Date of Treatment<span class="red">*</span></label>
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
        <button type="button" class="prev mr-5 btn btn-outline-custom" style="border-radius: 27.5px !important; font-size:15px;" onclick="prevStep(3)">Previous</button>
        <button type="button" class="btn-customized" style="border-radius: 27.5px !important; font-size:15px;"  id="submit-button">Submit</button>
    </div>
</div>
</div>

  </form>
</div>
</div>
</div>  
</div>
</div>

<!-- Modal -->
<div class="modal fade" id="responseModal" tabindex="-1" role="dialog" aria-labelledby="responseModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <!-- Modal Header -->
                                <div class="modal-header">
                                    <h4 class="modal-title p-3"></h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <!-- Modal body -->
                                <div class="modal-body justify-content-center align-items-center d-flex" style="flex-direction:column;">
									<img src="img/img-alerts/caution-mark.png" style="height:50px; width:50px;">
                                                                  <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>INVALID IMAGE</b></h2>
                                <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>FORMAT</b></h2>
                                <div class="text-center">
                                    <small style="letter-spacing: -1px; color:#5e6e82;">Only JPG, JPEG, and PNG can be accepted.<br></small>

                                </div>
                               
                                </div>
                                <!-- Modal footer -->
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

  <!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title p-3" id="reviewModalLabel"></h5>
  
        </button>
      </div>
      <div class="modal-body d-flex flex-column pl-5 pr-4 justify-content-center" id="reviewModalBody">
        <!-- Review content will be dynamically inserted here -->
      </div>
      <div class="modal-footer justify-content-center d-flex" style="border-top:none !important;">
        <button type="button" class="btn gray px-4" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary px-4" style="border-radius:27.5px !important" data-dismiss="modal" id="submitFinal">Submit</button>
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
      <script src="js/notifications.js"> </script>

  <script>
// Function to populate the modal with form data
function getFormData() {
  // Assuming you have a form with id 'multi-step-form' and need to structure data accordingly
  var formData = new FormData(document.getElementById('multi-step-form'));

  // Structure your form data into an object as per your application's needs
  var structuredData = {
    personalInformation: {
      fName: formData.get('fName'),
      mName: formData.get('mName'),
      lName: formData.get('lName'),
      birthDate: formData.get('birthDate'),
      age: formData.get('age'),
      sex: formData.get('sex'),
      weight: formData.get('weight'),
      phoneNumber: formData.get('phoneNumber'),
      email: formData.get('email'),
      address: formData.get('address'),
      province: formData.get('province'),
      city: formData.get('city')
    },
    emergencyContact: {
      emergencyContact: formData.get('emergencyContact'),
      emergency_contact_relationship: formData.get('emergency_contact_relationship'),
      emergencyPhoneNumber: formData.get('emergencyPhoneNumber')
    },
    exposureInformation: {
      exposureDate: formData.get('exposureDate'),
      exposureBy: formData.get('exposureBy'),
      exposureType: formData.get('exposureType'),
      animalType: formData.get('animalType'),
      biteLocation: formData.get('biteLocation'),
      uploadImage: formData.get('uploadImages')
    },
    medicalInformation: {
      medicines: [],
      equipment: []
    },
    treatmentInformation: {
      treatmentCategory: formData.get('treatmentCategory'),
      sessions: formData.get('sessions'),
      treatmentDate: formData.get('treatmentDate'),
      doctorRemarks: formData.get('doctorRemarks')
    }
  };

  // Handle medicines array
  var medicineCount = formData.getAll('medicineType[]').length;
  for (let i = 0; i < medicineCount; i++) {
    var selectedValue = formData.getAll('medicineType[]')[i];
    var medicineTypeElement = document.querySelector(`.medicineType option[value="${selectedValue}"]`);
    var medicineTypeText = medicineTypeElement.textContent;
    var selectedMedicineGivenValue = formData.getAll('medicineGiven[]')[i];
    var selectedMedicineGivenElement = document.querySelector(`.medicineGiven option[value="${selectedMedicineGivenValue}"]`);
    var medicineGivenText = selectedMedicineGivenElement.textContent;

    structuredData.medicalInformation.medicines.push({
      medicineType: medicineTypeText,
      medicineGiven: medicineGivenText,
      dosageQuantity: formData.getAll('dosageQuantity[]')[i],
      route: formData.getAll('route[]')[i],
      quantity: formData.getAll('quantity[]')[i]
    });
  }

  // Handle equipment array
  var equipmentCount = formData.getAll('equipmentType[]').length;
  for (let i = 0; i < equipmentCount; i++) {
    var selectedEquipmentTypeValue = formData.getAll('equipmentType[]')[i];
    var selectedEquipmentTypeElement = document.querySelector(`.equipmentType option[value="${selectedEquipmentTypeValue}"]`);
    var equipmentTypeText = selectedEquipmentTypeElement.textContent;
    structuredData.medicalInformation.equipment.push({
      equipmentType: equipmentTypeText,
      equipmentAmount: formData.getAll('equipmentAmount[]')[i]
    });
  }

  return structuredData;
}

function populateReviewModal() {
  var modalBody = document.getElementById('reviewModalBody');
  var formData = getFormData(); // Get your structured form data here

  var reviewContent = '';

  reviewContent += '<div class="section"><h5 class="text-center font-weight-bold gray">Personal Information</h5> <small>';
  reviewContent += '<ul class="gray">';
  reviewContent += `<li><strong>Name:</strong> ${formData.personalInformation.fName} ${formData.personalInformation.mName} ${formData.personalInformation.lName}</li>`;
  reviewContent += `<li><strong>Birth Date:</strong> ${formData.personalInformation.birthDate}</li>`;
  reviewContent += `<li><strong>Age:</strong> ${formData.personalInformation.age} years</li>`;
  reviewContent += `<li><strong>Sex:</strong> ${formData.personalInformation.sex}</li>`;
  reviewContent += `<li><strong>Weight:</strong> ${formData.personalInformation.weight} kg</li>`;
  reviewContent += `<li><strong>Phone Number:</strong> ${formData.personalInformation.phoneNumber}</li>`;
  reviewContent += `<li><strong>Email:</strong> ${formData.personalInformation.email}</li>`;
  reviewContent += `<li><strong>Address:</strong> ${formData.personalInformation.address}</li>`;
  reviewContent += `<li><strong>Name:</strong> ${formData.emergencyContact.emergencyContact}</li>`;
  reviewContent += `<li><strong>Relationship:</strong> ${formData.emergencyContact.emergency_contact_relationship}</li>`;
  reviewContent += `<li><strong>Phone Number:</strong> ${formData.emergencyContact.emergencyPhoneNumber}</li>`;
  reviewContent += '</ul>';

  reviewContent += '<div class="section" ><h5 class="text-center font-weight-bold gray">Exposure Details</h5>';
  reviewContent += '<ul class="gray">';
  reviewContent += `<li><strong>Date of Exposure:</strong> ${formData.exposureInformation.exposureDate}</li>`;
  reviewContent += `<li><strong>Exposure by:</strong> ${formData.exposureInformation.exposureBy}</li>`;
  reviewContent += `<li><strong>Exposure Type:</strong> ${formData.exposureInformation.exposureType}</li>`;
  reviewContent += `<li><strong>Animal Type:</strong> ${formData.exposureInformation.animalType}</li>`;
  reviewContent += `<li><strong>Bite Location:</strong> ${formData.exposureInformation.biteLocation}</li>`;
  reviewContent += `<li><strong>Upload Image:</strong> ${formData.exposureInformation.uploadImages}</li>`;
  reviewContent += '</ul></div>';

  reviewContent += '<div class="section"><h5 class="text-center font-weight-bold gray">Treatment Given</h5>';
  reviewContent += '<ul class="gray">';
  formData.medicalInformation.medicines.forEach((medicine, index) => {
    reviewContent += `<li><strong>Medicine ${index + 1}:</strong>`;
    reviewContent += `<ul class="gray">`;
    reviewContent += `<li><strong>Type:</strong> ${medicine.medicineType}, <strong>Given:</strong> ${medicine.medicineGiven}, <strong>Dosage:</strong> ${medicine.dosageQuantity} mL, <strong>Route:</strong> ${medicine.route}, <strong>Quantity:</strong> ${medicine.quantity}</li>`;
    reviewContent += `</ul>`;
    reviewContent += `</li>`;
  });
  formData.medicalInformation.equipment.forEach((equipment, index) => {
    reviewContent += `<li><strong>Equipment ${index + 1}:</strong>`;
    reviewContent += `<ul class="gray">`;
    reviewContent += `<li><strong>Type:</strong> ${equipment.equipmentType}, <strong>Amount:</strong> ${equipment.equipmentAmount} pcs</li>`;
    reviewContent += `</ul>`;
    reviewContent += `</li>`;
  });

  reviewContent += `<li><strong>Treatment Category:</strong> ${formData.treatmentInformation.treatmentCategory}</li>`;
  reviewContent += `<li><strong>Number of Sessions:</strong> ${formData.treatmentInformation.sessions}</li>`;
  reviewContent += `<li><strong>Date of Treatment:</strong> ${formData.treatmentInformation.treatmentDate}</li>`;
  reviewContent += `<li><strong>Doctor Remarks:</strong> ${formData.treatmentInformation.doctorRemarks}</li>`;
  reviewContent += '</ul></div> </small>';

  modalBody.innerHTML = reviewContent;
}


// Handling final submit after review



function setMinExposureDate() {
    // Get the value of the birth date
    const birthDate = document.getElementById('birthDate').value;

    if (birthDate) {
        // Calculate 90 days after the birth date
        const minExposureDate = new Date(birthDate);
        minExposureDate.setDate(minExposureDate.getDate() + 90);

        // Format minExposureDate to 'YYYY-MM-DD' for setting min attribute
        const formattedMinExposureDate = minExposureDate.toISOString().split('T')[0];

        // Set the min attribute of the exposureDate input to formattedMinExposureDate
        document.getElementById('exposureDate').min = formattedMinExposureDate;
    }
}

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
    const inputValue = weightInput.value;
    const decimalIndex = inputValue.indexOf('.');

    // Allow: backspace, delete, tab, escape, enter, and Ctrl+A, home, end, arrow keys
    if (
        [8, 9, 27, 13].includes(keyCode) || // Allow: backspace, delete, tab, escape, enter
        (keyCode === 65 && event.ctrlKey === true) || // Allow: Ctrl+A
        (keyCode >= 35 && keyCode <= 40) // Allow: home, end, left, right, down, up
    ) {
        return;
    }

    // Allow only one decimal point
    if (keyCode === 190 || keyCode === 110) { // Allow: '.' (both key codes for different keyboards)
        if (inputValue.includes('.')) {
            event.preventDefault();
        }
        return;
    }

    // Prevent more than two digits after the decimal point
    if (decimalIndex !== -1 && inputValue.substring(decimalIndex + 1).length >= 2) {
        // Allow backspace and delete to enable correction
        if ([8, 46].includes(keyCode)) {
            return;
        }
        // Prevent further input if two decimal places are already present
        if ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 96 && keyCode <= 105)) {
            event.preventDefault();
        }
    }

    // Allow number keys only
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
    var animalTypeInput = document.getElementById("otherAnimalType");
    var biteLocationInput = document.getElementById("biteLocation");

    // Function to validate input and allow only letters and spaces
    function validateAnimalTypeInput(inputField) {
        inputField.addEventListener("keypress", function(event) {
            var keyCode = event.keyCode;
            // Allow letters (a-z, A-Z) and spaces (32)
            if ((keyCode >= 65 && keyCode <= 90) || // A-Z
                (keyCode >= 97 && keyCode <= 122) || // a-z
                keyCode === 32) { // space
                return true;
            } else {
                event.preventDefault();
                return false;
            }
        });
    }

    // Function to validate input and allow letters, spaces, and comma
    function validateBiteLocationInput(inputField) {
        inputField.addEventListener("keypress", function(event) {
            var keyCode = event.keyCode;
            // Allow letters (a-z, A-Z), spaces (32), and comma (44)
            if ((keyCode >= 65 && keyCode <= 90) || // A-Z
                (keyCode >= 97 && keyCode <= 122) || // a-z
                keyCode === 32 || // space
                keyCode === 44) { // comma
                return true;
            } else {
                event.preventDefault();
                return false;
            }
        });
    }

    // Apply input validation to animalType and biteLocation inputs
    validateAnimalTypeInput(animalTypeInput);
    validateBiteLocationInput(biteLocationInput);
});

function toggleCustomSuffixInput(value) {
  const customSuffixInput = document.getElementById('customSuffix');
  if (value === 'Other') {
    customSuffixInput.style.display = 'block';
    customSuffixInput.required = true;
  } else {
    customSuffixInput.style.display = 'none';
    customSuffixInput.value = '';
    customSuffixInput.required = false;
  }
}



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
    validateEmailField();
    validateFirstNameField();
    validateMiddleNameField();
    validateLastNameField();
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
    if (currentStep === 2) {
            $('.step3-error-messages').empty();
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
    const errorContainer = document.querySelector('.step3-error-messages');
    errorContainer.innerHTML = '';

    const inputFields = document.querySelectorAll('.step3-container input, .step3-container select, .step3-container textarea');
    inputFields.forEach(function(field) {
        field.classList.remove('error-border');
    });

    const requiredFields = document.querySelectorAll('.step3-container input[required], .step3-container select[required], .step3-container textarea[required]');
    const missingFields = [];

    requiredFields.forEach(function(field) {
        if (!field.value.trim()) {
            missingFields.push(field);
        }
    });

    if (missingFields.length > 0) {
        const errorMessage = document.createElement('div');
        errorMessage.classList.add('error-message');
        errorMessage.textContent = 'Please fill in all required fields correctly.';
        errorContainer.appendChild(errorMessage);
        
        missingFields.forEach(function(field) {
            field.classList.add('error-border');
        });
        
        return false; // Validation failed
    }
    
    return true; // Validation passed
}


$(document).ready(function () {

  function fetchBrandsAndQuantity(medicineTypeDropdown, medicineGivenDropdown, totalQuantityContainer) {
    const medicineId = medicineTypeDropdown.val();
    console.log("Sending AJAX request with medicineType:", medicineId);

    $.ajax({
        url: 'backend/fetch-brands.php',
        method: 'POST',
        data: { medicineType: medicineId },
        dataType: 'json',
        success: function (response) {
            medicineGivenDropdown.empty();

            // Create the default option
            medicineGivenDropdown.append(`<option value="" selected disabled >Select Brand</option>`);
            // Populate the dropdown with brands from the response
            $.each(response, function (index, value) {
         
                if (!medicineGivenDropdown.find(`option[value="${value.MedicineBrandID}"]`).length) {
                    medicineGivenDropdown.append(`<option value="${value.MedicineBrandID}">${value.BrandName}</option>`);
                }
            });

            updateDropdownOptions();
        },
        error: function (xhr) {
            console.error(xhr.responseText);
        }
    });



        // Event listener for the change event on medicine given dropdown
        medicineGivenDropdown.off('change').on('change', function () {
            const routeInput = $(this).closest('.medicine-item').find('.route');
            const selectedBrandID = $(this).val();
            fetchQuantity(selectedBrandID, totalQuantityContainer, routeInput);
            updateDropdownOptions();
        });
    }

    // Function to fetch quantity based on selected brand
    function fetchQuantity(medicineBrandID, totalQuantityContainer, routeInput) {
        $.ajax({
            url: 'backend/fetch-quantity.php',
            method: 'POST',
            data: { medicineBrandID: medicineBrandID },
            dataType: 'json',
            success: function (response) {
                totalQuantityContainer.text(`Total Quantity: ${response.TotalQuantity}`);
                routeInput.val(response.Route);
                validateQuantity(totalQuantityContainer);
            },
            error: function (xhr) {
                console.error(xhr.responseText);
            }
        });
    }

    // Function to validate quantity input for a specific medicine item
    function validateQuantity(inputField, totalQuantity) {
    const enteredQuantity = parseInt(inputField.val());
    const invalidFeedback = inputField.closest('.form-group').find('.invalid-feedback');

    if (isNaN(enteredQuantity) || enteredQuantity < 1 || enteredQuantity > totalQuantity) {
        inputField.addClass('is-invalid');
        const message = `Quantity must be between 1 and ${totalQuantity}`;
        invalidFeedback.text(message);

        // Add data attributes for tooltip
        inputField.attr('data-toggle', 'tooltip');
        inputField.attr('data-placement', 'right');
        inputField.attr('title', message);

        // Initialize tooltip
        inputField.tooltip({ trigger: 'hover' });
    } else {
        inputField.removeClass('is-invalid');
        invalidFeedback.text('');

        // Remove tooltip
        inputField.removeAttr('data-toggle data-placement title');
        inputField.tooltip('dispose');
    }
}


    // Function to apply input validation to a specific medicine item
    function applyInputValidation(element) {
        element.find('input[name="quantity[]"]').each(function () {
            const inputField = $(this);
            const totalQuantityContainer = element.find('.total-quantity');
            const totalQuantity = parseInt(totalQuantityContainer.text().replace('Total Quantity: ', ''));
            validateQuantity(inputField, totalQuantity);
        });
    }

    // Function to add a new medicine item
    function addMedicineItem() {
        const newMedicineItem = $('#medicineItems .medicine-item').first().clone();
        newMedicineItem.find('input').val('');
        newMedicineItem.find('.total-quantity').text('Total Quantity: ');

        newMedicineItem.appendTo('#medicineItems');
        newMedicineItem.find('.medicineType').trigger('change');
        applyInputValidation(newMedicineItem);
    }

    // Function to add a new equipment item
    function addEquipmentItem() {
        const newEquipmentItem = $('#equipmentItems .equipment-item').first().clone();
        newEquipmentItem.find('input').val('');
        newEquipmentItem.find('input').val('').removeClass('is-invalid'); // Clear input and remove validation class
        newEquipmentItem.find('.invalid-feedback').text(''); // Clear validation message
        newEquipmentItem.appendTo('#equipmentItems');
        applyInputValidation(newEquipmentItem);
    }

    // Function to update dropdown options to prevent duplicate selection
    function updateDropdownOptions() {
    const selectedOptions = [];
    $('.medicineGiven').each(function () {
        const selectedValue = $(this).val();
        if (selectedValue) {
            selectedOptions.push(selectedValue);
        }
    });

    $('.medicineGiven').each(function () {
        const currentDropdown = $(this);
        currentDropdown.find('option').each(function () {
            const optionValue = $(this).val();
            if (optionValue === '') {
                // Ensure the default "Select Brand" option remains disabled
                $(this).prop('disabled', true);
            } else if (selectedOptions.includes(optionValue) && optionValue !== currentDropdown.val()) {
                $(this).attr('disabled', 'disabled');
            } else {
                $(this).removeAttr('disabled');
            }
        });
    });
}


    // Event listener for the change event on medicine type dropdown
    $(document).on('change', '.medicineType', function () {
        const medicineItem = $(this).closest('.medicine-item');
        const medicineGivenDropdown = medicineItem.find('.medicineGiven');
        const totalQuantityContainer = medicineItem.find('.total-quantity');
        fetchBrandsAndQuantity($(this), medicineGivenDropdown, totalQuantityContainer);
    });

    // Event listener for the input event on quantity input
    $(document).on('input', 'input[name="quantity[]"]', function () {
        const inputField = $(this);
        const medicineItem = inputField.closest('.medicine-item');
        const totalQuantityContainer = medicineItem.find('.total-quantity');
        const totalQuantity = parseInt(totalQuantityContainer.text().replace('Total Quantity: ', ''));
        validateQuantity(inputField, totalQuantity);
    });

    // Function to apply input validation event listeners
    function applyInputValidation(element) {
        const allowNumericInput = (event) => {
            const key = event.key;
            if (!/[0-9.]/.test(key) && key !== 'Backspace' && key !== 'Delete' && !['ArrowLeft', 'ArrowRight'].includes(key)) {
                event.preventDefault();
            }
        };

        element.find('input[name="dosageQuantity[]"], input[name="quantity[]"], input[name="equipmentAmount[]"]').on('keydown', allowNumericInput);

        element.find('input[type="number"]').on('input', function () {
            if (this.value < 0 || isNaN(this.value)) {
              this.value = '1';
            }
        });
    }
    $(document).ready(function () {

      $(document).ready(function () {
    // Event listener for the input event on dosage quantity input
    $(document).on('input', 'input[name="dosageQuantity[]"]', function () {
        const inputField = $(this);
        let enteredValue = inputField.val().trim(); // Trim whitespace

        // Convert entered value to float
        let floatValue = parseFloat(enteredValue);

        // Check if the entered value is empty
        if (enteredValue === '') {
            // Allow the field to be empty
            inputField.removeClass('is-invalid');
            return;
        } else if (isNaN(floatValue) || floatValue < 0.3) {
            // Set the value to 0.3 if it is lower than 0.3 or not a number
            inputField.val(0.3);
            floatValue = 0.3;
        }

        // Validate if the value is less than 0.3
        if (floatValue < 0.3) {
            inputField.val(0.3);
        }
    });

    // Event listener for the input event on quantity input
    $(document).on('input', 'input[name="quantity[]"]', function () {
        const inputField = $(this);
        let enteredValue = inputField.val().trim(); // Trim whitespace

        // Convert entered value to integer
        let intValue = parseInt(enteredValue);

        // Check if the entered value is empty
        if (enteredValue === '') {
            // Allow the field to be empty
            inputField.removeClass('is-invalid');
            return;
        } else if (isNaN(intValue) || intValue < 1) {
            // Set the value to 1 if it is lower than 1 or not a number
            inputField.val(1);
            intValue = 1;
        }

        // Validate if the value is less than 1
        if (intValue < 1) {
            inputField.val(1);
        }
    });
});


});

    // Event listener for the "Add Medicine" button
    $(document).on('click', '.addMedicineItem', addMedicineItem);

    // Event listener for the "Add Equipment" button
    $(document).on('click', '.addEquipmentItem', addEquipmentItem);

    // Event listener for the "Remove Equipment" button
    $(document).on('click', '.removeEquipmentItem', function () {
        if ($('.equipment-item').length > 1) {
            $(this).closest('.equipment-item').remove();
            updateDropdownOptions();
        } else {
            alert("At least one equipment item is required.");
        }
    });

    // Event listener for the "Remove Medicine" button
    $(document).on('click', '.removeMedicineItem', function () {
        if ($('.medicine-item').length > 1) {
            $(this).closest('.medicine-item').remove();
            updateDropdownOptions();
        } else {
            alert("At least one medicine item is required.");
        }
    });

    // Function to fetch equipment stock based on selected equipment type
    function fetchEquipmentStock(equipmentTypeDropdown, equipmentAmountInput) {
        const equipmentId = equipmentTypeDropdown.val();
        if (equipmentId) {
            $.ajax({
                url: 'backend/fetch-equipment-stock.php', // Path to your PHP script to fetch equipment stock
                method: 'POST',
                data: { equipmentID: equipmentId },
                dataType: 'json',
                success: function (response) {
                    const totalStock = response.TotalStock;
                    equipmentAmountInput.data('totalStock', totalStock); // Store the total stock in a data attribute
                    validateEquipmentAmount(equipmentAmountInput, totalStock);
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                }
            });
        }
    }

    // Function to validate equipment amount input
    function validateEquipmentAmount(inputField, totalStock) {
        const enteredAmount = parseInt(inputField.val());
        const invalidFeedback = inputField.closest('.form-group').find('.invalid-feedback');

        if (isNaN(enteredAmount) || enteredAmount < 1 || enteredAmount > totalStock) {
            inputField.addClass('is-invalid');
            invalidFeedback.text(`Amount must be between 1 and ${totalStock}`);
        } else {
            inputField.removeClass('is-invalid');
            invalidFeedback.text('');
        }
    }

    // Event listener for the change event on equipment type dropdown
    $(document).on('change', '.equipmentType', function () {
        const equipmentItem = $(this).closest('.equipment-item');
        const equipmentAmountInput = equipmentItem.find('.equipmentAmount');
        fetchEquipmentStock($(this), equipmentAmountInput);
    });

    // Event listener for the input event on equipment amount input
    $(document).on('input', '.equipmentAmount', function () {
        const inputField = $(this);
        const totalStock = inputField.data('totalStock');
        validateEquipmentAmount(inputField, totalStock);
    });

    // Function to validate all equipment and medicine fields
    function validateAllFields() {
        let isValid = true;

        // Validate equipment fields
        $('.equipment-item').each(function () {
            const equipmentAmountInput = $(this).find('.equipmentAmount');
            const totalStock = equipmentAmountInput.data('totalStock');
            if (totalStock !== undefined) {
                validateEquipmentAmount(equipmentAmountInput, totalStock);
                if (equipmentAmountInput.hasClass('is-invalid')) {
                    isValid = false;
                }
            }
        });

        // Validate medicine fields
        $('.medicine-item').each(function () {
            const quantityInput = $(this).find('input[name="quantity[]"]');
            const totalQuantityContainer = $(this).find('.total-quantity');
            const totalQuantity = parseInt(totalQuantityContainer.text().replace('Total Quantity: ', ''));
            validateQuantity(quantityInput, totalQuantity);
            if (quantityInput.hasClass('is-invalid')) {
                isValid = false;
            }
        });

        return isValid;
    }
    document.getElementById('submit-button').addEventListener('click', function(e) {
    e.preventDefault();

    // Validate Step 2 and Step 3 fields
    var step2Valid = validateStep2Fields();
    var step3Valid = validateStep3Fields();
    var allFieldsValid = validateAllFields();

    // Check if all validations passed
    if (step2Valid && step3Valid && allFieldsValid) {
        // Show the review modal
        populateReviewModal();
        $('#reviewModal').modal('show');

        // Get the submitFinal button
        var submitFinalButton = document.getElementById('submitFinal');

        // Remove any existing event listener
        submitFinalButton.removeEventListener('click', submitFinalHandler);

        // Attach the AJAX call to the submitFinal button
        submitFinalButton.addEventListener('click', submitFinalHandler);
    } else {
        console.log('Please fill in all required fields correctly.');
    }
});

// Define the event handler function separately
function submitFinalHandler() {
    const formData = new FormData(document.getElementById('multi-step-form'));

    $.ajax({
        url: 'backend/submit-existing.php',
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response) {
            console.log("Success response:", response);
            if (response.status === 'success') {
                console.log("Form submitted successfully. Redirecting to patient-list.php...");
                sessionStorage.removeItem('formData');
                window.location.href = 'patient-list.php';
            } else {
                console.log("Form submission error: " + response.message);
                $('#responseMessage').text(response.message);
                $('#responseModal').modal('show');
            }
        },
        error: function(xhr, status, error) {
            console.error("Error response:", xhr.responseText);
            $('#responseMessage').text('An error occurred while processing your request. Please try again.');
            $('#responseModal').modal('show');
        }
    });
}



});
function validateStep2Fields() {
    const errorContainer = document.querySelector('.step3-error-messages');
    errorContainer.innerHTML = '';
    let step2Valid = true;

    // Implement your validation logic for Step 2 fields here
    $('#step2 input[required], #step2 select[required]').each(function() {
        if (!$(this).val().trim()) {
            step2Valid = false;
            const errorMessage = document.createElement('div');
            errorMessage.classList.add('error-message');
            errorMessage.textContent = 'Please return to Step 2 for Post-exposure.';
            errorContainer.appendChild(errorMessage);
        }
    });

    return step2Valid;
}

function validateAllFields() {
        var allFieldsValid = true;
        // Implement your validation logic for all fields here
        // Example: Check if all required fields across the form are filled
        $('#multi-step-form input[required], #multi-step-form select[required]').each(function() {
            if (!$(this).val().trim()) {
                allFieldsValid = false;
                return false; // Exit loop early if an invalid field is found
            }
        });
        return allFieldsValid;
    }
    function saveFormData() {
    const formData = {};
    $('#step1, #step2').find('input, select, textarea').each(function () {
        formData[$(this).attr('name')] = $(this).val();
    });
    sessionStorage.setItem('formData', JSON.stringify(formData));
}


    // Function to load form data from sessionStorage
    function loadFormData() {
    const formData = JSON.parse(sessionStorage.getItem('formData'));
    if (formData) {
        $('#step1, #step2').find('input, select, textarea').each(function () {
            if (formData[$(this).attr('name')]) {
                $(this).val(formData[$(this).attr('name')]);
            }
        });
    }
}


// Save form data on input change within step1 and step2
$('#step1, #step2').on('input change', 'input, select, textarea', function () {
    saveFormData();
});

// Load form data on page load for step1 and step2
$(document).ready(function() {
    loadFormData();
});





function validateBirthDate(birthDate) {
  const currentDate = new Date();
  const selectedDate = new Date(birthDate);

  // Calculate 1 day before the current date
  const oneDayBefore = new Date(currentDate);
  oneDayBefore.setDate(currentDate.getDate() - 1);

  // Check if selectedDate is at least 1 day before the current date
  return selectedDate < oneDayBefore;
}



  // Function to validate age
  function validateAge(age) {
    return age >= 0 && age <= 124;
  }



  // Function to validate weight
  function validateWeight(weight) {
    return weight >= 1 && weight <= 650;
  }

  let validDomains = []; // Array to store valid domains

  // Fetch valid domains from CSV file
  fetch('free-domains-2.csv')
    .then(response => response.text())
    .then(csvData => {
      validDomains = csvData.split('\n').map(domain => domain.trim());
      console.log('Valid domains:', validDomains); // Debug log to check valid domains
    })
    .catch(error => console.error('Error fetching CSV file:', error));

  // Function to validate email format and domain
  function validateEmail(email) {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const emailDomain = email.split('@')[1];

    if (!emailPattern.test(email)) {
      return 'Invalid email format.';
    } else if (!validDomains.includes(emailDomain)) {
      return 'Please enter an email with a known domain.';
    } else {
      return ''; // Email is valid
    }
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
      showError(document.getElementById('age'), 'Age should be between 1 and 124.');
    } else {
      removeError(document.getElementById('age'));
    }
  }




  // Function to validate weight field
  function validateWeightField() {
  const weightElement = document.getElementById('weight');
  const weightValue = weightElement.value.trim();
  const weight = parseFloat(weightValue);
  const isValid = validateWeight(weight);

  if (!isValid && weightValue !== '') {
    showError(weightElement, 'Weight should be between 1 and 650 kg.');
  } else {
    removeError(weightElement);
  }
}


  // Function to validate email field
  function validateEmailField() {
  const emailField = document.getElementById('email');
  const email = emailField.value.trim(); // Trimmed email value

  const errorMessage = validateEmail(email); // Validate email format and domain
  if (errorMessage) {
    showError(emailField, errorMessage); // Show error message
  } else {
    removeError(emailField); // Remove error styling
  }
}

  function validateFirstNameField() {
  const fName = document.getElementById('fName').value.trim();
  const errorMessage = validateName(fName);
  if (errorMessage && fName !== '') {
    showError(document.getElementById('fName'), errorMessage);
  } else {
    removeError(document.getElementById('fName'));
  }
}


// Function to validate middle name field
function validateMiddleNameField() {
  const mName = document.getElementById('mName').value.trim();
  const errorMessage = validateName(mName);
  if (errorMessage && mName !== '') { // middle name can be optional
    showError(document.getElementById('mName'), errorMessage);
  } else {
    removeError(document.getElementById('mName'));
  }
}

// Function to validate last name field
function validateLastNameField() {
  const lName = document.getElementById('lName').value.trim();
  const errorMessage = validateName(lName);
  if (errorMessage && lName !== '') {
    showError(document.getElementById('lName'), errorMessage);
  } else {
    removeError(document.getElementById('lName'));
  }
}

// Function to validate name fields
function validateName(name) {
  const namePattern = /^[A-Za-zÀ-ÖØ-öø-ÿ]+(?:[ '-][A-Za-zÀ-ÖØ-öø-ÿ]+)*$/; // Allows letters, spaces, hyphens, and apostrophes
  const repeatingCharsPattern = /(.)\1{2,}/; // Matches any character repeated 3 or more times

  if (!namePattern.test(name)) {
    return 'Names should only contain letters, spaces, hyphens, and apostrophes.';
  } else if (repeatingCharsPattern.test(name)) {
    return 'Names should not contain repeating characters.';
  } else if (/[A-Z]{2,}/.test(name) || (/^[a-z]/.test(name) && /[A-Z]/.test(name.substring(1)))) {
    return 'Names should not have unusual capitalization.';
  } else {
    return '';
  }
}

  // Add event listeners to input fields to validate them as the user types
  document.getElementById('birthDate').addEventListener('input', validateBirthDateField);
  document.getElementById('age').addEventListener('input', validateAgeField);
  document.getElementById('weight').addEventListener('input', validateWeightField);
  document.getElementById('fName').addEventListener('input', validateFirstNameField);

  document.getElementById('mName').addEventListener('input', validateMiddleNameField);
  document.getElementById('lName').addEventListener('input', validateLastNameField);
  


    $('#email').on('input', function() {
      validateEmailField();
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneNumberInput = document.getElementById('phoneNumber');

    phoneNumberInput.addEventListener('input', function() {
        let phoneNumber = phoneNumberInput.value.trim();

        // Check if the number does not start with '09'
        if (!phoneNumber.startsWith('09')) {
            phoneNumber = '09' + phoneNumber.substring(2); // Prepend '09' and keep the rest of the input
            phoneNumberInput.value = phoneNumber; // Update the input value
        }
    });
});
document.addEventListener('DOMContentLoaded', function() {
    const emergencyPhoneNumberInput = document.getElementById('emergencyPhoneNumber');

    emergencyPhoneNumberInput.addEventListener('input', function() {
        let phoneNumber = emergencyPhoneNumberInput.value.trim();

        // Check if the number does not start with '09'
        if (!phoneNumber.startsWith('09')) {
            phoneNumber = '09' + phoneNumber.substring(2); // Prepend '09' and keep the rest of the input
            emergencyPhoneNumberInput.value = phoneNumber; // Update the input value
        }
    });
});
// Add event listeners to input fields to capitalize them as the user types
document.getElementById('fName').addEventListener('input', function() {
    const nameInput = this;
    let capitalized = capitalizeFirstLetter(nameInput.value);
    nameInput.value = capitalized;
});

document.getElementById('mName').addEventListener('input', function() {
    const nameInput = this;
    let capitalized = capitalizeFirstLetter(nameInput.value);
    nameInput.value = capitalized;
});

document.getElementById('lName').addEventListener('input', function() {
    const nameInput = this;
    let capitalized = capitalizeFirstLetter(nameInput.value);
    nameInput.value = capitalized;
});

document.getElementById('emergencyContact').addEventListener('input', function() {
    const nameInput = this;
    let capitalized = capitalizeFirstLetter(nameInput.value);
    nameInput.value = capitalized;
});

// Function to capitalize the first letter of each word in a string
function capitalizeFirstLetter(str) {
    return str.split(' ').map(word => {
        if (word) {
            return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
        }
        return '';
    }).join(' ');
}
document.getElementById('uploadImages').addEventListener('change', function() {
    const input = this;
    const files = input.files;

    // Regular expression to check file extension
    const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (!allowedExtensions.test(file.name)) {
            showError(input, 'Only JPEG, PNG, and JPG files are allowed.');
            input.value = ''; // Clear the input field
            return;
        }
    }

    removeError(input);
});


</script>
<script>
    // Function to handle selection change and show/hide the other input field
    function checkOtherOption() {
        const animalTypeSelect = document.getElementById('animalType');
        const otherAnimalTypeInput = document.getElementById('otherAnimalType');

        if (animalTypeSelect.value === 'Other') {
            otherAnimalTypeInput.classList.remove('d-none');
            otherAnimalTypeInput.focus(); // Focus on the input field for user convenience
        } else {
            otherAnimalTypeInput.classList.add('d-none');
            otherAnimalTypeInput.value = ''; // Clear the input field if it was previously filled
        }
    }
</script>
<script>
function validateLength(element, maxLength) {
    if (element.value.length > maxLength) {
        element.value = element.value.slice(0, maxLength);
    }
}
</script>

</body>
</html>
