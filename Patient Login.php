<?php
require_once 'pawfect_connect.php';
session_start(); // Start the session
$error = isset($_SESSION['error']) ? $_SESSION['error'] : "";
unset($_SESSION['error']); // Clear the error message from session
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>

     body{
        font-family: "Poppins";
        background-color: #EAEFF6;
        
        background-position: center center;
     }
     .red{
        color: red;
     }
     .custom{
            min-width: 15rem;
     }
     @media screen and (max-width: 1200px) {
        .custom{
            min-width: 13rem;
     }
     
     
     }
     @media screen and (max-width: 990px) {
        .custom{
            min-width: 11rem;
     }
     
     }
     .container{
        background-image: url('Group 2314.png');
        background-size:cover;
        background-position:center;
        background-attachment: scroll;
        background-repeat: no-repeat;
      
     }
     @media screen and (max-width: 768px) {
        .container{
            background-image: url('Group 2314.png');
        background-size:contain;
        background-repeat:repeat-y;
        background-position:  left center;
     }
}
  
    .pos-fix{
        position:relative;

    }
    .element {
    box-shadow: 4px 4px 4px rgba(0, 0, 0, 0.2);
    /* Other styles for the element */
}


.password-input-container {
    position: relative;
  }

  .toggle-password , .toggle-confirm-password {
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    cursor: pointer;
  }
  input.form-control {
            border: 1px solid #EAEFF6; /* Light gray border */
            background-color: #F9FAFD; /* Light blue background */
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center margin-bottom px-5 pb-5 ">
    
        <div class="row justify-content-center  ">
           
            <div class="card pos-fix col-md-10 col-lg-9 mt-5 element " style="border-radius: 19px;">
                <div class="row">
                <div class="col-md-4 p-3" style="background-color: #10AC84;">
                 <div class="col-md-12 mt-5 align-items-center justify-content-center d-flex">
                    <img src="Login-logo.png.png">
                </div>
                <div class="col-md-12 align-items-center justify-content-center d-flex">
                 <h4 style="color: white; white-space:nowrap;"> <b>Pawfect Track </b></h4>
                </div>
                <div class="col-md-12 align-items-center justify-content-center d-none d-sm-none d-md-block d-lg-block  pb-3">
                    <span class="text-center d-sm-none d-md-block d-lg-block" style="color: white; font-size: 12px;"> Pawfect Track is an Anti-Rabies Vaccination Record System and Inventory Management with Predictive Analytics </span>
                   </div>
                
              <div class="justify-content-center d-none align-items-center d-sm-none d-md-flex d-lg-flex d-xl-flex">
                   <div class="card mt-5 px-2 py-1" style="border-radius: 20px;" >
                   <div class="col-md-12 align-items-center justify-content-center d-flex mb-3" >
                    <img src="Group 2312.png" width="150" height="90" style="min-height: 100;min-width:170; ">
                    </div>
                </div>
          
                </div>
              
            </div>
            
                <div class="col-md-8 px-3 py-3 mt-5">
                    <div class="pl-4 pr-4">
                    <h4 class="text-center pb-4" style="color:#5E6E82;"><b>Account Login</b></h4>
                    <?php if(!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                    <form method="post" action="PatientLogin-backend.php" id="loginForm">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="last-name"><b>Username</b><span class="red">*</span></label>
                            <input type="text" id="username" name="username" class="form-control" placeholder="Username">
                            <div id="username-error" class="text-danger"></div>
                        </div>
                        
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                      <label for="password"><b>Password</b><span class="red">*</span></label>
                      <div class="password-input-container">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                        <i class="toggle-password fas fa-eye"></i>
                    </div>
                    <div id="password-error" class="text-danger"></div>
                        <div class="d-flex justify-content-end pt-2">
                            <small><a href="#" style="color: #0449A6;">Forgot password?</a></small>
                        </div>
                      
                    </div>
                  </div>
                
            <div class="col-md-12">
                <div class="form-group">
                    <label for="password"><b>Confirm Password</b><span class="red">*</span></label>
                    <div class="password-input-container">
                      <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Password">
                      <i class="toggle-confirm-password fas fa-eye"></i>
                    </div>
                  </div>
                    
            </div>
            <div class="col-md-12 mt-5 mb-0">
                <div class="form-group  justify-content-center d-flex">
                   <button type="submit" class="btn btn-success btn-lg px-5 py-2 pb-2" style="font-size: small; border-radius: 8px; background-color: #10AC84;"><b>Login</b></button>
                </form>
                </div>
                
        </div>
    </div>  
      
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const togglePassword = document.querySelector('.toggle-password');
        
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        });
        </script>
         <script>
            document.addEventListener('DOMContentLoaded', function() {
                const passwordInput = document.getElementById('confirmPassword');
                const togglePassword = document.querySelector('.toggle-confirm-password');
            
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            });
            </script>
         <script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginForm = document.getElementById('loginForm');

        loginForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            const username = document.getElementById('username');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirmPassword');
            const usernameError = document.getElementById('username-error');
            const passwordError = document.getElementById('password-error');

            // Reset previous error messages
            usernameError.textContent = '';
            passwordError.textContent = '';

            // Validate username
            if (username.value.trim() === '') {
                usernameError.textContent = 'Please enter your username.';
                return;
            }

            // Validate password
            if (password.value.trim() === '') {
                passwordError.textContent = 'Please enter your password.';
                return;
            }

            // Validate if password and confirm password match
            if (password.value.trim() !== confirmPassword.value.trim()) {
                passwordError.textContent = 'Passwords do not match.';
                return;
            }

            // If validation passes, submit the form
            this.submit();
        });
    });
</script>

</body>
</html>
