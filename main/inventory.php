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
include 'inventory-query.php';
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
<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="css/hamburgers.css" rel="stylesheet">
    <link href="css/userdashboard.css" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
    <title>Inventory</title>
    <style>
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
            /* Style for selected row */
            .selected {
            background-color: #5e6e82 !important;
            border: 1px solid black !important;
        }
        .hidden {
            display: none;
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
                        <a href="Inventory.php" class="btn btn-primary py-2 btn-inventory mr-5">Medicine </a>
                        <a href="equipment-inventory.php" style="color:#0449a6;" class="ml-5">Equipment </a>
                    </div>
                    <div class="  col-sm-12 col-md-10 col-lg-11 mt-2 mx-auto mb-4 ">
                        <div class="card mx-auto mb-3">




                            <div class="card-body bg-main-color-2 p-5">
                                <div id="buttonContainer" class="d-flex flex-column flex-sm-row align-items-center mb-2 ml-2 mt-1">
                                    <!-- Edit button on the left -->

                                    <button id="editButton" class="btn btn-gray-color btn-custom  mr-3 ml-0 " style="color:white;  border-radius: 6px; font-weight: 400;">
                                        Action <span style="font-size: 8px; vertical-align: middle;"> &#9654; </span>
                                    </button>
                                    <!-- Additional buttons next to Edit -->
                                    <div class="d-flex flex-row flex-wrap align-items-center">


                                        <button id="deleteButton" class="btn btn-danger"><img src="img/img-dashboard/white-subtract.png" alt="Icon" style="width: 17px; height: 17px; margin-right: 7px;">Remove</button>

                                    </div>
                                    <div class="ml-auto">
        <button id="addMedicineButton" class="btn greener mb-0 mr-sm-2 pt-2 no-break" data-toggle="modal" data-target="#addStockModal">Add Stock</button>
    </div>
                                </div>

                                <form id="deleteForm" action="backend/remove_medicine.php" method="post">
                                    <input type="hidden" name="selectedRows[]" id="selectedRowsInput">
                                </form>

                                <table id="example" class="table table-striped">
                                    <thead class="table-header no-break sm-text text-left">
                                        <tr>
                                            <th class="px-3"> <input type="checkbox" id="selectAllCheckbox"></th>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                            <th>Price Sold</th>
                                            <th>Expiry Date</th>
                                            <th>Price Bought</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT 
mb.MedicineBrandID,
mb.BrandName,
mb.Route,
SUM(mi.StockQuantity) AS TotalStockQuantity,
mi.InventoryID,
mi.StockPrice,
mi.StockDosage,
mi.StockExpiryDate,
mi.StockBoughtPrice
FROM 
medicineinventory mi
JOIN 
medicinebrand mb ON mi.MedicineBrandID = mb.MedicineBrandID
GROUP BY 
mb.BrandName, mi.StockExpiryDate;
"; // Group by MedicineID, BrandName, and StockExpiryDate

                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            // Output data of each row
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td class='px-3'><input type='checkbox' class='select-checkbox' name='selectedRows[]' value='" . $row['InventoryID'] . "'></td>";
                                                echo "<td>" . $row['BrandName'] . "</td>";
                                                echo "<td>" . ($row['TotalStockQuantity'] !== null ? $row['TotalStockQuantity'] : 'N/A') . "</td>";
                                                echo "<td>" . ($row['StockPrice'] !== null ? $row['StockPrice'] : 'N/A') . "</td>";
                                                echo "<td>" . ($row['StockExpiryDate'] !== null ? $row['StockExpiryDate'] : 'N/A') . "</td>";
                                                echo "<td>" . ($row['StockBoughtPrice'] !== null ? $row['StockBoughtPrice'] : 'N/A') . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                        }

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




                                    </tbody>
                                </table>
                            </div>
                        </div>




                        <div class="row justify-content-center mb-3">
                            <div class="col-md-6 col-lg-6 mt-2">
                                <div class="card table-card h-100">
                                    <div class="card-body bg-main-color-2 p-4">
                                        <div class="medicine-header m-0">
                                        <button id="actionButton" class="btn btn-gray-color action-button" style="color:white;  border-radius: 6px; font-weight: 400;">Action  <span style="font-size: 8px; vertical-align: middle;"> &#9654; </span></button>
                                        <button id="deleteButton2" class="btn btn-danger action-button hidden">Delete Selected</button>
                                            <button id="addMedicineButton" class="btn greener mb-0  mr-sm-2 pt-2 no-break" data-toggle="modal" data-target="#addMedicineModal">Add Medicine</button>
                                        </div>


                                        <table id="TotalStock" class="table table-with-spacing gfg w-100">
                                            <thead>
                                                <tr>
                                                    <th class="d-none">Medicine Name</th>
                                                    <th class="d-none">Brand Name</th>
                                                    <th class="d-none">Total Stock Quantity</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sql = "SELECT 
                    m.MedicineID,
                mb.MedicineBrandID,
                    m.MedicineName,
                    mb.BrandName,
                    SUM(COALESCE(mi.StockQuantity, 0)) AS TotalStockQuantity
                FROM 
                    medicinebrand mb
                JOIN 
                    medicine m ON mb.MedicineID = m.MedicineID
                LEFT JOIN
                    medicineinventory mi ON mb.MedicineBrandID = mi.MedicineBrandID
                 GROUP BY
                m.MedicineID, mb.MedicineBrandID, m.MedicineName, mb.BrandName";

                                                $result = $conn->query($sql);

                                                if ($result === false) {
                                                    // Handle query execution error
                                                    echo "<tr><td colspan='3'>Error executing query: " . $conn->error . "</td></tr>";
                                                } else {
                                                    if ($result->num_rows > 0) {
                                                        // Output data of each row
                                                        while ($row = $result->fetch_assoc()) {
                                                            echo "<tr class='row-spacing' style='background-color:#f5f5f5;' data-medicine-id='" . $row['MedicineID'] . "' data-medicine-brand-id='" . $row['MedicineBrandID'] . "'>";
                                                            echo "<td>" . $row['MedicineName'] . "</td>";
                                                            echo "<td>" . $row['BrandName'] . "</td>";
                                                            echo "<td>" . $row['TotalStockQuantity'] . "</td>"; // Display total stock quantity
                                                            echo "</tr>";
                                                        }
                                                    } else {
                                                        // Output a single row with a message when no data is available
                                                        echo "<tr><td></td>
                <td >No data available</td>
                <td ></td></tr>";
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>




                                    </div>
                                </div>
                            </div>






                            <div class="col-md-6 col-lg-6 mt-2">
    <div class="row">
        <div class="col-md-12 justify-content-center">
            <div class="card justify-content-center d-flex align-items-center mb-3 h-100">
            <div class="d-flex align-items-center justify-content-center px-5 pt-4 pb-3">
    <img src="img/img-dashboard/medicine-img.png" alt="Medicine Image" style="height: 65px; margin-right: 10px;">
    <div>
        <p class="h4 text-left font-weight-normal m-0 main-color">Medicine Stock</p>
        <p class="h4 text-left font-weight-normal m-0 main-color">Indicator</p>
    </div>
</div>

                <div class="card-body col-md-10 justify-content-center d-flex align-items-center mx-h-400 bg-main-color-2 m-0 p-0  pb-4 border-bottom " style="border-bottom-left-radius: 0 !important;
    border-bottom-right-radius: 0 !important; border-color:#0449a6 !important; border-width:2px !important;">
                    <div class="d-flex justify-content-center mx-h-300 align-items-center pt-4">
                        <canvas id="myChart1"></canvas>
                    </div>
                </div>
                <div class="text-left px-5 py-3">
        <small>Monitor your medicine stock levels in real-time,<br></small>
        <small>ensuring that you are always aware of your current</small>
        <small>inventory status.</small>
    </div>
            </div>
            
        </div>
    </div>
</div>
</div>

                        <div class="row">
                            <div class="  col-sm-12 col-md-12 col-lg-12 mt-2 mx-auto mb-3 ">
                                <div class="card mx-auto table-card p-5">




                                    <div class="medicine-header">
                                        <h5 class="main-color"> <b>Medicine Usage History</b></h5>

                                    </div>

                                    <table id="UsageTable" class="table table-striped">
                                        <thead class="table-header no-break text-center">
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Quantity</th>
                                                <th>Dosage</th>
                                                <th>Date Used</th>
                                                <th>Patient Involved</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Table rows -->
                                            <?php
                                            // Loop through the fetched medicine usage data and output each row as a table row
                                            foreach ($medicineUsageData as $row) {
                                                echo "<tr>";
                                                echo "<td>" . $row['MedicineBrand'] . "</td>";
                                                echo "<td>" . $row['Quantity'] . "</td>";
                                                echo "<td>" . $row['Dosage'] . "</td>";
                                                echo "<td>" . $row['UsageDate'] . "</td>";
                                                echo "<td>" . $row['FirstName'] . " " . $row['LastName'] . "</td>";
                                                echo "</tr>";
                                            }
                                            ?>
                                            <!-- ... other rows ... -->
                                        </tbody>
                                    </table>

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
    <input type="number" class="form-control" id="stockQuantity" name="stockQuantity" min="0" step="1"  max="100000" required>
</div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="stockPrice">Stock Price:</label>
                                                    <input type="number" class="form-control" id="stockPrice" name="stockPrice" required min="0" step="1" max="100000" >
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
                                                    <input type="number" class="form-control" id="stockBoughtPrice" name="stockBoughtPrice" min="0" step="1" max="100000"  required>
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
                                    <h5 class="modal-title" id="addEquipmentModalLabel">Add Equipment</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <!-- Equipment Name Input -->
                                    <div class="form-group">
                                        <label for="equipmentName">Equipment Name:</label>
                                        <input type="text" class="form-control" id="equipmentName" name="equipmentName" required>
                                    </div>
                                    <!-- Quantity Input -->
                                    <div class="form-group">
                                        <label for="quantity">Quantity:</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" required>
                                    </div>
                                    <!-- Price Bought Input -->
                                    <div class="form-group">
                                        <label for="priceBought">Price Bought:</label>
                                        <input type="number" class="form-control" id="priceBought" name="priceBought" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Add Equipment</button> <!-- Submit button added -->
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
                                <h5 class="modal-title" id="usernamePasswordMismatchModalLabel"></h5>
                                <i data-feather="x-circle" class="text-end featherer" data-dismiss="modal">

                                </i>
                            </div>
                            <div class="modal-body">
                                <div class="justify-content-center d-flex">
                                    <img src="img/img-alerts/caution-mark.png">
                                </div>
                                <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>REMOVE</b></h2>
                                <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>ITEM</b></h2>
                                <div class="text-center">
                                    <small style="letter-spacing: -1px; color:#5e6e82;">Are you sure you want to delete<br></small>
                                    <small style="letter-spacing: -1px; color:#5e6e82;">the selected item/s?<br></small>
                                </div>
                                <div class="align-items-center justify-content-center d-flex mb-3 mt-3">
                                    <button type="button" style="background-color: #C1C1C1; border:none;" class="btn btn-success px-3 mr-2 py-2" data-dismiss="modal"><b>Cancel</b></button>
                                    <button type="button" style="background-color: #EE5253; border:none;" class="btn btn-success px-3 py-2" id="confirmDeleteButton"><b>Remove</b></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                  <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the selected items?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button id="confirmDeleteBtn" type="button" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
                    <!-- Bootstrap Modal -->
    <div class="modal fade" id="stockModal" tabindex="-1" role="dialog" aria-labelledby="stockModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="stockModalLabel">Error</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Stock still exists, please remove stock before deleting.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                <script>
    document.getElementById('stockDosage').addEventListener('input', function (e) {
        // Remove any non-numeric characters except dot
        this.value = this.value.replace(/[^0-9.]/g, '');

        // Ensure only one dot is allowed
        if (this.value.split('.').length > 2) {
            this.value = this.value.replace(/\.+$/, '');
        }

        // Ensure zero is only allowed once before a dot
        if (this.value.length > 1 && this.value.startsWith('0') && !this.value.startsWith('0.')) {
            this.value = this.value.replace(/^0+/, '');
        }

        // Limit to 8 digits in total
        let parts = this.value.split('.');
        if (parts[0].length > 8) {
            this.value = parts[0].substring(0, 8);
        } else if (parts.length === 2 && parts[0].length + parts[1].length > 8) {
            parts[1] = parts[1].substring(0, 8 - parts[0].length);
            this.value = parts.join('.');
        }

        // Ensure value is not negative
        if (parseFloat(this.value) < 0) {
            this.value = '';
        }
    });
</script>
                <script>
    document.getElementById('stockQuantity').addEventListener('input', function (e) {
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
    document.getElementById('stockBoughtPrice').addEventListener('input', function (e) {
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
    document.getElementById('stockPrice').addEventListener('input', function (e) {
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
        document.addEventListener('DOMContentLoaded', (event) => {
            const actionButton = document.getElementById('actionButton');
            const deleteButton = document.getElementById('deleteButton2');
            const rows = document.querySelectorAll('#TotalStock tbody tr');
            const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            const stockModal = new bootstrap.Modal(document.getElementById('stockModal'));
            let selectedRows = [];
            let selectionMode = false;

            actionButton.addEventListener('click', () => {
                selectionMode = !selectionMode;
                actionButton.textContent = selectionMode ? 'Cancel' : 'Action';
                if (!selectionMode) {
                    selectedRows.forEach(row => row.classList.remove('selected'));
                    selectedRows = [];
                    deleteButton.classList.add('hidden');
                }
            });

            rows.forEach(row => {
                row.addEventListener('click', () => {
                    if (selectionMode) {
                        if (row.classList.contains('selected')) {
                            row.classList.remove('selected');
                            selectedRows = selectedRows.filter(r => r !== row);
                        } else {
                            row.classList.add('selected');
                            selectedRows.push(row);
                        }
                        deleteButton.classList.toggle('hidden', selectedRows.length === 0);
                    }
                });
            });

            deleteButton.addEventListener('click', () => {
                // Show the confirmation modal
                confirmDeleteModal.show();
            });

            document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
                const idsToDelete = selectedRows.map(row => ({
                    medicineId: row.getAttribute('data-medicine-id'),
                    medicineBrandId: row.getAttribute('data-medicine-brand-id')
                }));

                fetch('backend/delete_medicine.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ ids: idsToDelete }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        selectedRows.forEach(row => row.remove());
                        selectedRows = [];
                        deleteButton.classList.add('hidden');
                        selectionMode = false;
                        actionButton.textContent = 'Action';
                        location.reload();
                    } else {
                        stockModal.show();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    </script>
                <script>
                    feather.replace();
                </script>
                <script>
                    $(document).ready(function() {
                        $('#deleteButton').click(function() {
                            $('#removalConfirmationModal').modal('show');
                        });
                    });
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
                    const ctx = document.getElementById('myChart1');

                    new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: <?php echo $labels_js; ?>,
                            datasets: [{
                                label: '# of Stock',
                                data: <?php echo $data_js; ?>,
                                borderWidth: 1
                            }]
                        },
                        options: {
                            plugins: {
                                legend: {
                                    position: 'bottom', // Position the legend on the right side
                                    labels: {
                                        font: {
                                            size: 10 // Adjust font size as needed
                                        }
                                    }
                                }
                            }
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
                        $(document).ready(function() {
    // DataTable initialization
    var table = $('#example').DataTable({
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

    // Initially hide all checkboxes
    $('.select-checkbox').hide();

    // Flag to track edit mode status
    var editMode = false;

    // Function to toggle checkboxes visibility inside DataTable rows
    function toggleCheckboxesVisibility() {
        var rows = table.rows({ search: 'applied' }).nodes(); // Get all rows, including filtered ones

        $(rows).each(function() {
            var checkbox = $(this).find('.select-checkbox');
            if (editMode) {
                checkbox.show(); // Show checkbox if edit mode is on
            } else {
                checkbox.hide(); // Hide checkbox if edit mode is off
                checkbox.prop('checked', false); // Ensure checkbox is unchecked when hidden
            }
        });

        // Update buttons visibility after toggling checkboxes
        toggleButtonsVisibility();
    }

    // Function to toggle buttons visibility based on number of checkboxes checked
    function toggleButtonsVisibility() {
        var checkedCheckboxes = $('.select-checkbox:checked');
        if (checkedCheckboxes.length === 1) {
            $('#updateButton, #deleteButton, #viewButton').show();
        } else if (checkedCheckboxes.length > 1) {
            $('#updateButton, #deleteButton').show();
            $('#viewButton').hide();
        } else {
            $('#updateButton, #deleteButton, #viewButton, #selectAllButton').hide();
        }
    }

    // Hide "View", "Delete", "Edit" and "Select All" initially
    $('#viewButton, #deleteButton, #updateButton, #selectAllCheckbox').hide();

    // Handle "Edit" button click
    $('#editButton').on('click', function() {
        editMode = !editMode; // Toggle edit mode

        // Toggle checkboxes visibility
        toggleCheckboxesVisibility();

        // Toggle the visibility and state of the "Select All" button
        $('#selectAllCheckbox').toggle().data('checked', false);

        // Uncheck "Select All" checkbox when edit mode is turned off
        if (!editMode) {
            $('#selectAllCheckbox').prop('checked', false);
        }

        $('.status-dropdown').prop('disabled', true);

        // Hide "Select All" button if no checkboxes are visible
        if ($('.select-checkbox:visible').length === 0) {
            $('#selectAllCheckbox').hide();
        }
    });

    // Handle "Select All" button click
    $('#selectAllCheckbox').on('click', function() {
        var rows = table.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);

        // Toggle state of all checkboxes and disable/enable dropdowns accordingly
        $('.select-checkbox', rows).each(function() {
            var applicantID = $(this).val();
            var dropdown = $("select[name='statusUpdate[" + applicantID + "]']");
            dropdown.prop("disabled", !$(this).prop("checked"));
        });

        // Update buttons visibility
        toggleButtonsVisibility();
    });

    // Handle individual checkboxes
    $('#example tbody').on('change', '.select-checkbox', function() {
        // Update buttons visibility
        toggleButtonsVisibility();
    });

    // Handle "Update" button click
    $('#updateButton').on('click', function() {
        var selectedCheckbox = $('.select-checkbox:checked');

        // Handle update logic
        if (selectedCheckbox.length === 1) {
            var patientID = selectedCheckbox.val();
            window.location.href = 'Edit Patient.php?patientID=' + patientID;
        } else {
            alert('Please select exactly one row to update.');
        }
    });

        // Link custom search input with DataTable
        var customSearchInput = $('#customSearchInput');
    customSearchInput.on('input', function() {
        table.search(this.value).draw();
    });
    // Handle "View" button click
    $('#viewButton').on('click', function() {
        var selectedCheckbox = $('.select-checkbox:checked');

        // Handle view logic
        if (selectedCheckbox.length === 1) {
            var patientID = selectedCheckbox.val();
            window.location.href = 'patientdetails-profile.php?patientID=' + patientID;
        } else {
            alert('Please select exactly one row to view.');
        }
    });
});


$('#confirmDeleteButton').on('click', function() {
        var selectedRows = [];
        $('#example').DataTable().$('tr').each(function() {
        var checkbox = $(this).find('.select-checkbox');
        if (checkbox.is(':checked')) {
            selectedRows.push(checkbox.val());
        }
    });

        if (selectedRows.length > 0) {
            $.ajax({
                type: 'POST',
                url: 'backend/remove_medicine.php',
                data: { selectedRows: selectedRows },
                success: function(response) {
                    window.location.href = 'inventory.php';
                },
                error: function(xhr, status, error) {
                    alert('An error occurred: ' + xhr.responseText);
                }
            });
        } else {
            alert('No rows selected for deletion.');
        }
    });
                </script>
                <script>
                    $(document).ready(function() {
                        // DataTable initialization
                        var table1 = $('#UsageTable').DataTable({
                            paging: true,
                            responsive: true,
                            searching: true,
                            "pageLength": 5,
                            "lengthMenu": [
                                [5, 25, 50, -1],
                                [5, 25, 50, "All"]
                            ],
                            "dom": "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-12 ml-5 mt-3'>><<'col-sm-12'lp>>",
                            
                            columnDefs: [{
                                orderable: false,
                                targets: 0
                            }],
                            language: {
                                "lengthMenu": "Display _MENU_ "
                            }
                        });

                        function updatePageInfo2() {
                            var pageInfo1 = table1.page.info();
                            var currentPage1 = pageInfo1.page + 1;
                            var totalPages1 = pageInfo1.pages;
                            var pageLengthContainer1 = $('#UsageTable .dataTables_length');
                            pageLengthContainer1.find('.page-info').remove();
                            pageLengthContainer1.append('<span class="page-info" style="margin-left: 10px;">Page: <b>' + currentPage1 + '</b> of <b>' + totalPages1 + '</b></span>');
                        }

                        // Initial update of page information
                        updatePageInfo2();

                        // Update page information whenever the table is redrawn
                        table1.on('draw', function() {
                            updatePageInfo2();
                        });
                    });
                </script>
                <script>
                    $(document).ready(function() {
                        // DataTable initialization
                        var table = $('#TotalStock').DataTable({
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
                                "<<'col-sm-12 justify-content-center d-flex mt-5 p-0'p>>", // Place length menu and pagination in separate rows
                            
                            columnDefs: [{
                                    orderable: false,
                                    targets: 0
                                } // Disable ordering for the first column with checkboxes
                            ],
                            pageLength: 10,
                            lengthMenu: [
                                [5, 25, 50, -1],
                                [5, 25, 50, "All"]
                            ],
                            language: {
                                "lengthMenu": "Display _MENU_ "

                            }
                        });
                    });
                </script>
                <script>
                    $(document).ready(function() {
                        // DataTable initialization
                        var table = $('#EquipmentTable').DataTable({
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
                            columnDefs: [{
                                    orderable: false,
                                    targets: 0
                                } // Disable ordering for the first column with checkboxes
                            ],
                            pageLength: 5,
                            lengthMenu: [
                                [5, 25, 50, -1],
                                [5, 25, 50, "All"]
                            ],
                            language: {
                                "lengthMenu": "Display _MENU_ "

                            }
                        });
                    });
                </script>
                <script>
                    $(document).ready(function() {
                        // DataTable initialization
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

                            pageLength: 5,
                            lengthMenu: [
                                [5, 25, 50, -1],
                                [5, 25, 50, "All"]
                            ],
                            language: {
                                "lengthMenu": "Display _MENU_ "

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