<?php
session_start();

// Check if the 'admin' session variable is not set or is false (user not logged in)
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true || !isset($_SESSION['adminID'])) {
    // Redirect the user to the login page
    header("Location: Admin Login.php");
    exit(); // Terminate the script
}

// Include your database connection file
require_once 'pawfect_connect.php';

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

// Close the database connection

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"  rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet"> <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="hamburgers.css" rel="stylesheet">
  <link href="userdashboard.css" rel="stylesheet">
  <title>Tenant Dashboard</title>
  
</head>
<body>
<div class="container-fluid">
    <div class="main-container">
        <!-- Header and Sidebar -->
        <?php include 'admin_header.php'; ?>
        <div class="sidebar">
            <?php include 'sidebar.php'; ?>
        </div>


<!--Profile Picture and Details--><div class="content" id="content">
    <div class="row">
        <div class="  col-sm-12 col-md-10 col-lg-11 mt-2 mx-auto mb-4 ">
            <div class="card mx-auto table-card ">
                <div class="card-header header-main">
                    <h3 class="card-title text-center main-font-color mt-3 ml-2"><b>INVENTORY</b></h3>
                </div>
               
                
                    
                    <div class="card-body bg-main-color-2 p-5">
                        <div id="buttonContainer" class="d-flex flex-column flex-sm-row align-items-center mb-2 ml-2 mt-1">
                            <!-- Edit button on the left -->
                            <button id="toggleButtons" class="btn btn-lg btn-outline-info mr-2">Save Table</button>
                            <button id="editButton" class="btn btn-lg main-color-2 btn-gray-color  mr-2 ">Action</button>
                            <!-- Additional buttons next to Edit -->
                            <div class="d-flex flex-row flex-wrap align-items-center">

                    
                                <button id="deleteButton" class="btn btn-lg btn-danger" onclick="deleteSelectedRows()">Remove Medicine</button>
                               
                            </div>
                            
                        </div>
                        <div class="medicine-header">
                            <h4 class="main-color">Medicine</h4>
                            <div class="button-container">
                          
                            <button id="addMedicineButton" class="btn btn-lg greener mb-2  mr-sm-2 pt-2 pb-2 no-break" data-toggle="modal" data-target="#addMedicineModal">Add Medicine</button>
                            <button id="addMedicineButton" class="btn btn-lg greener mb-2  mr-sm-2 pt-2 pb-2 no-break" data-toggle="modal" data-target="#addStockModal">Add Stock</button>

                            </div>
                        </div>
                        <form id="deleteForm" action="remove_medicine.php" method="post">
                        <input type="hidden" name="selectedRows[]" id="selectedRowsInput">
                </form>
                <div class="table-responsive">
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
            m.MedicineID,
            m.MedicineName,
            mb.MedicineBrandID,
            mb.BrandName,
            mb.Route,
            SUM(mi.StockQuantity) AS TotalStockQuantity,
            mi.StockPrice,
            mi.StockDosage,
            mi.StockExpiryDate,
            mi.StockBoughtPrice
        FROM 
            medicinebrand mb
        JOIN 
            medicine m ON mb.MedicineID = m.MedicineID
        JOIN 
            medicineinventory mi ON mb.MedicineBrandID = mi.MedicineBrandID
        GROUP BY 
            m.MedicineID, mb.BrandName, mi.StockExpiryDate"; // Group by MedicineID, BrandName, and StockExpiryDate

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td class='px-3'><input type='checkbox' class='select-checkbox' name='selectedRows[]' value='" . $row['MedicineBrandID'] . "'></td>";
        echo "<td>" . $row['BrandName'] . "</td>";
        echo "<td>" . ($row['TotalStockQuantity'] !== null ? $row['TotalStockQuantity'] : 'N/A') . "</td>";
        echo "<td>" . ($row['StockPrice'] !== null ? $row['StockPrice'] : 'N/A') . "</td>";
        echo "<td>" . ($row['StockExpiryDate'] !== null ? $row['StockExpiryDate'] : 'N/A') . "</td>";
        echo "<td>" . ($row['StockBoughtPrice'] !== null ? $row['StockBoughtPrice'] : 'N/A') . "</td>";
        echo "</tr>";
    }
} else {
    echo "0 results";
}
?>




                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
       
        

    <div class="row justify-content-center">
    <div class="col-md-5 col-lg-6 mt-2">
        <div class="card table-card">     
            <div class="card-body bg-main-color-2 p-5">
                <div class="medicine-header">
                    <h4 class="main-color"> Medicine List</h4>
                </div>
              
                <div class="table-responsive max-h-300">
                    <table id="example" class="table table-striped">
                    
                        <tbody>
                            <!-- Table rows -->
                            <?php
