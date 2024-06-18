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

// Initialize empty arrays to store monthly data
$monthly_labels = [];
$monthly_patient_counts = [];

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

  // Modify the SQL query to retrieve monthly patient counts
  $monthly_sql = "SELECT YEAR(DateAdded) AS Year, MONTH(DateAdded) AS Month, COUNT(*) AS PatientCount
                    FROM patient
                    GROUP BY YEAR(DateAdded), MONTH(DateAdded)
                    ORDER BY YEAR(DateAdded), MONTH(DateAdded)";
  $monthly_result = mysqli_query($conn, $monthly_sql);
  if ($monthly_result) {
    // Fetch monthly data and store them in the arrays
    while ($monthly_row = mysqli_fetch_assoc($monthly_result)) {
      $monthly_labels[] = date("F", mktime(0, 0, 0, $monthly_row['Month'], 1)); // Convert month number to month name
      $monthly_patient_counts[] = $monthly_row['PatientCount'];
    }
  } else {
    echo "Failed to fetch monthly data!";
  }
} else {
  // Admin information not found
  echo "Admin information not found!";
}
$sql = "SELECT 
            DATE_FORMAT(mu.UsageDate, '%Y-%m') AS Month,
            SUM(mu.Quantity) AS TotalQuantity
        FROM 
            MedicineUsage mu
        GROUP BY 
            DATE_FORMAT(mu.UsageDate, '%Y-%m')
        ORDER BY 
            DATE_FORMAT(mu.UsageDate, '%Y-%m')";

$result = mysqli_query($conn, $sql);

// Initialize arrays to hold data
$labels = [];
$datas = [];

// Process the MySQL result
while ($row = mysqli_fetch_assoc($result)) {
  // Extract month name from the date format
  $month = date('F', strtotime($row['Month']));

  // Append month name to labels array
  $labels[] = $month;

  // Append total quantity to data array
  $datas[] = $row['TotalQuantity'];

}
$sql = "SELECT SUM(mi.StockQuantity) AS TotalStockQuantity, mb.MedicineID, m.MedicineName
        FROM medicineinventory mi
        JOIN medicinebrand mb ON mi.MedicineBrandID = mb.MedicineBrandID
        JOIN medicine m ON mb.MedicineID = m.MedicineID
        GROUP BY m.MedicineID";

// Execute the SQL query
$result = mysqli_query($conn, $sql);

// Check if the query was successful
if ($result) {
  // Fetch the rows from the result set
  $medicineData = array();
  while ($row = mysqli_fetch_assoc($result)) {
    $medicineData[] = $row;
  }

  // Now $medicineData contains the data you retrieved from the database
} else {
  // Error executing the query
  echo "Error: " . mysqli_error($conn);
}

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

    // Now $medicineBrand contains the BrandName of the MedicineBrand with the highest total quantity
  } else {
    // No data found
    $medicineBrand = "Unknown";
    $totalQuantity = 0;
  }
} else {
  // Error executing the query
  $medicineBrand = "Unknown";
  $totalQuantity = 0;
}



