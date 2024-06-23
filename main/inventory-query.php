<?php
$sql = "SELECT 
            mu.UsageID,
            mu.Quantity,
            mu.Dosage,
            mu.UsageDate,
            mu.TreatmentID,
            mu.MedicineBrand,
            t.PatientID,
            p.FirstName,
            p.LastName
       
        FROM 
            medicineusage mu
        JOIN 
            treatment t ON mu.TreatmentID = t.TreatmentID
        JOIN 
            patient p ON t.PatientID = p.PatientID";
// Perform the query
$result = mysqli_query($conn, $sql);

// Check if the query was successful
if ($result) {
    // Initialize an empty array to store the query results
    $medicineUsageData = array();

    // Fetch the rows from the result set
    while ($row = mysqli_fetch_assoc($result)) {
        // Add each row to the medicine usage data array
        $medicineUsageData[] = $row;
    }

    // Now $medicineUsageData contains the fetched data
} else {
    // Error executing the query
    echo "Error: " . mysqli_error($conn);
}



$sql = "SELECT 
            SUM(mi.StockQuantity) AS TotalQuantity,
            mi.MedicineBrandID,
            mb.BrandName
        FROM 
            medicineinventory mi
        LEFT JOIN 
            medicinebrand mb ON mi.MedicineBrandID = mb.MedicineBrandID
        GROUP BY 
            mi.MedicineBrandID";

$result = $conn->query($sql);

if ($result) {
    // Initialize an empty array to store the query results
    $medicineInventoryData = array();

    // Fetch the rows from the result set
    while ($row = $result->fetch_assoc()) {
        // Add each row to the medicine inventory data array
        $medicineInventoryData[] = $row;
    }

    // Now $medicineInventoryData contains the fetched data
} else {
    // Error executing the query
    echo "Error: " . $conn->error;
}
