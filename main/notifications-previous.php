<?php
// Database credentials
include 'backend/pawfect_connect.php';

// Query to select MedicineBrandIDs that have no corresponding entries in medicineinventory
$sql_no_stock = "
SELECT 
    mb.MedicineBrandID,
    mb.BrandName,
    'no_stock' AS type
FROM 
    medicinebrand mb
LEFT JOIN 
    medicineinventory mi ON mb.MedicineBrandID = mi.MedicineBrandID
WHERE 
    (mi.MedicineBrandID IS NULL OR mi.StockQuantity = 0)
    AND (mb.dismissed_at IS NULL)
";

// Query to select MedicineBrandIDs with expiring stock within the next 30 days
$sql_expiring_stock = "
SELECT 
    DISTINCT mb.MedicineBrandID,
    mb.BrandName,
    mi.StockExpiryDate,
    'expiring_stock' AS type
FROM 
    medicineinventory mi
JOIN 
    medicinebrand mb ON mi.MedicineBrandID = mb.MedicineBrandID
WHERE 
    mi.StockExpiryDate <= DATE_ADD(NOW(), INTERVAL 30 DAY)
    AND mi.StockQuantity > 0
    AND (mi.dismissed_at IS NULL)
";

// Query to select MedicineBrandIDs with low stock
$sql_low_stock = "
SELECT 
    i.MedicineBrandID,
    mb.BrandName,
    GROUP_CONCAT(i.InventoryID ORDER BY i.InventoryID SEPARATOR ', ') AS InventoryIDs,
    SUM(i.StockQuantity) AS TotalQuantity,
    'low_stock' AS type
FROM 
    medicineinventory i 
JOIN 
    medicinebrand mb ON i.MedicineBrandID = mb.MedicineBrandID 
WHERE 
    (i.dismissed_at IS NULL OR i.dismissed_at < DATE_SUB(NOW(), INTERVAL 15 MINUTE))
GROUP BY 
    i.MedicineBrandID, mb.BrandName
HAVING 
    SUM(i.StockQuantity) > 1 AND SUM(i.StockQuantity) < 20
ORDER BY 
    mb.BrandName;


";

// Query to select EquipmentIDs with stock quantity less than 10
$sql_low_stock_equipment = "
SELECT 
    es.EquipmentID,
    e.Name AS EquipmentName,
    es.Quantity,
    'equipment_low_stock' AS type
FROM 
    equipmentstock es
JOIN 
    equipment e ON es.EquipmentID = e.EquipmentID
WHERE 
    es.Quantity > 0 AND es.Quantity < 100
    AND (es.dismissed_at IS NULL OR es.dismissed_at < DATE_SUB(NOW(), INTERVAL 15 MINUTE))
";

// Query to select EquipmentIDs with stock quantity exactly 0
$sql_no_stock_equipment = "
SELECT 
    es.EquipmentID,
    e.Name AS EquipmentName,
    es.Quantity,
    'equipment_no_stock' AS type
FROM 
    equipmentstock es
JOIN 
    equipment e ON es.EquipmentID = e.EquipmentID
WHERE 
    es.Quantity = 0
    AND (es.dismissed_at IS NULL OR es.dismissed_at < DATE_SUB(NOW(), INTERVAL 15 MINUTE))
";

// Execute queries
$result_no_stock = $conn->query($sql_no_stock);
$result_expiring_stock = $conn->query($sql_expiring_stock);
$result_low_stock = $conn->query($sql_low_stock);
$result_low_stock_equipment = $conn->query($sql_low_stock_equipment);
$result_no_stock_equipment = $conn->query($sql_no_stock_equipment);

$notifications = [];

// Process medicine notifications for no stock, low stock, and expiring stock
if ($result_no_stock->num_rows > 0) {
    while ($row = $result_no_stock->fetch_assoc()) {
        $notifications[] = [
            'MedicineBrandID' => $row['MedicineBrandID'],
            'BrandName' => $row['BrandName'],
            'type' => $row['type']
        ];
    }
}

if ($result_expiring_stock->num_rows > 0) {
    while ($row = $result_expiring_stock->fetch_assoc()) {
        $notifications[] = [
            'MedicineBrandID' => $row['MedicineBrandID'],
            'StockExpiryDate' => $row['StockExpiryDate'],
            'BrandName' => $row['BrandName'],
            'type' => $row['type']
        ];
    }
}

if ($result_low_stock->num_rows > 0) {
    while ($row = $result_low_stock->fetch_assoc()) {
        $notifications[] = [
            'MedicineBrandID' => $row['MedicineBrandID'],
            'BrandName' => $row['BrandName'],
            'InventoryIDs' => $row['InventoryIDs'],
            'TotalQuantity' => $row['TotalQuantity'],
            'type' => $row['type']
        ];
    }
}

// Process equipment notifications for low stock and no stock
if ($result_low_stock_equipment->num_rows > 0) {
    while ($row = $result_low_stock_equipment->fetch_assoc()) {
        $notifications[] = [
            'EquipmentID' => $row['EquipmentID'],
            'EquipmentName' => $row['EquipmentName'],
            'Quantity' => $row['Quantity'],
            'type' => $row['type']
        ];
    }
}

if ($result_no_stock_equipment->num_rows > 0) {
    while ($row = $result_no_stock_equipment->fetch_assoc()) {
        $notifications[] = [
            'EquipmentID' => $row['EquipmentID'],
            'EquipmentName' => $row['EquipmentName'],
            'Quantity' => $row['Quantity'],
            'type' => $row['type']
        ];
    }
}

// Return the results as JSON
header('Content-Type: application/json');
echo json_encode($notifications);

// Close connection
$conn->close();
?>
