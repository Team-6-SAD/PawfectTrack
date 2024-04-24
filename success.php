<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Login</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet"> 
    <style>
        .input-error {
            border: 2px solid red !important;
        }
        body {
            font-family: 'Open Sans', sans-serif; 
            background-color: #14213D;
        }
        .navbar {
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.5); 
            border-bottom: 2px solid #0b0b22;
        }
        main {
            color: #000;
        }
        .box {
            background-color: #E1E5FA; 
            padding: 20px; 
            border: 1px solid #000; 
            border-radius: 20px;
            box-shadow: 35px 15px 62px 23px #00000086;
        }
        .input-with-icon {
            position: relative;
        }
        .input-icon {
            position: absolute;
            top: 60%; /* Adjust the vertical position */
            left: 10px;
            transform: translateY(-50%);
        }
        .error-message {
            color: red;
            font-size: small;
            margin-left: 27px;
            margin-top: 5px; /* Adjust the spacing from the input */
            position: absolute;
        }
        .SignInButton {
            background-color: #D9D9D9;
            color: white;
            border: none;
            border-color: #14213D;
            border-radius: 28px;
            padding-top: 8px;
            padding-bottom: 8px;
            padding-left: 43px;
            padding-right: 43px;
            color: #000;
            cursor: pointer;
            transition: opacity 1s;
            margin-left: 135px;
            font-family: 'Open Sans';
            font-weight: bold;
        }
        .SignInButton:hover {
            color: #FCA311;
        }
        .SignInButton:active {
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #14213D;">
            <a class="navbar-brand ml-5" href="landingpagelegit.php" style="color: #FCA311;">
                <img src="Group.svg" alt="Your Logo" width="30" height="30"> 
                I.T.M.S
            </a>
            
            <!--<ul class="navbar-nav ml-auto">
                <li class="nav-item" style="margin-right: 35px;"> 
                    <a class="nav-link" href="login.html"  style="color: #FCA311;">Login</a>
                </li>
                <li class="nav-item" style="margin-right: 35px;">
                    <a class="nav-link" href="#Features" style="color: #FCA311;">Features</a>
                </li>
                <li class="nav-item" style="margin-right: 70px;"> 
                    <a class="nav-link" href="#contact" style="color: #FCA311;">Contact</a>
                </li>
            </ul> -->
        </nav>
    </header>
    
    <main>
        <div class="container" style="margin-top: 90px; display: flex; justify-content: center; align-items: center;">
            <div class="row">
                <div class="col-md-12 text-center">
                    <img src="Partypopper.png" alt="Your Image Description" class="img-fluid pl-2" style="width: 300px; height: 200px;">
                    
                        <h5 style="color: #FCA311; margin-top:30px">Thank you for filling up the form!</h5>
                        <p style="color: #FCA311;">You will be notified about the result of your application through your email.</p>
                        <a href="landingpagelegit.php" class="btn btn-primary mb-3 mt-3 pl-2 pt-2 pb-2 pr-2" style=" background-color: #FCA311;  border-radius: 20px; color: #000000;">Back to Landing Page</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.bundle.js"></script>
</body>
</html>
 