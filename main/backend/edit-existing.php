<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection
    include_once 'pawfect_connect.php';

    // Extract patient data from the form
    $patientId = $_POST['patientID'];
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
    $emergencyContactRelationship = $_POST['emergency_contact_relationship'];
    $emergencyPhoneNumber = $_POST['emergencyPhoneNumber'];

    // Update patient data in the database
    $patientUpdateQuery = "UPDATE patient SET FirstName = ?, MiddleName = ?, LastName = ?, BirthDate = ?, Age = ?, Sex = ?, Weight = ? WHERE PatientID = ?";
    $patientStmt = mysqli_prepare($conn, $patientUpdateQuery);
    mysqli_stmt_bind_param($patientStmt, "sssssssi", $fName, $mName, $lName, $birthDate, $age, $sex, $weight, $patientId);
    mysqli_stmt_execute($patientStmt);

    // Update contact information with patient ID
    $contactUpdateQuery = "UPDATE contactinformation SET LineNumber = ?, EmailAddress = ? WHERE PatientID = ?";
    $contactStmt = mysqli_prepare($conn, $contactUpdateQuery);
    mysqli_stmt_bind_param($contactStmt, "ssi", $phoneNumber, $email, $patientId);
    mysqli_stmt_execute($contactStmt);

    // Update patient address with patient ID
    $addressUpdateQuery = "UPDATE patientaddress SET Province = ?, City = ?, Address = ? WHERE PatientID = ?";
    $addressStmt = mysqli_prepare($conn, $addressUpdateQuery);
    mysqli_stmt_bind_param($addressStmt, "sssi", $province, $city, $address, $patientId);
    mysqli_stmt_execute($addressStmt);

    // Update emergency contact with patient ID
    $emergencyUpdateQuery = "UPDATE emergencycontact SET FullName = ?, Relationship = ?, LineNumber = ? WHERE PatientID = ?";
    $emergencyStmt = mysqli_prepare($conn, $emergencyUpdateQuery);
    mysqli_stmt_bind_param($emergencyStmt, "sssi", $emergencyContact, $emergencyContactRelationship, $emergencyPhoneNumber, $patientId);
    mysqli_stmt_execute($emergencyStmt);

    // Close the database connection
    mysqli_close($conn);
}
?>
