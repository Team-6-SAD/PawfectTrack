<?php
session_start();

// Check if the 'admin' session variable is not set or is false (user not logged in)
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true || !isset($_SESSION['adminID'])) {
    // Redirect the user to the login page
    header("Location: Admin Login.php");
    exit(); // Terminate the script
}

// Include your database connection file
require_once 'backend/pawfect_connect.php';

// Get the AdminID from the session
$adminID = $_SESSION['adminID'];

// Prepare and execute the SQL query to fetch admin information
$sql = "SELECT * FROM admininformation WHERE AdminID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $adminID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check if there is a row returned
if ($row = mysqli_fetch_assoc($result)) {
    // Admin information retrieved successfully
    $firstName = $row['firstname'];
    $middleName = $row['middlename'];
    $lastName = $row['lastname'];
    $email = $row['email'];
    $phoneNumber = $row['phonenumber'];
    $adminPhoto = $row['adminphoto'];

    // Now you can use these variables to display the admin information in your HTML
} else {
    // Admin information not found
    echo "Admin information not found!";
}
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
            eu.Quantity,
            eu.UsageDate,
            eu.EquipmentID,
            eu.EquipmentName AS EquipmentName
        FROM 
            equipmentusage eu";

// Perform the query
$result = mysqli_query($conn, $sql);

// Check if the query was successful
if ($result) {
    // Initialize an empty array to store the query results
    $equipmentUsageData = array();

    // Fetch the rows from the result set
    while ($row = mysqli_fetch_assoc($result)) {
        // Add each row to the equipment usage data array
        $equipmentUsageData[] = $row;
    }

    // Now $equipmentUsageData contains the fetched data
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


?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="img/Favicon 2.png" type="image/png">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel='stylesheet' href='https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css'>
    <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet"> 
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="css/hamburgers.css" rel="stylesheet">
    <link href="css/userdashboard.css" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
    <title>Inventory</title>
    <style>
            .form-control{
    font-size: 12px !important;
}

      input::placeholder {
    font-size: 12px; /* Adjust the font size as needed */
}
        .row-spacing {
            margin-bottom: 10px;
            /* Adjust the value to control the spacing between rows */
        }

        #TotalStock tbody tr.row-spacing td {
            border-top: 4px solid #FFFFFF !important;
        }

        table.dataTable.no-footer {
            border-bottom: none !important;
        }

        .card-body {
            border-radius: 8px !important;
        }

        #deleteButton {
            white-space: nowrap;
        }

        #editButton {
            white-space: nowrap;
        }

        #addEqButton {
            white-space: nowrap;
            margin-left: auto;
            /* Ensure it aligns to the far right */
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="main-container">
            <!-- Header and Sidebar -->
            <?php include 'includes/admin_header.php'; ?>
            <div class="sidebar">
                <?php include 'includes/sidebar.php'; ?>
            </div>
            <div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999; position:fixed;"></div>
            <?php





            $sql = "SELECT 
            SUM(es.Quantity) AS TotalQuantity,
            e.EquipmentID,
            e.Name
        FROM 
            equipmentstock es
        JOIN 
            equipment e ON es.EquipmentID = e.EquipmentID
        GROUP BY 
            es.EquipmentID";

            $result = $conn->query($sql);

            // Check if the query was successful
            if ($result) {
                // Initialize an empty array to store the query results
                $equipmentStockData = array();

                // Fetch the rows from the result set
                while ($row = $result->fetch_assoc()) {
                    // Add each row to the equipment stock data array
                    $equipmentStockData[] = $row;
                }

                // Now $equipmentStockData contains the fetched data
            } else {
                // Error executing the query
                echo "Error: " . $conn->error;
            }
            ?>

            <!--Profile Picture and Details-->
            <div class="content" id="content">
                <div class="row">

                    <div class="  col-sm-12 col-md-10 col-lg-11 mt-2 mx-auto mb-4 ">
                        <div class="card">
                            <div class="card-body ml-3">
                                <p class="h4 font-weight-bold main-color"> INVENTORY </p>
                                <small> Take control of your clinic’s inventory. Gain clear insights, improve efficiency, <br></small>
                                <small> and ensure the highest level of patient care. </small>
                            </div>
                        </div>
                    </div>

                    <div class="  col-sm-12 col-md-10 col-lg-11 mt-2 mx-auto mb-4 justify-content-center align-items-center d-flex">
                        <a href="Inventory.php" style="color:#0449a6;" class="mr-5">Medicine </a>
                        <a href="equipment-inventory.php" class="btn btn-primary py-2 btn-inventory ml-5">Equipment </a>
                    </div>

                    <div class="  col-sm-12 col-md-10 col-lg-11 mt-2 mx-auto mb-4 ">
                        <div class="row justify-content-center mb-0">

                            <div class="col-md-6 col-lg-6 mt-2">
                                <div class="card table-card h-100">
                                    <div class="card-body bg-main-color-2 p-5">
                                    <div class="col-md-12 col-lg-12 col-xl-12 d-flex align-items-center p-0">
                                    <div class="d-flex justify-content-between align-items-center mb-2 ml-2 mt-1 w-100">

    <!-- Delete button next to Edit -->
    <button id="deleteButton" class="btn btn-danger mr-3">
        <img src="img/img-dashboard/white-subtract.png" alt="Icon" style="width: 17px; height: 17px; margin-right: 7px;">Remove
    </button>
    <!-- Add Equipment button pushed to the end -->
    <button id="addEqButton" class="btn greener no-break ml-auto" data-toggle="modal" data-target="#addEquipmentModal"><img src="img/img-dashboard/white-add.png" alt="Icon" class="mr-2" style="width: 20px; height: 20px; margin-right: 3px; margin-bottom:1px;">Add Equipment</button>
