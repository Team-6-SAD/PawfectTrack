<?php
header('Content-Type: application/json');

require_once 'backend/pawfect_connect.php';

$sql = "SELECT EmailAddress FROM contactinformation";
$result = $conn->query($sql);

$emails = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $emails[] = $row;
    }
}

echo json_encode($emails);

$conn->close();
?>