$sql = "SELECT 
            m.MedicineID,
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
            m.MedicineID, mb.BrandName"; // Group by MedicineID and BrandName

$result = $conn->query($sql);

if ($result === false) {
    // Handle query execution error
    echo "Error executing query: " . $conn->error;
} else {
    if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['MedicineName'] . "</td>";
            echo "<td>" . $row['BrandName'] . "</td>";
            echo "<td>" . $row['TotalStockQuantity'] . "</td>"; // Display total stock quantity
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='3'>No data available</td></tr>"; // Display message if no data found
    }
}
?>




                            <!-- More rows here -->
                        </tbody>
                    </table>
                </div>
                </div>
                </div>
                </div>
               
               
               
            
 
    
    <div class="col-md-5 col-lg-6 mt-2">
        <div class="row">
            <div class="col-md-12">
                <div class="card table-card"> 
                    <div class="card-header header-main">
                        <h4 class="card-title text-left main-font-color mt-3 ml-2"><b>Medicine Stock Indicator 1</b></h4>
                    </div>    
                    <div class="card-body mx-h-400 bg-main-color-2 pb-2 m-0 p-0">
                        <div class="d-flex justify-content-center mx-h-300 align-items-center">
                            <canvas id="myChart1"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mt-2">
                <div class="card table-card"> 
                    <div class="card-header header-main">
                        <h4 class="card-title text-left main-font-color mt-3 ml-2"><b>Medicine Stock Indicator 2</b></h4>
                    </div>    
                    <div class="card-body mx-h-400 bg-main-color-2 pb-2 p-0">
                        <div class="d-flex justify-content-center mx-h-300  align-items-center" style="height:300px;">
                            <canvas id="myChart2"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            </div>
            </div>
       
        <div class="row">
        <div class="  col-sm-12 col-md-10 col-lg-12 mt-2 mx-auto mb-4 ">
            <div class="card mx-auto table-card p-5">
               
                
                    
              
                        <div class="medicine-header">
                            <h4 class="main-color"> <b>Medicine Usage History</b></h4>
                           
                        </div>
                     
                <div class="table-responsive">
                    <table id="UsageTable" class="table table-striped">
                        <thead class="table-header no-break text-center">
                            <tr>
                         
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Price Sold</th>
                                <th>Batch Date</th>
                                <th>Expiry Date</th>
                                <th>Price Bought</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Table rows -->
                
                            <tr style="background-color:white;">
                          
                                <td>Check</td>
                                <td>Test</td>
                                <td>Try</td>
                                <td>Try</td>
                                <td>Test</td>
                                <td>Check</td>
                            </tr>
                            <tr style="background-color:white;">
                            
                                <td>Check</td>
                                <td>Test</td>
                                <td>Try</td>
                                <td>Try</td>
                                <td>Test</td>
                                <td>Check</td>
                            </tr>
                            <tr style="background-color:white;">
                              
                                <td>Check</td>
                                <td>Test</td>
                                <td>Try</td>
                                <td>Try</td>
                                <td>Test</td>
                                <td>Check</td>
                            </tr>
                            <tr style="background-color:white;">
                               
                                <td>Check</td>
                                <td>Test</td>
                                <td>Try</td>
                                <td>Try</td>
                                <td>Test</td>
                                <td>Check</td>
                            </tr>
                            <!-- ... other rows ... -->
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
   
    <div class="row justify-content-center">
    <div class="col-md-5 col-lg-6 mt-2">
        <div class="card table-card">     
            <div class="card-body bg-main-color-2 p-5">
                <div class="row">
                <div class="col-md-12 col-lg-6 col-xl-6">
                <div class="medicine-header">
                    <h4 class="main-color">Equipment</h4>
