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


$sql = "SELECT 
            DATE_FORMAT(mu.UsageDate, '%Y-%m') AS Month,
            mu.MedicineName,
            SUM(mu.Quantity) AS TotalQuantity
        FROM 
            MedicineUsage mu
        GROUP BY 
            Month, mu.MedicineName
        ORDER BY 
            Month";


$result = mysqli_query($conn, $sql);

// Check if the query was successful
if ($result) {
    // Initialize an array to store the monthly medicine distribution data
    $medicineDistribution = array();
    
    // Initialize an empty array to store medicine names
    $medicineNames = array();

    // Fetch the rows from the result set
    while ($row = mysqli_fetch_assoc($result)) {
        // Add each row to the medicine distribution array
        $medicineDistribution[] = $row;
        
        // Add the medicine name to the medicine names array if it's not already there
        if (!in_array($row['MedicineName'], $medicineNames)) {
            $medicineNames[] = $row['MedicineName'];
        }
        
    }

    // Now $medicineDistribution contains the monthly medicine distribution data
    // and $medicineNames contains the unique medicine names
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

  <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"  rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet"> <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="css/hamburgers.css" rel="stylesheet">
  <link href="css/userdashboard.css" rel="stylesheet">
  <title>Reports and Analytics</title>
  <style>
        #monthlyPatientChart {
            width: 100%; /* Make the canvas responsive */
            height: 200px; /* Set the desired height */
        }
    </style>
</head>
<body>

<div class="container-fluid">
<div id="preloader"></div>
    <div class="main-container">
        <!-- Header and Sidebar -->
        <?php include 'includes/admin_header.php'; ?>
        <div class="sidebar">
            <?php include 'includes/sidebar.php'; ?>
        </div>
        


<!--Profile Picture and Details--><div class="content" id="content">
<div class="row">
  <div class="col-sm-12 col-md-10 col-lg-11 mt-2 mx-auto mb-4">
    <div class="card mx-auto table-card mb-3">
      <div class="card-header header-main">
        <h3 class="card-title text-center main-font-color mt-3 ml-2"><b>REPORTS</b></h3>
      </div>
      <div class="card-body p-4 px-5">
        <div class="row">
          <div class="col-md-6 pt-4">
            <h5 class="main-font-color"><b>Monthly Treatment Distribution</b></h5>
            <canvas id="monthlyTreatmentChart"></canvas>
          </div>
      
          <div class="col-md-6 border-left py-3">
            <div class="col-md-10">
            <div class="row">
            <div class="col-md-3 align-items-center justify-content-center d-flex">
                <img src="img/img-dashboard/injection-blue-db.png" height="55px">
              </div>
              <div class="col-md-9 mb-3 pl-0">
                <h1 class="main-font-color " style="font-size: 3rem; margin-bottom: -10px;"><b>  <?php
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

    // Close the statement and database connection
    
    ?></b></h1>
                <span class="pt-0 mb-0">Treatments done this week</span>
                <h5 class="main-font-color"><b>Weekly Treatment Count</b></h5>
              </div>
            </div>
            </div>
            <div class="col-md-10 mt-1 p-0">
              <div class="row border-top">
              <div class="col-md-3 align-items-center justify-content-center d-flex pl-4">
                <img src="img/img-dashboard/medicine-bag-blue.png" height="55px">
              </div>
                <div class="col-md-9 pl-0 mt-3">
                <h1 class="main-font-color pb-0 " style="font-size: 3rem; margin-bottom: -10px;"><b><?php echo $totalQuantity; ?></b></h1>
