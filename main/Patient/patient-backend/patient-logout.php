<?php
error_reporting(E_ALL);
session_start();

unset($_SESSION['user']);
unset($_SESSION['userID']);


$_SESSION['message'] = "You are now logged out";

session_destroy();

header("location: ../Patient Login.php");
exit(); // Ensure that no code is executed after the header redirect
?>
