<?php
include 'pawfect_connect.php'; // Replace with your actual database connection file

if (isset($_POST['equipmentID'])) {
    $equipmentID = $_POST['equipmentID'];

    // Query to get the total stock for the selected equipment
    $sql = "SELECT Quantity FROM equipmentstock WHERE EquipmentID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $equipmentID);
    $stmt->execute();
    $stmt->bind_result($totalStock);
    $stmt->fetch();
    $stmt->close();

    // Return the total stock as JSON
    echo json_encode(['TotalStock' => $totalStock]);
}
?>
