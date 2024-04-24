
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
mysqli_stmt_close($stmt);
mysqli_close($conn);
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
  <div class="col-sm-12 col-md-10 col-lg-11 mt-2 mx-auto mb-4">
    <div class="card mx-auto table-card">
      <div class="card-header header-main">
        <h3 class="card-title text-center main-font-color mt-3 ml-2"><b>REPORTS</b></h3>
      </div>
      <div class="card-body p-5">
        <div class="row">
          <div class="col-md-6 pt-4">
            <h5 class="main-font-color"><b>Monthly Treatment Distribution</b></h5>
            <canvas id="monthlyTreatmentChart"></canvas>
          </div>
          <div class="col-md-6 border-left pt-4">
            <div class="row">
              <div class="col-md-12 mb-3">
                <h1 class="main-font-color pb-0 mb-0"><b>46</b></h1>
                <span class="pt-0 mb-0">Treatments done this week</span>
                <h5 class="main-font-color"><b>Weekly Treatment Count</b></h5>
              </div>
            </div>
            <div class="col-md-10 mt-1 p-0">
              <div class="row border-top">
                <div class="col-md-12 mt-4">
                  <h1 class="main-font-color pb-0 mb-0"><b>46</b></h1>
                  <span class="pt-0 mb-0">Treatments done this week</span>
                  <h5 class="main-font-color"><b>Weekly Treatment Count</b></h5>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  
  <div class="row mb-5">
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
    <div class="col-sm-12 col-md-12 col-lg-12 mt-2 mx-auto mb-4 mr-0 pr-0">
      <div class="card mx-auto table-card">
        <div class="card-header header-main">
          <h3 class="card-title text-center main-font-color mt-3 ml-2"><b>ANALYTICS</b></h3>
        </div>
        <div class="card-body p-5">
          <div class="row">
            <div class="col-lg-2 no-break m-0 p-0 align-items-center justify-content-center d-flex d-none d-sm-none d-md-none d-lg-none d-xl-block">
              <img src="green-plus.png">
            </div>
            <div class="col-sm-12 col-lg-5 text-left ">
              <h5 class="main-font-color "><b>Daily Predicted Medicine Usage</b></h5>
              <div style="display: flex; align-items: center;">
                <h5 class="gray mr-2">12</h5><h6>ERIG</h6>
              </div>
              <div style="display: flex; align-items: center;">
                <h5 class="gray mr-2">12</h5><h6>ERIG</h6>
              </div>
              <div style="display: flex; align-items: center;">
                <h5 class="gray mr-2">12</h5><h6>ERIG</h6>
              </div>
              
            </div>
            <div class="col-sm-12 col-lg-5 text-left">
              <h5 class="main-font-color "><b>Daily Predicted Medicine Usage</b></h5>
              <div style="display: flex; align-items: center;">
                <h5 class="gray mr-2">12</h5><h6>ERIG</h6>
              </div>
              <div style="display: flex; align-items: center;">
                <h5 class="gray mr-2">12</h5><h6>ERIG</h6>
              </div>
              <div style="display: flex; align-items: center;">
                <h5 class="gray mr-2">12</h5><h6>ERIG</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row mb-5">
    <div class="col-md-6 mt-3">
      <div class="card table-card" style="height: 325px;"> 
        <div class="card-header header-main">
          <h4 class="card-title text-left main-font-color mt-3 ml-2"><b>Medicine Stock Distribution</b></h4>
        </div>    
        <div class="card-body bg-main-color-2 pb-2 m-0 p-0">
          <div class="d-flex justify-content-center align-items-center">
            <canvas id="monthlyPatientChart"width="400" style="max-height: 250px; min-height: 200px; height:250px; min-width:200px;"></canvas>    
          </div>
        </div>
        
      </div>
      
    </div>
    <div class="col-md-6 mt-3">
      <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <img src="ri_user-add-fill.png" alt="Logo" class="img-card-icons mr-3 mt-2">
                                    <div>
                                        <h1 class="text-font-big main-font-color mb-0"><b>294</b></h1>
                                        <p class="small-text mb-0">Patient Count</p>
                                        <h5 class="text-font-medium main-font-color mb-0"><b>Total Number of Patients</b></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="col-md-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <img src="ri_user-add-fill.png" alt="Logo" class="img-card-icons mr-3 mt-2">
                                    <div>
                                        <h1 class="text-font-big main-font-color mb-0"><b>294</b></h1>
                                        <p class="small-text mb-0">Patient Count</p>
                                        <h5 class="text-font-medium main-font-color mb-0"><b>Total Number of Patients</b></h5>
                                    </div>
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


</script>
<script>
    // Define chart configuration
const monthlyTreatmentChartConfig = {
  type: 'bar',
  data: {
    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
    datasets: [
      {
        label: 'Dataset 1',
        data: [20, 30, 40, 50, 60, 70, 80], // Sample data, replace with actual data
        borderColor: 'red',
        backgroundColor: 'rgba(255, 0, 0, 0.5)',
      },
      {
        label: 'Dataset 2',
        data: [10, 30, 40, 50, 60, 70, 80], // Sample data, replace with actual data
        borderColor: 'blue',
        backgroundColor: 'rgba(0, 0, 255, 0.5)',
      }
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
  },
};

// Create the chart
const ctx3 = document.getElementById('monthlyTreatmentChart').getContext('2d');
const monthlyTreatmentChart = new Chart(ctx3, monthlyTreatmentChartConfig);

   // Define chart configuration
   const monthlyPatientCountConfig = {
  type: 'bar',
  data: {
    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
    datasets: [
      {
        label: 'Dataset 2',
        data: [10, 30, 40, 50, 60, 70, 80], // Sample data, replace with actual data
        borderColor: 'blue',
        backgroundColor: 'rgba(0, 0, 255, 0.5)',
      }
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
        text: 'Monthly Patient Chart'
      }
    }
  },
};

// Create the chart
const ctx5 = document.getElementById('monthlyPatientChart').getContext('2d');
const monthlyPatientChart = new Chart(ctx5, monthlyPatientCountConfig);
// Define chart configuration for monthly medicine usage (horizontal bar chart)
const monthlyMedicineUsageChartConfig = {
  type: 'bar',
  data: {
    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
    datasets: [
      {
        label: 'Medicine A',
        data: [30, 40, 50, 60, 70, 80, 90], // Sample data for Medicine A, replace with actual data
        backgroundColor: 'rgba(255, 99, 132, 0.5)', // Red color with transparency
        borderColor: 'rgba(255, 99, 132, 1)', // Solid red border
        borderWidth: 1,
      },
      {
        label: 'Medicine B',
        data: [20, 35, 45, 55, 65, 75, 85], // Sample data for Medicine B, replace with actual data
        backgroundColor: 'rgba(54, 162, 235, 0.5)', // Blue color with transparency
        borderColor: 'rgba(54, 162, 235, 1)', // Solid blue border
        borderWidth: 1,
      }
    ]
  },
  options: {
    indexAxis: 'y', // Use y-axis as the primary axis for a horizontal bar chart
    responsive: true,
    maintainAspectRatio: false, // Try adjusting this option
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
          text: 'Number of Uses'
        }
      },
      y: {
        title: {
          display: true,
          text: 'Month'
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
      labels: ['ERIG', 'Anti-Rabies', 'Anti-Tetanus'],
      datasets: [{
        data: [300, 500, 200], // Sample data, replace with actual stock quantities
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
