<?php
// Database credentials
include 'backend/pawfect_connect.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$patientID = $_POST['patientID'];

// Check for pending appointments
$sql = "SELECT COUNT(*) AS count FROM appointmentinformation WHERE patientID = ? AND status = 'Pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patientID);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$stmt->close();
$conn->close();

echo json_encode(['hasPendingAppointments' => $row['count'] > 0]);
?>
