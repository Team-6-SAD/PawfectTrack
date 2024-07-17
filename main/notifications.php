<?php
// Database credentials
include 'backend/pawfect_connect.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to select MedicineBrandIDs that have no corresponding entries in medicineinventory
$sql_no_stock = "
SELECT 
    mb.MedicineBrandID,
    mb.BrandName,
    'no_stock' AS type,
    mb.dismissed_at AS dismissed_at
FROM 
    medicinebrand mb
LEFT JOIN 
    medicineinventory mi ON mb.MedicineBrandID = mi.MedicineBrandID
WHERE 
    (mi.MedicineBrandID IS NULL OR mi.StockQuantity = 0)
";

// Query to select MedicineBrandIDs with expiring stock within the next 30 days
$sql_expiring_stock = "
SELECT 
    DISTINCT mb.MedicineBrandID,
    mb.BrandName,
    mi.StockExpiryDate,
    'expiring_stock' AS type,
    mb.dismissed_at AS dismissed_at
FROM 
    medicineinventory mi
JOIN 
    medicinebrand mb ON mi.MedicineBrandID = mb.MedicineBrandID
WHERE 
    mi.StockExpiryDate <= DATE_ADD(NOW(), INTERVAL 30 DAY)
    AND mi.StockQuantity > 0
";

// Query to select MedicineBrandIDs with low stock
$sql_low_stock = "
SELECT 
    i.MedicineBrandID,
    mb.BrandName,
    GROUP_CONCAT(i.InventoryID ORDER BY i.InventoryID SEPARATOR ', ') AS InventoryIDs,
    SUM(i.StockQuantity) AS TotalQuantity,
    'low_stock' AS type,
    mb.dismissed_at AS dismissed_at
FROM 
    medicineinventory i 
JOIN 
    medicinebrand mb ON i.MedicineBrandID = mb.MedicineBrandID 
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
    'equipment_low_stock' AS type,
    e.dismissed_at AS dismissed_at
FROM 
    equipmentstock es
JOIN 
    equipment e ON es.EquipmentID = e.EquipmentID
WHERE 
    es.Quantity > 0 AND es.Quantity < 100
";

// Query to select EquipmentIDs with stock quantity exactly 0
$sql_no_stock_equipment = "
SELECT 
    es.EquipmentID,
    e.Name AS EquipmentName,
    es.Quantity,
    'equipment_no_stock' AS type,
    e.dismissed_at AS dismissed_at
FROM 
    equipmentstock es
JOIN 
    equipment e ON es.EquipmentID = e.EquipmentID
WHERE 
    es.Quantity = 0
";

// Execute queries and handle errors
$result_no_stock = $conn->query($sql_no_stock);
if (!$result_no_stock) {
    die("Query failed: " . $conn->error);
}

$result_expiring_stock = $conn->query($sql_expiring_stock);
if (!$result_expiring_stock) {
    die("Query failed: " . $conn->error);
}

$result_low_stock = $conn->query($sql_low_stock);
if (!$result_low_stock) {
    die("Query failed: " . $conn->error);
}

$result_low_stock_equipment = $conn->query($sql_low_stock_equipment);
if (!$result_low_stock_equipment) {
    die("Query failed: " . $conn->error);
}

$result_no_stock_equipment = $conn->query($sql_no_stock_equipment);
if (!$result_no_stock_equipment) {
    die("Query failed: " . $conn->error);
}

$notifications = [];
$nonDismissedCount = 0; // Initialize count for non-dismissed notifications

// Function to determine if a notification is dismissed (read)
function isDismissed($dismissed_at) {
    if ($dismissed_at === null) {
        return false; // Not dismissed
    }
    // Check if dismissed less than 24 hours ago
    return (strtotime($dismissed_at) >= strtotime('-1 day'));
}

// Process medicine notifications for no stock, low stock, and expiring stock
if ($result_no_stock->num_rows > 0) {
    while ($row = $result_no_stock->fetch_assoc()) {
        $dismissed = isDismissed($row['dismissed_at']);
        $notifications[] = [
            'MedicineBrandID' => $row['MedicineBrandID'],
            'BrandName' => $row['BrandName'],
            'type' => $row['type'],
            'dismissed' => $dismissed // Check dismissed status
        ];
        if (!$dismissed) {
            $nonDismissedCount++; // Increment count for non-dismissed notifications
        }
    }
}

if ($result_expiring_stock->num_rows > 0) {
    while ($row = $result_expiring_stock->fetch_assoc()) {
        $dismissed = isDismissed($row['dismissed_at']);
        $notifications[] = [
            'MedicineBrandID' => $row['MedicineBrandID'],
            'StockExpiryDate' => $row['StockExpiryDate'],
            'BrandName' => $row['BrandName'],
            'type' => $row['type'],
            'dismissed' => $dismissed // Check dismissed status
        ];
        if (!$dismissed) {
            $nonDismissedCount++; // Increment count for non-dismissed notifications
        }
    }
}

if ($result_low_stock->num_rows > 0) {
    while ($row = $result_low_stock->fetch_assoc()) {
        $dismissed = isDismissed($row['dismissed_at']);
        $notifications[] = [
            'MedicineBrandID' => $row['MedicineBrandID'],
            'BrandName' => $row['BrandName'],
            'InventoryIDs' => $row['InventoryIDs'],
            'TotalQuantity' => $row['TotalQuantity'],
            'type' => $row['type'],
            'dismissed' => $dismissed // Check dismissed status
        ];
        if (!$dismissed) {
            $nonDismissedCount++; // Increment count for non-dismissed notifications
        }
    }
}

// Process equipment notifications for low stock and no stock
if ($result_low_stock_equipment->num_rows > 0) {
    while ($row = $result_low_stock_equipment->fetch_assoc()) {
        $dismissed = isDismissed($row['dismissed_at']);
        $notifications[] = [
            'EquipmentID' => $row['EquipmentID'],
            'EquipmentName' => $row['EquipmentName'],
            'Quantity' => $row['Quantity'],
            'type' => $row['type'],
            'dismissed' => $dismissed // Check dismissed status
        ];
        if (!$dismissed) {
            $nonDismissedCount++; // Increment count for non-dismissed notifications
        }
    }
}

if ($result_no_stock_equipment->num_rows > 0) {
    while ($row = $result_no_stock_equipment->fetch_assoc()) {
        $dismissed = isDismissed($row['dismissed_at']);
        $notifications[] = [
            'EquipmentID' => $row['EquipmentID'],
            'EquipmentName' => $row['EquipmentName'],
            'Quantity' => $row['Quantity'],
            'type' => $row['type'],
            'dismissed' => $dismissed // Check dismissed status
        ];
        if (!$dismissed) {
            $nonDismissedCount++; // Increment count for non-dismissed notifications
        }
    }
}

// Prepare the response
$response = [
    'count' => $nonDismissedCount,
    'notifications' => $notifications
];

// Return the results as JSON
header('Content-Type: application/json');
echo json_encode($response);

// Close connection
$conn->close();
?>
