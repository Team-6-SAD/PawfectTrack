<?php
require_once 'backend/pawfect_connect.php';

// Function to sanitize data for output in Excel
function sanitizeForExcel($value) {
    // Escape special characters and enclose in double quotes
    return '"' . str_replace('"', '""', $value) . '"';
}

// Define the start and end dates for the past 7 days
$startDate = date('Y-m-d', strtotime('-7 days'));
$endDate = date('Y-m-d');

// Query 1: Get weekly treatment count
$sql = "SELECT COUNT(*) AS TreatmentCount FROM treatment WHERE DateofTreatment BETWEEN ? AND ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $startDate, $endDate);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fetch the treatment count
if ($row = mysqli_fetch_assoc($result)) {
    $weeklyTreatmentCount = $row['TreatmentCount'];
} else {
    $weeklyTreatmentCount = 0; // Default to 0 if no treatments were found
}

// Query 2: Get medicine stock data
$sql = "SELECT SUM(mi.StockQuantity) AS TotalStockQuantity, mb.MedicineID, m.MedicineName
        FROM medicineinventory mi
        JOIN medicinebrand mb ON mi.MedicineBrandID = mb.MedicineBrandID
        JOIN medicine m ON mb.MedicineID = m.MedicineID
        GROUP BY m.MedicineID";
$result = mysqli_query($conn, $sql);

// Check if the query was successful
if ($result) {
    // Initialize an array to store medicine data
    $medicineData = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $medicineData[] = $row;
    }
} else {
    // Error executing the query
    echo "Error fetching medicine data: " . mysqli_error($conn);
    exit;
}

// Query 3: Get most used medicine brand and total quantity
$sql = "SELECT mu.MedicineBrand, SUM(mu.Quantity) AS TotalQuantity
        FROM medicineusage mu
        GROUP BY mu.MedicineBrand
        ORDER BY TotalQuantity DESC
        LIMIT 1";
$result = mysqli_query($conn, $sql);

// Check if the query was successful
if ($result) {
    // Fetch the row with the highest total quantity
    $row = mysqli_fetch_assoc($result);
    // Check if a row was fetched
    if ($row) {
        // Extract the MedicineBrand and TotalQuantity
        $medicineBrandName = $row['MedicineBrand'];
        $totalQuantity = $row['TotalQuantity'];
    } else {
        // No data found
        $medicineBrandName = "Unknown";
        $totalQuantity = 0;
    }
} else {
    // Error executing the query
    $medicineBrandName = "Unknown";
    $totalQuantity = 0;
}

// Query 4: Get treatment counts by category and month
$sql = "SELECT 
            COUNT(*) AS TreatmentCount,
            DATE_FORMAT(DateofTreatment, '%Y-%m') AS Month,
            Category
        FROM 
            treatment
        GROUP BY 
            Month, Category";
$result = mysqli_query($conn, $sql);

// Check if the query was successful
if ($result) {
    // Initialize an array to store treatment data
    $treatmentData = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $treatmentData[] = $row;
    }
} else {
    // Error executing the query
    echo "Error fetching treatment data: " . mysqli_error($conn);
    exit;
}

// Query 5: Get monthly medicine usage data
$sql = "SELECT 
            DATE_FORMAT(mu.UsageDate, '%Y-%m') AS Month,
            SUM(mu.Quantity) AS TotalQuantity
        FROM 
            medicineusage mu
        GROUP BY 
            DATE_FORMAT(mu.UsageDate, '%Y-%m')
        ORDER BY 
            DATE_FORMAT(mu.UsageDate, '%Y-%m')";
$result = mysqli_query($conn, $sql);

// Check if the query was successful
if ($result) {
    // Initialize arrays to hold data
    $labels = [];
    $data = [];

    // Process the MySQL result
    while ($row = mysqli_fetch_assoc($result)) {
        // Extract month name from the date format
        $month = date('F', strtotime($row['Month']));
        // Append month name to labels array
        $labels[] = $month;
        // Append total quantity to data array
        $data[] = $row['TotalQuantity'];
    }
} else {
    // Error executing the query
    echo "Error fetching medicine usage data: " . mysqli_error($conn);
    exit;
}

// Set headers for Excel file download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Weekly_Report.xls");

// Output content for Excel file
?>
<html>
<head>
    <title>Weekly Report</title>
</head>
<body>
    <table border="1">
        <tr>
            <th colspan="2">Weekly Treatment Count</th>
        </tr>
        <tr>
            <td>Weekly Treatment Count:</td>
            <td><?php echo sanitizeForExcel($weeklyTreatmentCount); ?></td>
        </tr>
        <tr>
            <th colspan="2">Medicine Stock Data</th>
        </tr>
        <tr>
            <td>Medicine Name</td>
            <td>Total Stock Quantity</td>
        </tr>
        <?php foreach ($medicineData as $medData): ?>
            <tr>
                <td><?php echo sanitizeForExcel($medData['MedicineName']); ?></td>
                <td><?php echo sanitizeForExcel($medData['TotalStockQuantity']); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th colspan="2">Most Used Medicine Brand</th>
        </tr>
        <tr>
            <td>Medicine Brand</td>
            <td>Total Quantity</td>
        </tr>
        <tr>
            <td><?php echo sanitizeForExcel($medicineBrandName); ?></td>
            <td><?php echo sanitizeForExcel($totalQuantity); ?></td>
        </tr>
        <tr>
            <th colspan="2">Treatment Counts by Category and Month</th>
        </tr>
        <tr>
            <td>Month</td>
            <td>Category</td>
            <td>Treatment Count</td>
        </tr>
        <?php foreach ($treatmentData as $treatment): ?>
            <tr>
                <td><?php echo sanitizeForExcel($treatment['Month']); ?></td>
                <td><?php echo sanitizeForExcel($treatment['Category']); ?></td>
                <td><?php echo sanitizeForExcel($treatment['TreatmentCount']); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th colspan="2">Monthly Medicine Usage</th>
        </tr>
        <tr>
            <td>Month</td>
            <td>Total Quantity</td>
        </tr>
        <?php for ($i = 0; $i < count($labels); $i++): ?>
            <tr>
                <td><?php echo sanitizeForExcel($labels[$i]); ?></td>
                <td><?php echo sanitizeForExcel($data[$i]); ?></td>
            </tr>
        <?php endfor; ?>
    </table>
</body>
</html>
