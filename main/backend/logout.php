<?php
error_reporting(E_ALL);
session_start();

unset($_SESSION['tenantUsername']);
unset($_SESSION['TenantID']);
unset($_SESSION['ApplicantID']);
unset($_SESSION['AdminUsername']);

$_SESSION['message'] = "You are now logged out";

session_destroy();

header("location: ../Admin Login.php");
exit(); // Ensure that no code is executed after the header redirect
?>
