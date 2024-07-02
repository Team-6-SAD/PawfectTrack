<?php 
  $servername = "localhost";
  $username = "pawfect";
  $password = "EJHts0D5qExNa9P4IOAt";
  $dbname = "pawfect";

  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);

  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }
  ?>