<span class="pt-0 mb-0"><?php echo $medicineBrandName; ?></span>

                  <h5 class="main-font-color"><b>Most Used Medicine</b></h5>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  
  <div class="row mb-4">
    <div class="col-md-6 mt-3">
      <div class="card table-card"> 
        <div class="card-header header-main">
          <h4 class="card-title text-left main-font-color mt-3 ml-2"><b>Medicine Stock Distribution</b></h4>
        </div>    
        <div class="card-body mx-h-400 bg-main-color-2 pb-2 m-0 p-0">
          <div class="d-flex justify-content-center mx-h-300 align-items-center">
            <canvas id="medicineStockChart"width="400" style="min-height:300px;min-width:300px;"></canvas>    
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-md-6 mt-3">
      <div class="card table-card"> 
        <div class="card-header header-main">
          <h4 class="card-title text-left main-font-color mt-3 ml-2"><b>Monthly Medicine Usage</b></h4>
        </div>    
        <div class="card-body mx-h-400 bg-main-color-2 pb-2 p-0">
          <div class="d-flex justify-content-center mx-h-300 align-items-center" style="height:300px;">
            <canvas id="monthlyMedicineUsageChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12 col-md-10 col-lg-12 mt-2 mx-auto mb-4">
      <div class="line"></div>
    </div>
  </div>

  <div class="row mr-0 pr-0">
    <div class="col-sm-12 col-md-12 col-lg-12  mx-auto mb-4 mr-0 pr-0">
      <div class="card mx-auto table-card">
        <div class="card-header header-main">
          <h3 class="card-title text-center main-font-color mt-3 ml-2"><b>ANALYTICS</b></h3>
        </div>
        <div class="card-body p-5">
          <div class="row">
            <div class="col-lg-2 no-break m-0 p-0 align-items-center justify-content-center d-flex d-none d-sm-none d-md-none d-lg-none d-xl-block">
              <img src="img/img-dashboard/green-plus.png">
            </div>
            <div class="col-sm-12 col-lg-6 col-xl-5 text-left">
            <?php
// Execute the Python script and capture the JSON output
$output = shell_exec('python backend/machinelearning/linear-regression.py');

// Find the start and end positions of the JSON object within the output
$start_pos = strpos($output, '{');
$end_pos = strrpos($output, '}');