</div>
</div>


                                            

                                        <form id="deleteForm" action="backend/remove_equipment.php" method="post">
                                            <input type="hidden" name="selectedRows[]" id="selectedRowsInput">
                                        </form>

                                        <table id="EquipmentTable" class="table table-striped">
    <thead class="table-header">
        <tr>
            <th>Product Name</th>
            <th>Quantity</th>
        </tr>
    </thead>
    <tbody>
        <!-- Table rows -->
        <?php
        $sql = "SELECT e.EquipmentID, e.Name AS EquipmentName, SUM(es.Quantity) AS TotalQuantity
                FROM equipment e
                LEFT JOIN equipmentstock es ON e.EquipmentID = es.EquipmentID
                GROUP BY e.EquipmentID, e.Name";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr data-id='" . $row['EquipmentID'] . "'>";
                echo "<td>" . $row['EquipmentName'] . "</td>";
                echo "<td>" . $row['TotalQuantity'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No equipment data available</td></tr>";
        }
        ?>
        <!-- More rows here -->
    </tbody>
</table>

                                    </div>

                                </div>
                            </div>

                            <div class="col-md-6 col-lg-6 mt-2">
                                <div class="card table-card h-100">
                                    <div class="card-body bg-main-color-2 p-5">
     <div class="medicine-header justify-content-start">
                                      <img src="img/img-dashboard/Usage History Image.png" class="mr-2" style="height:20px; width:auto;">
                                        <h5 class="main-color mb-0 pb-0 font-weight-normal" style="color:#5E6E82;">Equipment Usage History</h5>

                                    </div>



                                        <table id="EquipmentUsageTable" class="table table-striped">
                                            <thead class="table-header">
                                                <tr>
                                                    <th>Product Name</th>
                                                    <th>Quantity</th>
                                                    <th>Usage Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Loop through each row of equipment usage data
                                                foreach ($equipmentUsageData as $row) {
                                                    echo "<tr>";
                                                    echo "<td>" . $row['EquipmentName'] . "</td>";
                                                    echo "<td>" . $row['Quantity'] . "</td>";
                                                    echo "<td>" . $row['UsageDate'] . "</td>";
                                                    echo "</tr>";
                                                }

                                                // Check if no data is available
                                                if (empty($equipmentUsageData)) {
                                                    echo "<tr><td></td><td>No equipment data available</td><td></td></tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>


                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="  col-sm-12 col-md-10 col-lg-11 mt-2 mx-auto mb-5 ">
                        <div class="row mb-5">
                            <div class="col-md-12 justify-content-center">
                                <div class="card justify-content-center d-flex align-items-center mb-3 h-100">
                                    <div class="d-flex align-items-center justify-content-center px-5 pt-4 pb-3">
                                        <img src="img/img-dashboard/medicine-img.png" alt="Medicine Image" style="height: 65px; margin-right: 10px;">
                                        <div>
                                            <p class="h4 text-left font-weight-normal m-0 main-color">Equipment Stock</p>
                                            <p class="h4 text-left font-weight-normal m-0 main-color">Indicator</p>
                                        </div>
                                    </div>

                                    <div class="card-body col-md-10 justify-content-center align-items-center bg-main-color-2 m-0 p-0 px-5  py-4 ">
                                        <div>
                                            <canvas id="myChart2" height="10" width="30"></canvas>
                                        </div>

                                    </div>
                                    <div class="text-left px-0 py-4 pb-5" style="border-top: 2px solid #0449a6;">
                                        <small>Equipment Stock Indicator is designed to provide you with real-time insights and efficient management<br></small>
                                        <small>tools, ensuring your clinic is always well-equipped to serve patients effectively.</small>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




        <div class="modal fade" id="addMedicineModal" tabindex="-1" role="dialog" aria-labelledby="addMedicineModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMedicineModalLabel">Add Medicine</h5>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Form for adding medicine -->
                        <form id="addMedicineForm" action="backend/add_medicine.php" method="post">
                            <div class="form-group">
                                <?php
                                if (isset($_SESSION['productNameExists']) && $_SESSION['productNameExists'] === true) {
                                    echo '<div class="alert alert-danger" role="alert">Product Name already exists</div>';
                                    unset($_SESSION['productNameExists']); // Clear the session variable
                                }
                                ?>
                                <?php
                                if (isset($_SESSION['productBrandExists']) && $_SESSION['productBrandExists'] === true) {
                                    echo '<div class="alert alert-danger" role="alert">Product Brand already exists</div>';
                                    unset($_SESSION['productBrandExists']); // Clear the session variable
                                }
                                ?>
                                <label for="productName">Product Name</label>
                                <input type="text" class="form-control" id="productName" name="productName" required oninput="preventLeadingSpace(event)">
                            </div>
                            <div class="form-group">
                                <label for="productBrand">Product Brand</label>
                                <input type="text" class="form-control" id="productBrand" name="productBrand" required oninput="prevenLeadingtSpace(event)">
                            </div>
                            <div class="form-group">
                                <label for="route">Route</label>
                                <select class="form-control" id="route" name="route" required>
                                    <option value="">Select Route</option>
                                    <option value="Intramuscular">Intramuscular</option>
                                    <option value="Intradermal">Intradermal</option>
                                </select>
                            </div>
                            <div class="justify-content-center align-items-center d-flex">
                                <button type="submit" style="background-color:#10AC84; font-weight:bold;" class="btn btn-success px-4">List Medicine</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="successModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Success!</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <!-- Modal body -->
                    <div class="modal-body">
                        <p>The medicine has been listed successfully.</p>
                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Already Exists Modal -->
        <div class="modal" id="alreadyExistsModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Already Exists!</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <!-- Modal body -->
                    <div class="modal-body">
                        <p>The product brand already exists for this medicine.</p>
                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addStockModal" tabindex="-1" role="dialog" aria-labelledby="addStockModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="addStockForm" method="post" action="backend/add_stock.php">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addStockModalLabel">Add Stock</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="brandName">Brand Name:</label>
                                        <select class="form-control" id="brandName" name="brandName" required>
                                            <option value="">Select Brand Name</option>
                                            <?php
                                            // Fetch all available brand names from the database
                                            $sql = "SELECT * FROM medicinebrand";
                                            $result = $conn->query($sql);

                                            // Check if there are any rows returned
                                            if ($result->num_rows > 0) {
                                                // Output data of each row as options in the select element
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<option value='" . $row['MedicineBrandID'] . "'>" . $row['BrandName'] . "</option>";
                                                }
                                            } else {
                                                echo "<option value=''>No brands available</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- Stock Quantity input -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="stockQuantity">Stock Quantity:</label>
                                        <input type="number" class="form-control" id="stockQuantity" name="stockQuantity" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="stockPrice">Stock Price:</label>
                                        <input type="number" class="form-control" id="stockPrice" name="stockPrice" required max="1000000">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="stockDosage">Stock Dosage:</label>
                                        <input type="text" class="form-control" id="stockDosage" name="stockDosage" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="stockExpiryDate">Stock Expiry Date:</label>
                                        <input type="date" class="form-control" id="stockExpiryDate" name="stockExpiryDate" required min="<?php echo date('Y-m-d', strtotime('+7 days')); ?>" onkeydown="return false">
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="stockBoughtPrice">Stock Bought Price:</label>
                                        <input type="number" class="form-control" id="stockBoughtPrice" name="stockBoughtPrice" required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-center align-items-center d-flex">
                                <button type="submit" style="background-color:#10AC84; font-weight:bold;" class="btn btn-success px-4">Add Stock</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Equipment Modal -->
    <div class="modal fade" id="addEquipmentModal" tabindex="-1" role="dialog" aria-labelledby="addEquipmentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="addEquipmentForm" method="post" action="backend/add_equipment.php"> <!-- Form starts here -->
                    <div class="modal-header">
                        <h5 class="modal-title p-3" id="addEquipmentModalLabel"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Equipment Name Input -->
                        <div class="form-group">
                          <label for="equipmentName"><small><b>Product Name</b></small></label>
                            <input type="text" class="form-control" id="equipmentName" placeholder="Product Name" name="equipmentName" required oninput="preventLeadingSpace(event)" maxlength="60">
                        </div>
                        <!-- Quantity Input -->
                        <div class="form-group">
                            <label for="quantity"><small><b>Product Quantity</b></small></label>
                            <input type="number" class="form-control" id="quantity" placeholder="Product Quantity(pcs)" name="quantity" required>
                        </div>
                        <!-- Price Bought Input -->
                        <div class="form-group">
                          <label for="priceBought"><small><b>Product Price</b></small></label>
                            <input type="number" class="form-control" id="priceBought" placeholder="Product Price" name="priceBought" required>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center d-flex align-items-center" style="border-top:none">
                        <button type="submit" class="btn btn-success px-5" style="background-color: #10AC84 !important; border-radius:27.5px !important;">Add Equipment</button> <!-- Submit button added -->
                    </div>
                </form> <!-- Form ends here -->
            </div>
        </div>
    </div>
    <div class="modal fade" id="addMedicineSuccessModal" tabindex="-1" role="dialog" aria-labelledby="usernamePasswordMismatchModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="usernamePasswordMismatchModalLabel"></h5>
                    <i data-feather="x-circle" class="text-end featherer" data-dismiss="modal">

                    </i>
                </div>
                <div class="modal-body">
                    <div class="justify-content-center d-flex">
                        <img src="img/img-alerts/check-mark.png">
                    </div>
                    <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>MEDICINE</b></h2>
                    <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>ADDED</b></h2>
                    <div class="text-center">
                        <small style="letter-spacing: -1px; color:#5e6e82;">Medicine has been added successfully.<br></small>

                    </div>
                    <div class="align-items-center justify-content-center d-flex mb-3 mt-3">
                        <button type="button" style="background-color: #1DD1A1; border:none;" class="btn btn-success px-5 py-2" data-dismiss="modal"><b>OK</b></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addStockSuccessModal" tabindex="-1" role="dialog" aria-labelledby="usernamePasswordMismatchModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="usernamePasswordMismatchModalLabel"></h5>
                    <i data-feather="x-circle" class="text-end featherer" data-dismiss="modal">

                    </i>
                </div>
                <div class="modal-body">
                    <div class="justify-content-center d-flex">
                        <img src="img/img-alerts/check-mark.png">
                    </div>
                    <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>STOCK</b></h2>
                    <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>ADDED</b></h2>
                    <div class="text-center">
                        <small style="letter-spacing: -1px; color:#5e6e82;">Stock has been added successfully.<br></small>
                    </div>
                    <div class="align-items-center justify-content-center d-flex mb-3 mt-3">
                        <button type="button" style="background-color: #1DD1A1; border:none;" class="btn btn-success px-5 py-2" data-dismiss="modal"><b>OK</b></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addEquipmentSuccessModal" tabindex="-1" role="dialog" aria-labelledby="usernamePasswordMismatchModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="usernamePasswordMismatchModalLabel"></h5>
                    <i data-feather="x-circle" class="text-end featherer" data-dismiss="modal">

                    </i>
                </div>
                <div class="modal-body">
                    <div class="justify-content-center d-flex">
                        <img src="img/img-alerts/check-mark.png">
                    </div>
                    <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>EQUIPMENT</b></h2>
                    <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>ADDED</b></h2>
                    <div class="text-center">
                        <small style="letter-spacing: -1px; color:#5e6e82;">Equipment has been added successfully.<br></small>
                    </div>
                    <div class="align-items-center justify-content-center d-flex mb-3 mt-3">
                        <button type="button" style="background-color: #1DD1A1; border:none;" class="btn btn-success px-5 py-2" data-dismiss="modal"><b>OK</b></button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="removalConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="usernamePasswordMismatchModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title p-3" id="usernamePasswordMismatchModalLabel"></h5>

                </div>
                <div class="modal-body">
                    <div class="justify-content-center d-flex">
                        <img src="img/img-alerts/caution-mark.png" style="height:50px; width:auto;">
                    </div>
                    <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>REMOVE</b></h2>
                    <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>ITEM</b></h2>
                    <div class="text-center">
                        <small style="letter-spacing: -1px; color:#5e6e82;">Are you sure you want to delete<br></small>
                        <small style="letter-spacing: -1px; color:#5e6e82;">the selected item/s?<br></small>
                    </div>
                    <div class="align-items-center justify-content-center d-flex mb-3 mt-3">
                        <button type="button" style="background-color: none; border:none;" class="btn px-3 mr-4 py-2" data-dismiss="modal">Cancel</button>
                        <button type="button" style="background-color: #EE5253; border:none; border-radius:27.5px !important;" class="btn btn-success px-3 py-2 font-weight-bold" id="confirmDeleteButton">Remove</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <!-- Data Table JS -->

    <!-- Data Table JS -->
    <script src='https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js'></script>

    <script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>


    <!-- ... (your existing script imports) ... -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/notifications.js"></script>
    <script>
        feather.replace();
    </script>

    <script>
        $(document).ready(function() {
            <?php if (isset($_SESSION['successMessage'])) { ?>
                $('#addMedicineSuccessModal').modal('show');
                <?php unset($_SESSION['successMessage']); ?> // Unset the session variable
            <?php } ?>
        });
    </script>
    <script>
        $(document).ready(function() {
            <?php if (isset($_SESSION['successMessageStock'])) { ?>
                $('#addStockSuccessModal').modal('show');
                <?php unset($_SESSION['successMessageStock']); ?> // Unset the session variable
            <?php } ?>
        });
    </script>
    <script>
        $(document).ready(function() {
            <?php if (isset($_SESSION['successMessageEquipment'])) { ?>
                $('#addEquipmentSuccessModal').modal('show');
                <?php unset($_SESSION['successMessageEquipment']); ?> // Unset the session variable
            <?php } ?>
        });
    </script>


    <script>
        $(document).ready(function() {
            // Check if the session variable for alreadyExists is set to true
            <?php if (isset($_SESSION['alreadyExists']) && $_SESSION['alreadyExists'] === true) : ?>
                // Show the Already Exists modal
                $('#alreadyExistsModal').modal('show');
                // Remove the session variable
                <?php unset($_SESSION['alreadyExists']); ?>
            <?php endif; ?>

            // Check if the session variable for success is set to true
            <?php if (isset($_SESSION['success']) && $_SESSION['success'] === true) : ?>
                // Show the Success modal
                $('#successModal').modal('show');
                // Remove the session variable
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
        });
    </script>

    <?php


    // Extract labels and data from the fetched data
    $labels = array_column($medicineInventoryData, 'BrandName'); // Extract BrandName as labels
    $data = array_column($medicineInventoryData, 'TotalQuantity'); // Extract TotalQuantity as data

    // Convert PHP arrays to JavaScript arrays
    $labels_js = json_encode($labels);
    $data_js = json_encode($data);
    ?>
    <script>
        const ctx2 = document.getElementById('myChart2');

        // Extracting data from PHP and formatting for the chart
        const equipmentStockData = <?php echo json_encode($equipmentStockData); ?>;

        // Extract the names and quantities
        const labels = equipmentStockData.map(item => item.Name);
        const data = equipmentStockData.map(item => item.TotalQuantity);

        // Define a set of colors for each bar
        const backgroundColors = [
            'rgba(255, 99, 132, 1)', // Red
            'rgba(54, 162, 235, 1)', // Blue
            'rgba(255, 206, 86, 1)', // Yellow
            'rgba(75, 192, 192, 1)', // Green
            'rgba(153, 102, 255, 1)', // Purple
            'rgba(255, 159, 64, 1)', // Orange
            'rgba(199, 199, 199, 1)' // Grey
        ];

        const borderColors = [
            'rgba(255, 99, 132, 1)', // Red
            'rgba(54, 162, 235, 1)', // Blue
            'rgba(255, 206, 86, 1)', // Yellow
            'rgba(75, 192, 192, 1)', // Green
            'rgba(153, 102, 255, 1)', // Purple
            'rgba(255, 159, 64, 1)', // Orange
            'rgba(199, 199, 199, 1)' // Grey
        ];

        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: labels, // Use equipment names as labels
                datasets: [{
                    label: 'Equipment Stock', // Single label for the entire dataset
                    data: data, // Use equipment quantities as data
                    backgroundColor: backgroundColors, // Apply the background colors
                    borderColor: borderColors, // Apply the border colors
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: true, // Show the legend
                        position: 'bottom', // Position the legend on the right side
                        labels: {
                            generateLabels: function(chart) {
                                // Custom labels based on data
                                return labels.map((label, index) => {
                                    const dataset = chart.data.datasets[0];
                                    return {
                                        text: label,
                                        fillStyle: dataset.backgroundColor[index], // Use the corresponding color
                                        hidden: isNaN(dataset.data[index]),
                                        lineCap: dataset.borderCapStyle,
                                        lineDash: dataset.borderDash,
                                        lineDashOffset: dataset.borderDashOffset,
                                        lineJoin: dataset.borderJoinStyle,
                                        strokeStyle: dataset.borderColor[index], // Use the corresponding color
                                        pointStyle: dataset.pointStyle,
                                        rotation: 0
                                    };
                                });
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            title: (tooltipItem) => labels[tooltipItem[0].dataIndex], // Show label in tooltip
                            label: (tooltipItem) => 'Quantity: ' + tooltipItem.raw // Show quantity in tooltip
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
                  <script>
    document.getElementById('quantity').addEventListener('input', function (e) {
        // Remove any non-numeric characters
        this.value = this.value.replace(/[^0-9]/g, '');

        // Ensure the value does not start with 0 unless it is the only character
        if (this.value.length > 1 && this.value.startsWith('0')) {
            this.value = this.value.replace(/^0+/, '');
        }

        // Limit to 6 digits
        if (this.value.length > 6) {
            this.value = this.value.slice(0, 6);
        }
    });
</script>
<script>
    document.getElementById('priceBought').addEventListener('input', function (e) {
        // Remove any non-numeric characters
        this.value = this.value.replace(/[^0-9]/g, '');

        // Ensure the value does not start with 0 unless it is the only character
        if (this.value.length > 1 && this.value.startsWith('0')) {
            this.value = this.value.replace(/^0+/, '');
        }

        // Limit to 6 digits
        if (this.value.length > 6) {
            this.value = this.value.slice(0, 8);
        }
    });
</script>

    <script>
        document.getElementById("profileDropdown").addEventListener("mousedown", function(event) {
            event.preventDefault(); // Prevent the default action of the mousedown event
            var dropdownContent = document.getElementById("dropdownContent");

            // Check if the clicked element is within the dropdown content
            if (!dropdownContent.contains(event.target)) {
                // Clicked outside the dropdown content, toggle its visibility
                if (dropdownContent.style.display === "block") {
                    dropdownContent.style.display = "none";
                } else {
                    dropdownContent.style.display = "block";
                }
            }
        });
    </script>


    <script>
        // Get all sidebar items
        const sidebarItems = document.querySelectorAll('.sidebar-item');

        // Loop through each sidebar item
        sidebarItems.forEach(function(sidebarItem) {
            // Find the icon within the sidebar item
            const sidebarIcon = sidebarItem.querySelector('.sidebar-icon');

            // Get the paths to the default and hover icons from data attributes
            const defaultIcon = sidebarItem.dataset.defaultIcon;
            const hoverIcon = sidebarItem.dataset.hoverIcon;

            // Add mouseenter event listener
            sidebarItem.addEventListener('mouseenter', function() {
                // Change the source of the icon to the hover icon upon hover
                sidebarIcon.src = hoverIcon;
            });

            // Add mouseleave event listener
            sidebarItem.addEventListener('mouseleave', function() {
                // Change the source of the icon back to the default icon upon mouse leave
                sidebarIcon.src = defaultIcon;
            });
        });

        $(document).ready(function() {
            $('#sidebarCollapse1').on('click', function() {
                $('#sidebar').toggleClass('collapsed'); // Toggle 'collapsed' class on #sidebar
                $('#content').toggleClass('collapsed'); // Toggle 'collapsed' class on #content
            });
        });
    </script>


  

<script>
        $('.select-Equip').hide();
$(document).ready(function() {
    // DataTable initialization
    var table = $('#EquipmentTable').DataTable({
        paging: true,
        responsive: true,
        searching: true,
        pageLength: 5,
        lengthMenu: [[5, 25, 50, -1], [5, 25, 50, "All"]],
        dom: "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-12 ml-5 mt-3'>><<'col-sm-12'lp>>",
        columnDefs: [
            { orderable: false, targets: 0 } // Disable sorting for the first column (index 0)
        ],
        language: {
            lengthMenu: "Display _MENU_"
        }
    });

    // Function to update page information
    function updatePageInfo() {
        var pageInfo = table.page.info();
        var currentPage = pageInfo.page + 1; // Convert zero-based index to one-based index
        var totalPages = pageInfo.pages;
        var pageLengthContainer = $('.dataTables_length');

        // Update the page information
        pageLengthContainer.find('.page-info').remove();
        pageLengthContainer.append('<span class="page-info" style="margin-left: 10px;">Page <b>' + currentPage + '</b> of <b>' + totalPages + '</b></span>');
    }

    // Initial update of page information
    updatePageInfo();

    // Update page information whenever the table is redrawn
    table.on('draw', function() {
        updatePageInfo();
    });



 
 

 




        // Link custom search input with DataTable
        var customSearchInput = $('#customSearchInput');
    customSearchInput.on('input', function() {
        table.search(this.value).draw();
    });
    // Handle "View" button click

});

$(document).ready(function() {
    const deleteButton = $('#deleteButton');
    const rows = $('#EquipmentTable tbody tr');
    let selectedRows = [];

    rows.on('click', function() {
        const row = $(this);
        const id = row.data('id');

        if (row.hasClass('selected')) {
            row.removeClass('selected');
            selectedRows = selectedRows.filter(rowId => rowId !== id);
        } else {
            row.addClass('selected');
            selectedRows.push(id);
        }
    });

    deleteButton.on('click', function() {
        if (selectedRows.length === 0) {
            alert('Please select a row for deletion.');
        } else {
            // Show the confirmation modal
            $('#removalConfirmationModal').modal('show');
        }
    });

    $('#confirmDeleteButton').on('click', function() {
        $.ajax({
            type: 'POST',
            url: 'backend/remove_equipment.php',
            contentType: 'application/json',
            data: JSON.stringify({ selectedRows: selectedRows }),
            success: function(response) {
                if (response.success) {
                    selectedRows.forEach(id => {
                        $(`#EquipmentTable tbody tr[data-id="${id}"]`).remove();
                        location.reload();
                    });
                    selectedRows = [];
                    $('#removalConfirmationModal').modal('hide');
                    location.reload();
                } else {
                    location.reload();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });
});


                </script>
<script>
    $(document).ready(function() {
        // DataTable initialization for EquipmentUsageTable
        var table = $('#EquipmentUsageTable').DataTable({
            paging: true,
            responsive: true,
            searching: true,
            "pageLength": 5, // Set default page length
            "lengthMenu": [
                [5, 25, 50, -1],
                [5, 25, 50, "All"]
            ], // Customize page length menu
            "dom": // Place search input at the top
                "<'row'<'col-sm-12't>>" + // Place table in a row
                "<<'col-sm-12 justify-content-center d-flex mt-5'p>>", // Place length menu and pagination in separate rows
            buttons: [{
                    extend: 'copyHtml5',
                    text: '<img style="width:25px; height:25px;" src="copy_image.png" alt="Copy">',
                    titleAttr: 'Copy',
                    className: 'btn-img'
                },
                {
                    extend: 'excelHtml5',
                    text: '<img style="width:25px; height:25px;" src="excel_image.png" alt="Excel">',
                    titleAttr: 'Excel',
                    className: 'btn-img'
                },
                {
                    extend: 'csvHtml5',
                    text: '<img style="width:25px; height:25px;" src="csv_image.png" alt="CSV">',
                    titleAttr: 'CSV',
                    className: 'btn-img'
                },
                {
                    extend: 'pdfHtml5',
                    text: '<img style="width:25px; height:25px;" src="pdf_image.png" alt="PDF">',
                    titleAttr: 'PDF',
                    className: 'btn-img'
                }
            ],
            language: {
                "lengthMenu": "Display _MENU_ "
            },
"drawCallback": function(settings) {
    if (settings.nTable.id !== 'EquipmentUsageTable') return;

    var api = this.api();
    var pages = api.page.info().pages;
    var page = api.page.info().page;
    var paginationButtons = $(api.table().container()).find('.dataTables_paginate').empty();

    // Display Previous button
    if (page > 0) {
        paginationButtons.append('<a class="paginate_button previous" data-dt-idx="' + (page - 1) + '" tabindex="0">Previous</a>');
    }

    // Display The First Page button with numerical value
    paginationButtons.append('<a class="paginate_button first" data-dt-idx="0" tabindex="0">1</a>');

    // Display Current page number
    paginationButtons.append('<span class="paginate_button current">' + (page + 1) + '</span>');

    // Display The Last Page button with numerical value
    paginationButtons.append('<a class="paginate_button last" data-dt-idx="' + (pages - 1) + '" tabindex="0">' + pages + '</a>');

    // Display Next button
    if (page < pages - 1) {
        paginationButtons.append('<a class="paginate_button next" data-dt-idx="' + (page + 1) + '" tabindex="0">Next</a>');
    }

    // Handle pagination button click events
    paginationButtons.find('a.paginate_button').on('click', function() {
        api.page(parseInt($(this).data('dt-idx'))).draw(false);
    });
}


        });
    });
</script>





    <script>
        function preventLeadingSpace(event) {
            const input = event.target;
            if (input.value.startsWith(' ')) {
                input.value = input.value.trim(); // Remove leading space
            }
            // Replace multiple consecutive spaces with a single space
            input.value = input.value.replace(/\s{2,}/g, ' ');
        }

        function preventSpaces(event) {
            const input = event.target;
            if (input.value.includes(' ')) {
                input.value = input.value.replace(/\s/g, ''); // Remove all spaces
            }
        }
    </script>
    <script>
        // Get reference to the input element
        var stockPriceInput = document.getElementById('stockPrice');

        // Add event listener for input change
        stockPriceInput.addEventListener('input', function() {
            // Parse the value of the input to a number
            var stockPrice = parseFloat(stockPriceInput.value);

            // Check if the value is negative
            if (stockPrice < 0) {
                // If negative, replace the value with an empty string
                stockPriceInput.value = '';
            }
        });
    </script>
</body>

</html>