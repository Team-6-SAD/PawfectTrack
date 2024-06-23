<?php
session_start();
if (isset($_SESSION['admin']) || isset($_SESSION['adminID'])) {
    // Redirect the user to the login page
    header("Location: admindashboard.php");
    exit(); // Terminate the script
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <link rel="icon" href="img/Favicon 2.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>


     body{
        font-family: "Rubik";
        background-color: #EAEFF6;
   
        background-position: center center;
     }
     input.form-control{
            background-color: white !important;
            border: #ECECEC solid 2px;
            border-radius: 0;
            height: 35px;
        }
     .red{
        color: red;
     }
    

     .password-input-wrapper {
            display: flex;
            align-items: center;
        }

        .password-input-wrapper input {
            flex: 1;
        }

.password-input-container {
    position: relative;
  }
  

  ::-ms-reveal {
  display: none !important;
}

  .toggle-password , .toggle-confirm-password {
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    cursor: pointer;
  }
     .padding-top{
        padding-top:120px;
     }
     .glow-on-focus {
        border-color: #719CF2 ; /* Add !important to override existing styles if necessary */
        box-shadow: 0 0 10px #719CF2 !important; /* Add !important to override existing styles if necessary */
    }

    .error-border {
    border-color: red !important; /* Change border color to red */
    /* Add any other styling for error indication */
}


    .pos-fix{
        position:relative;

    }
    .element {
    box-shadow: 4px 4px 4px rgba(0, 0, 0, 0.2);
    /* Other styles for the element */
}
    .margin-bottom{
        margin-bottom: 100px;
    }

        .image-container {
            display: flex;
            flex-direction: column;
            align-items: center;
         
        }
        .image-container img {
            max-width: 100%;
            height: auto;
        }
        .form-container {
            border-radius: 19px;
            position: relative;
            z-index: 10;
            background-color: #fff;
        }
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        
        }

        .header-container h2 {
            color: #5E6E82;
            font-weight: 800;
            margin-bottom: 0px;
        }

        .header-container img {
            margin-left: 10px;
            max-width: 100px; /* Adjust size as needed */
            height: auto;
        }
        
    
        .form-control::placeholder{
            color:#ECECEC;
            font-size: 12px;
        }
        .card{
            border:none;
        }
        .toggle-btn {
            height:35px;
            padding: 5px 12px;
            border: 2px solid #ECECEC;
            border-left:none;
            background-color: #f8f8f8;
            cursor: pointer;
        }
        label {
    font-size: 12px;  /* Change the font size to 16px for all labels */
}
.form-group{
    margin-bottom: 0.5rem;
}
   

    </style>

</head>
<body>

<div class="row mt-5">
    <div class="col-md-12 justify-content-center d-flex align-items-center mt-5">
        <img src="img/img-login-register/Admin Success Page.png" style="height: 300px; width:300px;" class="img-fluid">
    </div>
    <div class="col-md-12 justify-content-center d-flex align-items-center">
    <h3 style="font-weight:bolder; color:#1DD1A1;"> Successfully Registered! </h3>


</div>
<div class="col-md-12 justify-content-center d-flex align-items-center">
<h6 style="font-weight: normal;">You can now access your account by</h6>

</div>
<div class="col-md-12 justify-content-center d-flex align-items-center">

<h6 style="font-weight: normal;">Logging into the PawfectTrack login page.</h6>
</div>
<div class="col-md-12 justify-content-center d-flex align-items-center">
<button type="button" class="btn btn-success px-3" style="background-color:#1DD1A1; border-radius:20px; border:none; font-size:15px; font-weight:bold;" onclick="window.location.href='Admin Login.php'">
            Login <i data-feather="arrow-right-circle" style="width:15px; height:15px; margin-left:5px;"></i>
        </button>

</div>

<script>
  feather.replace();
</script>
</body>
</html>