</div>
</div>
<div class="col-md-12 col-lg-6 col-xl-6">
                    <div class="button-container">
                        <button id="addEqButton" class="btn btn-lg greener mb-2  mr-sm-2 pt-2 pb-2 no-break"  data-toggle="modal" data-target="#addEquipmentModal">Add Equipment</button>
                  
                    </div>
                </div>
                </div>
                <form id="deleteForm" action="delete_medicine.php" method="post">
    <input type="hidden" name="selectedRows[]" id="selectedRowsInput">
    </form>
                <div class="table-responsive h-1000">
                    <table id="example" class="table table-striped">
                        <thead class="table-header">
                            <tr>
                                <th></th>
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
// Iterate over the data to sum quantities for products with the same name
if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td class='px-3'><input type='checkbox' class='select-checkbox' name='selectedRows[]' value='" . $row['EquipmentID'] . "'></td>";
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
    </div>
    
    <div class="col-md-5 col-lg-6 mt-2">
        <div class="card table-card">     
            <div class="card-body bg-main-color-2 p-5">
                <div class="medicine-header">
                    <h4 class="main-color">Equipment Usage History</h4>
                 
                </div>
               
                <div class="table-responsive max-h-300">
                    <table id="example" class="table table-striped">
                        <thead class="table-header">
                            <tr>
                                <th></th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Table rows -->
                            <tr style="background-color:white;">
                                <td><input type="checkbox" class="select-checkbox" name="selectedRows[]" value="' . $row['ApplicantID'] . '"></td>
                                <td>Check</td>
                                <td>Test</td>
                            </tr>
                            <!-- More rows here -->
                        </tbody>
                    </table>
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
        <form id="addMedicineForm" action="add_medicine.php" method="post">
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
        <?php
        if (isset($_SESSION['successMessage'])) {
            echo '<div class="alert alert-success" role="alert">' . $_SESSION['successMessage'] . '</div>';
            unset($_SESSION['successMessage']); // Clear the session variable
        }
        ?>
            <label for="productName">Product Name</label>
            <input type="text" class="form-control" id="productName" name="productName" required>
          </div>
          <div class="form-group">
            <label for="productBrand">Product Brand</label>
            <input type="text" class="form-control" id="productBrand" name="productBrand" required>
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
          <button type="submit"  style="background-color:#10AC84; font-weight:bold;" class="btn btn-success px-4">List Medicine</button>
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
<div class="modal fade" id="addStockModal" tabindex="-1" role="dialog"  aria-labelledby="addStockModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <form id="addStockForm" method="post" action="add_stock.php">
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
                while($row = $result->fetch_assoc()) {
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
          <input type="number" class="form-control" id="stockQuantity"name="stockQuantity" required>
        </div>
        </div>
        </div>
        <div class="row">
        <div class="col-md-6">
        <div class="form-group">
          <label for="stockPrice">Stock Price:</label>
          <input type="number" class="form-control" id="stockPrice" name="stockPrice"required>
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
          <input type="date" class="form-control" id="stockExpiryDate" name="stockExpiryDate" required>
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
        <button type="submit"  style="background-color:#10AC84; font-weight:bold;" class="btn btn-success px-4">Add Stock</button>
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
      <form id="addEquipmentForm" method="post" action="add_equipment.php"> <!-- Form starts here -->
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
$(document).ready(function() {
    // Check if the session variable for alreadyExists is set to true
    <?php if(isset($_SESSION['alreadyExists']) && $_SESSION['alreadyExists'] === true): ?>
        // Show the Already Exists modal
        $('#alreadyExistsModal').modal('show');
        // Remove the session variable
        <?php unset($_SESSION['alreadyExists']); ?>
    <?php endif; ?>

    // Check if the session variable for success is set to true
    <?php if(isset($_SESSION['success']) && $_SESSION['success'] === true): ?>
        // Show the Success modal
        $('#successModal').modal('show');
        // Remove the session variable
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
});
</script>

<script>
  const ctx = document.getElementById('myChart1');

  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
      datasets: [{
        label: '# of Votes',
        data: [12, 19, 3, 5, 2, 3],
        borderWidth: 1
      }]
    },
    options: {
    plugins: {
        legend: {
            position: 'right', // Position the legend on the right side
            labels: {
                font: {
                    size: 10 // Adjust font size as needed
                }
            }
        }
    }
}});

     
</script>