if ($start_pos !== false && $end_pos !== false) {
    // Extract the JSON object from the output
    $json_data = substr($output, $start_pos, $end_pos - $start_pos + 1);

    // Parse the extracted JSON data
    $parsed_data = json_decode($json_data, true);

    // Check if JSON parsing was successful
    if ($parsed_data !== null) {

        // Display the daily predicted medicine usage
        echo '<div class="col-sm-12 col-lg-7 text-left">';
        echo '<h5 class="main-font-color"><b>Daily Predicted Medicine Usage</b></h5>';
        foreach ($parsed_data['daily'] as $medicineName => $quantity) {
            echo '<div style="display: flex; align-items: center;">';
            echo '<h5 class="gray mr-2">' . $quantity . '</h5><h6>' . $medicineName . '</h6>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
        // Display the weekly predicted medicine usage
        echo '<div class="col-sm-12 col-lg-5 text-left">';
        echo '<h5 class="main-font-color"><b>Weekly Predicted Medicine Usage</b></h5>';
        foreach ($parsed_data['weekly'] as $medicineName => $quantity) {
            echo '<div style="display: flex; align-items: center;">';
            echo '<h5 class="gray mr-2">' . $quantity . '</h5><h6>' . $medicineName . '</h6>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
    } else {
        // JSON parsing failed
        $error_code = json_last_error();
        $error_message = json_last_error_msg();
        echo "JSON parsing failed! Error code: $error_code, Error message: $error_message";
    }
} else {
    echo "JSON object not found in the output!";
}
?>
 </div>
 
</div>
    </div>
  </div>
  <div class="row mb-5 mr-0 pr-0">
    <div class="col-md-6 mt-3">
      <div class="card table-card h-100"> 
        <div class="card-header header-main">
          <h5 class="card-title text-left gray mt-2 ml-2"><b>Monthly Patient Count</b></h5>
        </div>    
        <div class="card-body bg-main-color-2 pb-4 pt-4 px-2 m-0 p-0 d-flex justify-content-center align-items-center w-100 h-100">
         
            <canvas id="monthlyPatientChart" height="180px"></canvas>    
      
        </div>
        
      </div>
      
    </div>
    <div class="row col-md-6 mt-3 pr-0">
    <?php
// Execute the Python script and capture the JSON output
$output = shell_exec('python backend/machinelearning/linear-regression-patient.py');

// Decode the JSON data
$parsed_data = json_decode($output, true);

// Check if JSON decoding was successful
if ($parsed_data !== null) {
    // Display the daily predicted patient count
    $daily_prediction = number_format($parsed_data["next_day_prediction"], 2);
    echo '<div class="col-md-12 pr-0">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <img src="img/img-dashboard/ri_user-add-fill.png" alt="Logo" class="img-card-icons mr-3 mt-2">
                        <div>
                            <h1 class="text-font-big main-font-color mb-0"><b>' . $daily_prediction . '</b></h1>
                            <p class="small-text mb-0">Patients</p>
                            <h5 class="text-font-medium main-font-color mb-0"><b>Daily Predicted Patient Count</b></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        ';

        $weekly_prediction = number_format($parsed_data["weekly_prediction"], 2);
        
    echo ' 
    <div class="col-md-12 pr-0 align-items-end justify-content-end d-flex w-100">
            <div class="card pr-0 w-100">
                <div class="card-body w-100">
                    <div class="d-flex align-items-center">
                        <img src="img/img-dashboard/ri_user-add-fill.png" alt="Logo" class="img-card-icons mr-3 mt-2">
                        <div> 
                            <h1 class="text-font-big main-font-color mb-0"><b>' . $weekly_prediction . '</b></h1>
                            <p class="small-text mb-0">Patients</p>
                            <h5 class="text-font-medium main-font-color mb-0"><b>Weekly Predicted Patient Count</b></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
} else {
    // JSON parsing failed
    echo "Failed to parse JSON data!";
}
?>
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
      <?php foreach ($datasets as $index => $dataset): ?>
      {
        label: '<?php echo $dataset['label']; ?>', // Using category as the label
        data: <?php echo json_encode($dataset['data']); ?>,
        borderColor: '<?php echo $dataset['borderColor']; ?>',
        backgroundColor: '<?php echo $dataset['backgroundColor']; ?>'
      }<?php if ($index < count($datasets) - 1): ?>,<?php endif; ?>
      <?php endforeach; ?>
    ]
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        position: 'top',
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

   // Define chart configuration
   const monthlyPatientCountConfig = {
  type: 'bar',
  data: {
    labels: <?php echo json_encode($monthly_labels); ?>,
    datasets: [
      {
        label: 'Patient Count',
        data: <?php echo json_encode($monthly_patient_counts); ?>,
        borderColor: 'blue',
        backgroundColor: 'rgba(0, 0, 255, 0.5)',
      }
    ]
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        position: 'bottom', // Change legend position to bottom
      },
      title: {
        display: true,
        text: 'Monthly Patient Chart'
      }
    }
  },
};
function getRandomColor() {
  // Generate a random hexadecimal color with different hues of blue
  var shades = ['33', '66', '99', 'CC', 'FF']; // Different shades of blue
  var randomShade = shades[Math.floor(Math.random() * shades.length)]; // Choose a random shade
  return '#' + '0000' + randomShade; // Combine with fixed red and green components
}

// Create the chart
const ctx5 = document.getElementById('monthlyPatientChart').getContext('2d');
const monthlyPatientChart = new Chart(ctx5, monthlyPatientCountConfig);
// Define chart configuration for monthly medicine usage (horizontal bar chart)
const monthlyMedicineUsageChartConfig = {
  type: 'bar',
  data: {
    labels: <?php echo json_encode(array_unique(array_column($medicineDistribution, 'Month'))); ?>,
    datasets: [
      <?php foreach ($medicineNames as $medicineName): ?>
      {
        label: '<?php echo $medicineName; ?>',
        data: <?php
  $data = array();
  foreach ($medicineDistribution as $entry) {
    if ($entry['MedicineName'] === $medicineName) {
      $data[] = (int)$entry['TotalQuantity']; // Ensure TotalQuantity is converted to integer
    }
  }
  echo json_encode($data);
?>,

        backgroundColor: getRandomColor(),
        borderColor: getRandomColor(),
        borderWidth: 1,
      },
      <?php endforeach; ?>
    ]
  },
  options: {
    indexAxis: 'y',
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'top',
      },
      title: {
        display: true,
        text: 'Monthly Medicine Usage Chart'
      }
    },
    scales: {
      x: {
        title: {
          display: true,
          text: 'Date'
        }
      },
      y: {
        title: {
          display: true,
          text: 'Number of Uses'
        }
      }
    }
  }
};

