<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are present
    if (isset($_POST['fName']) && isset($_POST['lName']) && isset($_POST['birthDate']) && isset($_POST['age']) && isset($_POST['sex']) && isset($_POST['weight']) && isset($_POST['phoneNumber']) && isset($_POST['email']) && isset($_POST['province']) && isset($_POST['city']) && isset($_POST['address']) && isset($_POST['emergencyContact']) && isset($_POST['emergency_contact_relationship']) && isset($_POST['emergencyPhoneNumber']) && isset($_POST['exposureDate']) && isset($_POST['exposureBy']) && isset($_POST['exposureType']) && isset($_POST['animalType']) && isset($_POST['biteLocation']) && isset($_POST['treatmentCategory']) && isset($_POST['sessions']) && isset($_POST['treatmentDate'])) {
        // Extract submitted form data
        $fName = $_POST['fName'];
        $mName = $_POST['mName'];
        $lName = $_POST['lName'];
        $birthDate = $_POST['birthDate'];
        $age = $_POST['age'];
        $sex = $_POST['sex'];
        $weight = $_POST['weight'];
        $phoneNumber = $_POST['phoneNumber'];
        $email = $_POST['email'];
        $province = $_POST['province'];
        $city = $_POST['city'];
        $address = $_POST['address'];
        $emergencyContact = $_POST['emergencyContact'];
        $emergency_contact_relationship = $_POST['emergency_contact_relationship'];
        $emergencyPhoneNumber = $_POST['emergencyPhoneNumber'];
        $exposureDate = $_POST['exposureDate'];
        $exposureBy = $_POST['exposureBy'];
        $exposureType = $_POST['exposureType'];
        $animalType = $_POST['animalType'];
        $biteLocation = $_POST['biteLocation'];
        $treatmentCategory = $_POST['treatmentCategory'];
        $sessions = $_POST['sessions'];
        $treatmentDate = $_POST['treatmentDate'];
        $doctorRemarks = $_POST['doctorRemarks'];
        
        // Handle array-like form fields
        $medicineTypes = $_POST['medicineType'];
        $medicineGivens = $_POST['medicineGiven'];
        $dosageQuantities = $_POST['dosageQuantity'];
        $routes = $_POST['route'];
        $quantities = $_POST['quantity'];
        
        // Handle equipment array
        $equipmentTypes = $_POST['equipmentType'];
        $equipmentAmounts = $_POST['equipmentAmount'];

        // Echo the submitted form data for testing
        echo "First Name: $fName <br>";
        echo "Middle Name: $mName <br>";
        echo "Last Name: $lName <br>";
        echo "Birth Date: $birthDate <br>";
        echo "Age: $age <br>";
        echo "Sex: $sex <br>";
        echo "Weight: $weight <br>";
        echo "Phone Number: $phoneNumber <br>";
        echo "Email: $email <br>";
        echo "Province: $province <br>";
        echo "City: $city <br>";
        echo "Address: $address <br>";
        echo "Emergency Contact: $emergencyContact <br>";
        echo "Emergency Contact Relationship: $emergency_contact_relationship <br>";
        echo "Emergency Phone Number: $emergencyPhoneNumber <br>";
        echo "Exposure Date: $exposureDate <br>";
        echo "Exposure By: $exposureBy <br>";
        echo "Exposure Type: $exposureType <br>";
        echo "Animal Type: $animalType <br>";
        echo "Bite Location: $biteLocation <br>";
        echo "Treatment Category: $treatmentCategory <br>";
        echo "Sessions: $sessions <br>";
        echo "Treatment Date: $treatmentDate <br>";
        echo "Doctor Remarks: $doctorRemarks <br>";
        
        // Echo array-like form field data
        echo "<br>Medicine Types:<br>";
        print_r($medicineTypes);
        echo "<br>Medicine Givens:<br>";
        print_r($medicineGivens);
        echo "<br>Dosage Quantities:<br>";
        print_r($dosageQuantities);
        echo "<br>Routes:<br>";
        print_r($routes);
        echo "<br>Quantities:<br>";
        print_r($quantities);
        
        // Echo equipment array data
        echo "<br>Equipment Types:<br>";
        print_r($equipmentTypes);
        echo "<br>Equipment Amounts:<br>";
        print_r($equipmentAmounts);
    } else {
        // Required fields are missing
        echo "Error: Required fields are missing!";
    }
} else {
    // Form was not submitted via POST method
    echo "Error: Form was not submitted!";
}
?>
