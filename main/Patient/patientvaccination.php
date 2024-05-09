<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel='stylesheet' href='https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css'>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="hamburgers.css" rel="stylesheet">
  <link href="patient.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">


  <title>Admin Dashboard</title>
  <style>
    table.dataTable thead .sorting:before, table.dataTable thead .sorting_asc:before, table.dataTable thead .sorting_desc:before, table.dataTable thead .sorting_asc_disabled:before, table.dataTable thead .sorting_desc_disabled:before {
        content: "\e5d8" !important; /* Font Awesome icon for ascending sort */
        font-family: 'Material Icons';
        right: 1em !important;
    }

    table.dataTable thead .sorting:after, table.dataTable thead .sorting_asc:after, table.dataTable thead .sorting_desc:after, table.dataTable thead .sorting_asc_disabled:after, table.dataTable thead .sorting_desc_disabled:after {
        content: "\e5db" !important; /* Font Awesome icon for descending sort */
        font-family: 'Material Icons';
        right: 0.5em !important;
    }
  </style>
</head>
<body>
    <div class="container-fluid">
        <div class="main-container">
            <!-- Header and Sidebar -->
            <?php include 'patient_header.php'; ?>
            <!-- Content -->
            <div class="content" id="content">
                <div class="row justify-content-center d-flex"  id="pdfContent" >
                    <div class="col-md-10 mt-0 pt-0 ">
                        <div class="card p-5">
                        <div class="card-body  p-0 align-items-center">
                            <div class="col-md-12" style="border-bottom: 1px solid black;">
                            IMG HEREEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE
                            </div>
                            <div class="col-md-12">
                            Personal Information<br>
                                <div class="row d-flex">
                                    
                                <div class="col-md-6">
                              
                                    Patient Name : Input Name<br>
                                    Age: input age;<br>
                                    Sex: input sex;<br>
                                    Weight: input weight;
                                </div>
                                <div class="col-md-6 justify-content-end d-flex">
                                    Phone Number: Input Number;
                                    <br>
                                    Birth Date: Input Birth Date;
                                </div>
                              

      <div class="col-lg-4 form-group m-0">
      <label for="province">Province<span class="red">*</span></label>
      <select id="provinceSelect" name="province" class="form-control" required >
    <option value="">Select Province</option>
  
</select>
      </div>
      <div class="col-lg-4 form-group m-0">
      <label for="city">City<span class="red">*</span></label>
      <select id="citySelect" name="city" class="form-control" required >
    <option value="">Select City</option>
</select>

      </div>
      <div class="col-lg-4 form-group m-0">
      <label for="address">Address<span class="red">*</span></label>
        <input type="text" id="address" name="address" class="form-control" placeholder="Address" required ><br><br>
      </div>
    
                     

      <div class="col-lg-4 form-group m-0">
        <label for="emergencyContact">In case of Emergency, notify<span class="red">*</span></label>
        <input type="text" id="emergencyContact" name="emergencyContact" placeholder="Full Name" class="form-control" required ><br><br>
      </div>
      <div class="col-lg-4 form-group m-0">
        <label for="relationship">Relationship<span class="red">*</span></label>
        <select id="emergencyContactRelationship" name="emergency_contact_relationship" class="form-control" required >
</select>

      </div>
      <div class="col-lg-4 form-group m-0">
        <label for="emergencyPhoneNumber">Emergency Phone Number<span class="red">*</span></label>
        <input type="tel" id="emergencyPhoneNumber" name="emergencyPhoneNumber" placeholder="Emergency Phone Number" class="form-control" required ><br><br>
      </div>
      </div>
                            

        
                        
                        Bite Exposure Details
               <div class="row d-flex ">
                        <div class="col-lg-6 form-group ">
            <label for="exposureDate">Date of Exposure</label>
            <input type="date" id="exposureDate" name="exposureDate" class="form-control" placeholder="Date of Exposure" max="<?php echo date('Y-m-d'); ?>" required>
        </div>
        <div class="col-lg-6 form-group">
            <label for="exposureDate">Date of Treatment</label>
            <input type="date" id="exposureDate" name="exposureDate" class="form-control" placeholder="Date of Exposure" max="<?php echo date('Y-m-d'); ?>" required>
        </div>
        
    <div class="col-lg-3 form-group m-0">
    <label for="exposureType">Type of Exposure</label>