// Create the chart
const ctxMedicineUsage = document.getElementById('monthlyMedicineUsageChart').getContext('2d');
const monthlyMedicineUsageChart = new Chart(ctxMedicineUsage, monthlyMedicineUsageChartConfig);

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
            position: 'top',
          },
          title: {
            display: true,
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
$(document).ready(function () {
    $('#generateInvoiceButton').on('click', function () {
        var selectedRows = $('.select-checkbox:checked').map(function () {
            return $(this).val();
        }).get();

        if (selectedRows.length === 0) {
            alert('No rows selected to generate invoices.');
            return;
        }

        console.log('Sending AJAX request...');
        $.ajax({
            type: 'POST',
            url: 'generateinvoice.php',
            data: { selectedRows: selectedRows },
            dataType: 'json', // Specify that you expect JSON data
            success: function (response) {
                console.log(response);

                // Check the 'status' property in the JSON response
                if (response.status === 'success') {
                    alert(response.message);
                } else {
                    alert('Invoice generation failed: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                // Handle errors (if any) with an alert
                if (xhr.status === 404) {
                    alert('Error: Resource not found (404).');
                } else if (xhr.status === 500) {
                    alert('Error: Internal server error (500).');
                } else {
                    alert('Error: ' + xhr.responseText);
                }
            }
        });
    });




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
            $('#viewButton').show();
            $('#generateInvoiceButton').show();
        } else if (checkedCheckboxes.length > 1) {
            $('#updateButton').hide();
            $('#viewButton').hide();
            $('#deleteButton').show();
            $('#generateInvoiceButton').show();
        } else {
            $('#updateButton, #deleteButton,#viewButton,#generateInvoiceButton').hide();
        }
    }

    // Initially hide the Delete and Update buttons
    $('#deleteButton, #updateButton, #selectAllButton,#viewButton,#generateInvoiceButton').hide();

    // Handle "Edit" button click
    $('#editButton').on('click', function () {
        toggleCheckboxesVisibility();
        toggleButtonsVisibility(); 

    
        

        // Toggle the visibility and state of the "Select All" button
        $('#selectAllButton').toggle();
        $('#selectAllButton').data('checked', false);

        $('.status-dropdown').prop('disabled', true);

        // Hide "Select All" button if no checkboxes are visible
        if ($('.select-checkbox:visible').length === 0) {
            $('#selectAllButton').hide();
        }
    });

    $("#updateButton").on("click", function () {
        var updates = {};
        $(".select-checkbox:checked").each(function () {
            var applicantID = $(this).val();
            var newStatus = $("select[name='statusUpdate[" + applicantID + "]']").val();
            updates["statusUpdate[" + applicantID + "]"] = newStatus;
        });

        // Set the updates directly as form parameters
        $("#updateForm").find(":input[name^='statusUpdate']").remove();
        $.each(updates, function (name, value) {
            $("#updateForm").append('<input type="hidden" name="' + name + '" value="' + value + '">');
        });

        // Submit the form
        $("#updateForm").submit();
    });
    // Handle "Select All" button click
    $('#selectAllButton').on('click', function () {
        var checkboxes = $('.select-checkbox');
        var allChecked = checkboxes.length === checkboxes.filter(':checked').length;

        // Toggle the state of all checkboxes
        checkboxes.prop('checked', !allChecked);
        checkboxes.each(function () {
            var applicantID = $(this).val();
            var dropdown = $("select[name='statusUpdate[" + applicantID + "]']");
            dropdown.prop("disabled", !$(this).prop("checked"));
        });


        // Update buttons visibility
        toggleButtonsVisibility();
    });

    // Handle individual checkboxes
    $('#example tbody').on('change', '.select-checkbox', function () {
        // Update buttons visibility
        toggleButtonsVisibility();
    });




        // Implement your update logic here
        $('#viewButton').on('click', function () {
    var selectedCheckbox = $('.select-checkbox:checked');

    // Check if exactly one checkbox is checked
    if (selectedCheckbox.length === 1) {
        var applicantID = selectedCheckbox.val();

        // Redirect to the view profile page with the selected Applicant ID
        window.location.href = 'patientdetails-profile.php?ApplicantID=' + applicantID;
    } else {
        // If no checkbox or more than one checkbox is checked, show an alert
        alert('Please select exactly one row to view.');
    }
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