// Execute the SQL query
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
  // Initialize an array to store the treatment data
  $treatmentData = array();

  // Fetch the rows from the result set
  while ($row = mysqli_fetch_assoc($result)) {
    // Add each row to the treatment data array
    $treatmentData[] = $row;
  }

  // Now $treatmentData contains the treatment count for each category per month
} else {
  // Error executing the query
  echo "Error: " . mysqli_error($conn);
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

  <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="css/hamburgers.css" rel="stylesheet">
  <link href="css/userdashboard.css" rel="stylesheet">
  <title>Reports and Analytics</title>
  <style>
    #monthlyPatientChart {
      width: 100%;
      /* Make the canvas responsive */
      height: 200px;
      /* Set the desired height */
    }
      .card {
            border-radius: 8px !important;
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
      <div class="content mb-4" id="content">
        <div class="row">
          <div class="  col-sm-12 col-md-10 col-lg-11 mt-2 mx-auto mb-4 ">
            <div class="card">
              <div class="card-body ml-3">
                <div class="row">
                <div class="col-md-9 p-0 m-0">
                <p class="h4 font-weight-bold main-color"> REPORTS & ANALYTICS </p>
                <small> Reports and Analytics tools provide you with critical insights into your clinic’s  <br></small>
                <small> treatment counts, medicine usage, distribution patterns, and predictive analytics.  </small>
                </div>
                <div class="col-md-3 d-flex align-items-center text-right justify-content-end">
                <a href="export.php" style="font-size:small; text-wrap:nowrap; color:#0449a6;"> <img src="img/img-dashboard/ph_download-fill.png" height="20px" class="mr-2"></img>Download Report</a>
                </div>
                </div>
              </div>
              
            </div>
          </div>
          <div class="  col-sm-12 col-md-10 col-lg-11 mt-2 mx-auto mb-4 justify-content-center align-items-center d-flex">
            <a href="Reports and Analytics.php" class="btn btn-primary py-2 btn-inventory mr-5" style="font-size: 15px;">Clinic Reports </a>
            <a href="Reports and Analytics-Analytics.php" style="color:#0449a6;" class="ml-5">Analytics </a>
          </div>
          
          <div class="col-sm-12 col-md-10 col-lg-11 mt-2 mx-auto mb-4">
  <div class="row">
    <div class="col-md-8">
      <div class="card h-100">
        <div class="card-body p-4 px-5">
          <div class="row">
            <div class="col-md-12 d-flex justify-content-between">
              <div>
                <h5 class="gray font-weight-normal">Treatment Distribution</h5>
              </div>
              <div>
                <span class="badge badge-pill badge-info py-1 px-2 font-weight-normal" style="background-color: #FFC70080; color:#967500; font-size:14px;">Monthly</span>
              </div>
            </div>
            <canvas id="monthlyTreatmentChart" height="130"></canvas>
          </div>
        </div>
      </div>
    </div>

   <div class="col-md-4 d-flex flex-column justify-content-between">
      <div>
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-md-12 d-flex justify-content-between">
                <div>
                  <span class="gray">Treatment Count</span>
                </div>
                <div>
                  <span class="badge badge-pill badge-info py-1 px-2 font-weight-normal" style="background-color: #DCE8F9; color:#0449a6; font-size:14px;">Weekly</span>
                </div>
              </div>
            </div>
            <div class="row pl-3">
              <div class="col-md-9 mb-0 pl-0 d-flex align-items-center py-2">
                <img src="img/img-dashboard/injection-badge.png" height="55px" class="mr-2">
                <h1 class="main-font-color mr-3" style="font-size: 3rem; margin-bottom: -10px;">
                  <b>
                    <?php
                    // Define the start and end dates for the past 7 days
                    $startDate = date('Y-m-d', strtotime('-7 days'));
                    $endDate = date('Y-m-d');

                    // Prepare and execute the SQL query to get the count of treatments done in the past 7 days
                    $sql = "SELECT COUNT(*) AS TreatmentCount FROM treatment WHERE DateofTreatment BETWEEN ? AND ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "ss", $startDate, $endDate);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    // Fetch the treatment count from the result
                    if ($row = mysqli_fetch_assoc($result)) {
                      echo $row['TreatmentCount'];
                    } else {
                      echo '0'; // Default to 0 if no treatments were found
                    }
                    ?>
                  </b>
                </h1>
              </div>
              <small class="font-weight-normal gray" style="font-size:x-small;">Treatments Done</small>
            </div>
          </div>
        </div>
      </div>

      <div>
        <div class="card mt-4">
          <div class="card-body">
            <div class="row">
              <div class="col-md-12 d-flex justify-content-between">
                <div>
                  <span class="gray">Most Used Medicine</span>
                </div>
                <div>
                  <span class="badge badge-pill badge-info py-1 px-2 font-weight-normal" style="background-color: #DCE8F9; color:#0449a6; font-size:14px;">Weekly</span>
                </div>
              </div>
            </div>
            <div class="row pl-3">
              <div class="col-md-9 mb-0 pl-0 d-flex align-items-center py-2">
                <img src="img/img-dashboard/medicine-bag-badge.png" height="55px" class="mr-2">
                <h1 class="main-font-color mr-3" style="font-size: 3rem; margin-bottom: -10px;">
                  <b>
                    <?php echo $totalQuantity; ?>
                  </b>
                </h1>
              </div>
            </div>
            <small class="font-weight-normal gray" style="font-size:x-small;"><?php echo $medicineBrandName; ?></small>
          </div>
        </div>
      </div>
    </div>
  </div>


  <div class="col-md-12 p-0 m-0  mt-4 card h-100">
  <div class="row mb-4 m-0 p-0 h-100">
    <div class="col-md-5 border-right m-0 p-0 h-100" style="border-width: 2px !important;">
      <div class="card-body bg-main-color-2 text-center mt-4">
        <h5 class="gray font-weight-normal">Medicine Stock Distribution</h5>
        <div class="d-flex justify-content-center align-items-center mt-4" style="height: 300px;">
          <canvas id="medicineStockChart" width="400" style="min-height: 300px; min-width: 300px;"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-7 m-0 p-0 h-100">
      <div class="card table-card h-100">
      <div class="card-body bg-main-color-2 text-left pl-5 mt-4">
      <div class="col-md-12 d-flex justify-content-between">
              <div>
                <h5 class="gray font-weight-normal">Medicine Usage</h5>
              </div>
              <div>
                <span class="badge badge-pill badge-info py-1 px-2 font-weight-normal" style="background-color: #FFC70080; color:#967500; font-size:14px;">Monthly</span>
              </div>
            </div>
          <div class="d-flex justify-content-center align-items-center" style="height: 300px;">
            <canvas id="monthlyMedicineUsageChart"></canvas>
          </div>
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
              // Define chart configuration
              // Define chart configuration
              <?php
              $datasets = [];
              $colors = ['#FF0000', '#1dd1a1', '#0000FF'];

              // Initialize an associative array to store treatment counts indexed by category and month
              $treatmentCountsByCategory = [];

              // Loop through the treatment data to organize treatment counts by category and month
              foreach ($treatmentData as $data) {
                $month = $data['Month'];
                $category = $data['Category'];
                $treatmentCount = $data['TreatmentCount'];

                // Check if the category already exists in the array
                if (!isset($treatmentCountsByCategory[$category])) {
                  // If not, initialize an array for the category
                  $treatmentCountsByCategory[$category] = [];
                }

                // Set the treatment count for the category and month
                $treatmentCountsByCategory[$category][$month] = $treatmentCount;
              }

              // Loop through the treatment counts to generate datasets
              foreach ($treatmentCountsByCategory as $category => $counts) {
                $data = [];
                foreach ($counts as $month => $count) {
                  $data[] = $count;
                }

                // Add the dataset for the current category
                $datasets[] = [
                  'label' => $category,
                  'data' => $data,
                  'borderColor' => $colors[count($datasets) % count($colors)], // Use modulo to cycle through colors
                  'backgroundColor' => $colors[count($datasets) % count($colors)] // Use modulo to cycle through colors
                ];
              }
              ?>

              const monthlyTreatmentChartConfig = {
                type: 'bar',
                data: {
                  labels: <?php echo json_encode(array_keys($treatmentCountsByCategory[array_key_first($treatmentCountsByCategory)])); ?>,
                  datasets: [
                    <?php foreach ($datasets as $index => $dataset) : ?> {
                        label: '<?php echo $dataset['label']; ?>', // Using category as the label
                        data: <?php echo json_encode($dataset['data']); ?>,
                        borderColor: '<?php echo $dataset['borderColor']; ?>',
                        backgroundColor: '<?php echo $dataset['backgroundColor']; ?>'
                      }
                      <?php if ($index < count($datasets) - 1) : ?>, <?php endif; ?>
                    <?php endforeach; ?>
                  ]
                },
                options: {
                  responsive: true,
                  plugins: {
                    legend: {
                      position: 'bottom',
                    },
                    title: {
                      display: true,
                      text: 'Monthly Treatment Chart'
                    }
                  }
                }
              };

              // Create the chart
              const ctx3 = document.getElementById('monthlyTreatmentChart').getContext('2d');
              const monthlyTreatmentChart = new Chart(ctx3, monthlyTreatmentChartConfig);
            </script>

            <script>
              // Data for the pie chart
              const data = {
                labels: <?php echo json_encode(array_column($medicineData, 'MedicineName')); ?>,
                datasets: [{
                  data: <?php echo json_encode(array_column($medicineData, 'TotalStockQuantity')); ?>,
                  backgroundColor: [
                    'rgba(255, 99, 132, 0.7)', // Red for ERIG
                    'rgba(54, 162, 235, 0.7)', // Blue for Anti-Rabies
                    'rgba(255, 206, 86, 0.7)', // Yellow for Anti-Tetanus
                  ],
                  borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                  ],
                  borderWidth: 1
                }]
              };

              // Configuration for the pie chart
              const config = {
                type: 'pie',
                data: data,
                options: {
                  responsive: true,
                  plugins: {
                    legend: {
                      position: 'bottom',
                    },
                    title: {
                      display: false,
                      text: 'Medicine Stock Distribution'
                    }
                  }
                }
              };

              // Create the pie chart
              const ctx4 = document.getElementById('medicineStockChart').getContext('2d');
              const myPieChart = new Chart(ctx4, config);
            </script>


            <script>
              var loader = document.getElementById("preloader");
              window.addEventListener("load", function() {
                setTimeout(function() {
                  loader.style.opacity = "1"; // Fading in
                  setTimeout(function() {
                    loader.style.opacity = "0"; // Fading out
                    setTimeout(function() {
                      loader.style.display = "none"; // Hide the preloader after fading out
                    }, 500); // Delay before hiding after fading out
                  }, 1500); // Duration of fading out
                }, 500); // Delay before fading in
              });
            </script>

            

        
            <script>
              const ctx6 = document.getElementById('monthlyMedicineUsageChart').getContext('2d');
              const monthlyMedicineUsageChart = new Chart(ctx6, {
                type: 'bar',
                data: {
                  labels: <?php echo json_encode($labels); ?>, // Dynamically populate labels arra

                  datasets: [{
                    label: 'Medicine Usage',
                    data: <?php echo json_encode($datas); ?>, // Dynamically populate data array
                    backgroundColor: 'rgba(75, 192, 192, 1)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                  }]
                },
                options: {
                  plugins: {
                    legend: {
                      position: 'bottom',
                    }
                  },
                  indexAxis: 'y',
                  scales: {
                    x: {
                      beginAtZero: true
                    }
                  }
                }
              });
            </script>

</body>

</html>