<select id="exposureType" name="exposureType" class="form-control" required>
    <option value="">Select Type of Exposure</option>
    <option value="Category I">Category I</option>
    <option value="Category II">Category II</option>
    <option value="Category III">Category III</option>
    <option value="Category IV">Category IV</option>
</select>

        </div>
        <div class="col-lg-3 form-group m-0">
            <label for="exposureBy">Exposure</label>
            <select id="exposureBy" name="exposureBy" class="form-control" required>
    <option value="">Select Option</option>
    <option value="Bite">Bite</option>
    <option value="Scratch">Scratch</option>
    <option value="Saliva Contact with Open Wound">Saliva Contact with Open Wound</option>
    <option value="Saliva Contact with Mucous Membrane">Saliva Contact with Mucous Membrane</option>
</select>

        </div>
 
        <div class="col-lg-3 form-group m-0">
            <label for="animalType">Type of Animal</label>
            <input type="text" id="animalType" name="animalType" placeholder="Type of Animal" class="form-control" required><br><br>
        </div>
        <div class="col-lg-3 form-group m-0 ">
            <label for="biteLocation">Bite Location</label>
            <input type="text" id="biteLocation" name="biteLocation" placeholder="Bite Location" class="form-control" required><br><br>
        </div>
        </div>
 
        Treatment Given
        <div class="row d-flex ">
                        <div class="col-lg-6 form-group ">
            <label for="exposureDate">Type of Medicine</label>
            <input type="text" id="exposureDate" name="exposureDate" class="form-control" placeholder="Date of Exposure" max="<?php echo date('Y-m-d'); ?>" required>
        </div>
        <div class="col-lg-6 form-group">
            <label for="exposureDate">Medicine Given</label>
            <input type="text" id="exposureDate" name="exposureDate" class="form-control" placeholder="Date of Exposure" max="<?php echo date('Y-m-d'); ?>" required>
        </div>
        
 
        <div class="col-lg-4 form-group m-0">
            <label for="exposureBy">Treatment Category</label>
            <select id="exposureBy" name="exposureBy" class="form-control" required>
    <option value="">Select Option</option>
    <option value="Bite">Bite</option>
    <option value="Scratch">Scratch</option>
    <option value="Saliva Contact with Open Wound">Saliva Contact with Open Wound</option>
    <option value="Saliva Contact with Mucous Membrane">Saliva Contact with Mucous Membrane</option>
</select>

        </div>
 
        <div class="col-lg-4 form-group m-0">
            <label for="animalType">Sessions</label>
            <input type="text" id="animalType" name="animalType" placeholder="Type of Animal" class="form-control" required><br><br>
        </div>
        <div class="col-lg-4 form-group m-0 ">
            <label for="biteLocation">Route</label>
            <input type="text" id="biteLocation" name="biteLocation" placeholder="Bite Location" class="form-control" required><br><br>
        </div>
        <div class="col-lg-12 form-group">
        <label for="doctorRemarks">Doctor Remarks</label>
        <textarea id="doctorRemarks" name="doctorRemarks" placeholder="Doctor Remarks" class="form-control w-100"></textarea>
    </div>
        </div>
        </div>
        <button id="generatePdfButton">Generate PDF</button>
    
    </div>
    
</div>

                    </div>
                
            </div> <!-- End of content -->
        </div> <!-- End of main-container -->
    </div> <!-- End of container-fluid -->

    <!-- Your script tags here -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src='https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js'></script>
<script src='https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js'></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
document.getElementById('generatePdfButton').addEventListener('click', function() {
    html2pdf().set({
  pagebreak: { mode: 'avoid-all', before: '#page2el' }
});
    var element = document.getElementById('pdfContent');
    var opt = {
    filename:     'myfile.pdf',
    image:        { type: 'jpeg', quality: 10 },
    html2canvas:  { scale: 5 }, // Adjust the scale value as needed
    jsPDF:        { unit: 'in', format: 'a2', orientation: 'portrait' }
};
html2pdf().set(opt).from(element).save();;
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
    $(document).ready(function(){
        $('.navbar-toggler').on('click', function () {
            $('.navbar').toggleClass('navbar-collapsed', $('.collapse').hasClass('show'));
        });
    });
</script>
</body>
</html>