<script>
    // JavaScript function to update the hidden input field with selected medicine brand IDs
    function deleteSelectedRows() {
        // Get all checkboxes with the class 'select-checkbox'
        var checkboxes = document.getElementsByClassName('select-checkbox');
        var selectedRows = [];
        // Iterate over all checkboxes
        for (var i = 0; i < checkboxes.length; i++) {
            // Check if the checkbox is checked
            if (checkboxes[i].checked) {
                // Add the value (MedicineBrandID) of the checked checkbox to the selectedRows array
                selectedRows.push(checkboxes[i].value);
            }
        }
        // Set the value of the hidden input field to the selectedRows array
        document.getElementById('selectedRowsInput').value = selectedRows.join(',');
        // Submit the form
        document.getElementById('deleteForm').submit();
    }
</script>
<script>
  const ctx2 = document.getElementById('myChart2');

  new Chart(ctx2, {
    type: 'doughnut',
    data: {
      labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
      datasets: [{
        label: '# of Votes',
        data: [12, 19, 3, 5, 2, 3],
        borderWidth: 1
      }]
    },
    options: {
    plugins: {
        legend: {
            position: 'right', // Position the legend on the right side
            labels: {
                font: {
                    size: 10 // Adjust font size as needed
                }
            }
        }
    },
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

$(document).ready(function () {
    $('#sidebarCollapse1').on('click', function () {
        $('#sidebar').toggleClass('collapsed'); // Toggle 'collapsed' class on #sidebar
        $('#content').toggleClass('collapsed'); // Toggle 'collapsed' class on #content
    });
});


</script>


<script>

$(document).ready(function () {
 



    $(".select-checkbox").change(function () {
    var selectedCheckboxValue = $(this).val();
    var dropdown1 = $("select[name='statusUpdate[" + selectedCheckboxValue + "]']");

    if ($(this).prop('checked')) {
        // Check if the option doesn't already exist and the current status is "Pending"
        if (dropdown1.find("option[value='Received']").length === 0 && dropdown1.val() === 'Pending') {
            // Add more options to the dropdown dynamically using JavaScript
            dropdown1.append('<option value="Received">Received</option>');
            
            // Add more options as needed
        }
    } else {
        // If checkbox is unchecked and the current status is "Pending," remove the added options
        if (dropdown1.val() === 'Pending') {
            dropdown1.find("option[value='Received']").remove();
            
            // Remove more options as needed
        }
    }

    var checkboxId = $(this).val();
    var dropdown = $("select[name='statusUpdate[" + checkboxId + "]']");
    dropdown.prop("disabled", !$(this).prop("checked"));
});

        
    // Function to toggle checkbox visibility
    function toggleCheckboxesVisibility() {
        var checkboxes = $('.select-checkbox');
        checkboxes.toggle();

        // If the checkboxes are being hidden, uncheck all of them
        if (!checkboxes.is(':visible')) {
            checkboxes.prop('checked', false);
        }
    }
    


    // Function to toggle buttons visibility based on the number of checkboxes checked
    function toggleButtonsVisibility() {
        var checkedCheckboxes = $('.select-checkbox:checked');
        if (checkedCheckboxes.length === 1) {
            $('#updateButton').show();
            $('#deleteButton').show();
          
            $('#addStockButton').show();
        } else if (checkedCheckboxes.length > 1) {
            $('#updateButton').hide();
            $('#viewButton').hide();
            $('#deleteButton').show();
            $('#addStockButton').hide();
        } else {
            $('#updateButton, #deleteButton,#addStockButton').hide();
        }
    }

    // Initially hide the Delete and Update buttons
    $('#deleteButton, #updateButton, #selectAllCheckbox,#viewButton,#addStockButton').hide();

    // Handle "Edit" button click
    $('#editButton').on('click', function () {
        toggleCheckboxesVisibility();
        toggleButtonsVisibility(); 

    
        

        // Toggle the visibility and state of the "Select All" button
        $('#selectAllCheckbox').toggle();
        $('#selectAllCheckbox').data('checked', false);

        $('.status-dropdown').prop('disabled', true);

        // Hide "Select All" button if no checkboxes are visible
        if ($('.select-checkbox:visible').length === 0) {
            $('#selectAllCheckbox').hide();
        }
    });

 

$('#selectAllCheckbox').on('click', function () {
    console.log("Select All button clicked!"); // Add this line for debugging

    var checkboxes = $('.select-checkbox');
    var allChecked = checkboxes.length === checkboxes.filter(':checked').length;

    // Toggle the state of all checkboxes
    checkboxes.prop('checked', !allChecked);
  
    // Update buttons visibility
    toggleButtonsVisibility();

    // Show the "Remove Medicine" button if all checkboxes are checked
   
});





    // Handle individual checkboxes
    $('#example tbody').on('change', '.select-checkbox', function () {
        // Update buttons visibility
        toggleButtonsVisibility();
    });




       
$(document).ready(function () {
    // DataTable initialization
    var table = $('#example').DataTable({
        paging: true,
        responsive: true,
        searching: true,
        "pageLength": 5, // Set default page length
        "lengthMenu": [[5, 25, 50, -1], [5, 25, 50, "All"]], // Customize page length menu
        "dom": "<'row'<'col-sm-12'f>>" + // Place search input at the top
               "<'row'<'col-sm-12't>>" + // Place table in a row
               "<'row'<'col-sm-12 ml-5 mt-3'l>><<'col-sm-12'p>>", // Place length menu and pagination in separate rows
       
        buttons: [
            {
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
        columnDefs: [
            { orderable: false, targets: 0 } // Disable ordering for the first column with checkboxes
        ],
        pageLength: 5,
        lengthMenu: [ [5, 25, 50, -1], [5, 25, 50, "All"] ],
        language: { "lengthMenu": "Display _MENU_ "
          
        }
       
    });
    function updatePageInfo() {
        var pageInfo = table.page.info();
        var currentPage = pageInfo.page + 1; // Add 1 to convert zero-based index to one-based index
        var totalPages = pageInfo.pages;
        var pageLengthContainer = $('.dataTables_length');

        // Update the page information with styles
        pageLengthContainer.find('.page-info').remove(); // Remove previous page info to avoid duplication
        pageLengthContainer.append('<span class="page-info" style="margin-left: 10px;">Page: <b>' + currentPage + '</b> of <b>' + totalPages + '</b></span>');
    }

    // Initial update of page information
    updatePageInfo();

    // Update page information whenever the table is redrawn
    table.on('draw', function() {
        updatePageInfo();
    });


    $('.btn-img').hide();

    // Toggle button visibility
    $('#toggleButtons').on('click', function () {
        $('.btn-img').toggle();
    });

    // Link custom search input with DataTable
    var customSearchInput = $('#customSearchInput');

    // Add an input event listener to trigger DataTables search on input
    customSearchInput.on('input', function () {
        table.search(this.value).draw();
    });

    // Button click event for exporting to Excel
    $('#button-excel').on('click', function () {
        // Trigger the DataTables Excel export
        table.buttons('excelHtml5').trigger();
    });

    // Toggle sidebar functionality
});






});
</script>


</body>
</html>
