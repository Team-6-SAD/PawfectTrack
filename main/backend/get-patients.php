<?php
include 'pawfect_connect.php';
$sql = "
SELECT 
    PatientID, 
    CONCAT(FirstName, ' ', LastName) AS FullName
FROM 
    patient
WHERE 
    ActiveStatus = 'Active'
GROUP BY 
    PatientID, FullName
ORDER BY 
    FullName ASC
";

$result = mysqli_query($conn, $sql);

$patients = array();
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $patients[] = $row;
    }
}

echo json_encode($patients);
mysqli_close($conn);
?>