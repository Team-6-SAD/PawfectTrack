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

// Query 6: Get top 5 cities based on patient addresses
$sql = "SELECT City, COUNT(*) AS CityCount 
        FROM patientaddress 
        GROUP BY City 
        ORDER BY CityCount DESC 
        LIMIT 5";

$result = $conn->query($sql);

$cities = [];
$counts = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cities[] = $row['City'];
        $counts[] = $row['CityCount'];
    }
}

// Query 7: Get age range counts
$sql = "SELECT 
            AgeRange,
            COUNT(*) AS Count
        FROM (
            SELECT 
                CASE 
                    WHEN Age BETWEEN 1 AND 10 THEN '1-10'
                    WHEN Age BETWEEN 11 AND 19 THEN '11-19'
                    WHEN Age BETWEEN 20 AND 29 THEN '20-29'
                    WHEN Age BETWEEN 30 AND 39 THEN '30-39'
                    WHEN Age BETWEEN 40 AND 49 THEN '40-49'
                    WHEN Age BETWEEN 50 AND 59 THEN '50-59'
                    WHEN Age BETWEEN 60 AND 69 THEN '60-69'
                    WHEN Age BETWEEN 70 AND 79 THEN '70-79'
                    WHEN Age BETWEEN 80 AND 89 THEN '80-89'
                    WHEN Age BETWEEN 90 AND 99 THEN '90-99'
                    WHEN Age BETWEEN 100 AND 109 THEN '100-109'
                    WHEN Age BETWEEN 110 AND 119 THEN '110-119'
                    WHEN Age BETWEEN 120 AND 124 THEN '120-124'
                    ELSE 'Unknown'
                END AS AgeRange
            FROM patient
        ) AS AgeRanges
        GROUP BY AgeRange
        ORDER BY Count DESC
        LIMIT 5";

$result = mysqli_query($conn, $sql);

// Check if the query was successful
if ($result) {
    // Initialize an array to store age range data
    $ageData = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $ageData[] = $row;
    }
} else {
    // Error executing the query
    echo "Error fetching age range data: " . mysqli_error($conn);
    exit;
}

// Query 8: Get gender count and percentages
$sql = "SELECT 
            SUM(CASE WHEN sex = 'Male' THEN 1 ELSE 0 END) AS MaleCount,
            SUM(CASE WHEN sex = 'Female' THEN 1 ELSE 0 END) AS FemaleCount
        FROM patient";

$result = $conn->query($sql);

// Initialize variables
$malePercentage = 0;
$femalePercentage = 0;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Calculate percentages
    $totalPatients = $row['MaleCount'] + $row['FemaleCount'];
    $malePercentage = ($totalPatients > 0) ? ($row['MaleCount'] / $totalPatients) * 100 : 0;
    $femalePercentage = ($totalPatients > 0) ? ($row['FemaleCount'] / $totalPatients) * 100 : 0;
}

// Set headers for Excel file download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Weekly_Report.xls");
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
        <tr>
            <th colspan="2">Top 5 Cities by Patient Count</th>
        </tr>
        <tr>
            <td>City</td>
            <td>City Count</td>
        </tr>
        <?php for ($i = 0; $i < count($cities); $i++): ?>
            <tr>
                <td><?php echo sanitizeForExcel($cities[$i]); ?></td>
                <td><?php echo sanitizeForExcel($counts[$i]); ?></td>
            </tr>
        <?php endfor; ?>
        <tr>
            <th colspan="2">Age Range Distribution</th>
        </tr>
        <tr>
            <td>Age Range</td>
            <td>Count</td>
        </tr>
        <?php foreach ($ageData as $age): ?>
            <tr>
                <td><?php echo sanitizeForExcel($age['AgeRange']); ?></td>
                <td><?php echo sanitizeForExcel($age['Count']); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th colspan="2">Gender Distribution</th>
        </tr>
        <tr>
            <td>Male Percentage (%)</td>
            <td><?php echo sanitizeForExcel(number_format($malePercentage, 2)); ?></td>
        </tr>
        <tr>
            <td>Female Percentage (%)</td>
            <td><?php echo sanitizeForExcel(number_format($femalePercentage, 2)); ?></td>
        </tr>
    </table>
</body>
</html>
