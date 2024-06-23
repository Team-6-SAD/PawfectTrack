<?php
require_once 'backend/pawfect_connect.php';
session_start(); // Start the session
if (isset($_SESSION['error'])) {
    $errorMessage = $_SESSION['error'];
    unset($_SESSION['error']);
}
if (isset($_SESSION['registered'])) {
    unset($_SESSION['registered']);
}
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
    <title>Login Page</title>
    <link rel="icon" href="img/Favicon 2.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
              .white-shadow {
    box-shadow: 0 4px 40px 4px white !important;
}
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
<body>  <div class="container mb-5">
        <div class="row justify-content-center px-5">
            <div class="col-lg-6 mt-3 pb-0 pt-5 px-0 image-container">
                <img src="img/img-login-register/Admin Authentication.png" alt="Admin Authentication">
                <img src="img/img-login-register/PawfectTrackSign.png" alt="Pawfect Track Sign" style="margin-top: -60px; width:400px;">
            </div>
            <div class="col-lg-6  d-flex justify-content-center flex-column mt-5">
                <div class="card form-container white-shadow">
                    <div class="col-md-12 px-3 pb-3 mt-4">
                        <div class="pl-4 pr-4">
                            <div class="header-container mt-4">
                                <h2>Login</h2>
                                <img src="img/img-dashboard/ABC-Sign.png" alt="ABC Sign">
                            </div>
                            <form method="post" action="backend/Login-backend.php" id="loginForm">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="username">Username<span class="red">*</span></label>
                                        <input type="text" id="username" name="username" class="form-control" placeholder="Username" oninput="preventSpaces(event)">
                                        <div id="username-error" class="text-danger"></div>
                                    </div>
                                </div>
  <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Password<span class="red">*</span></label>
                                    <div class="password-input-wrapper">
                                        <input type="password" id="password" name="password" class="form-control" placeholder="Password" oninput="preventSpaces(event)" maxlength="16">
                                        <button type="button" class="toggle-btn" onclick="togglePasswordVisibility('password')">SHOW</button>
                    
                                    </div>
                                    <small id="password-error" class="text-danger"></small>
                                    <div class="d-flex justify-content-end pt-2">
                                            <small><a id="forgot-password" href="#" style="color: #0449A6;">Forgot password?</a></small>
                                        </div>
                                </div>
                            </div>
                                <div class="col-md-12" >
                                <div class="form-group justify-content-center d-flex py-4 "style="border-bottom:2px solid #ECECEC;">
                                    <button type="submit" id="submitBtn"  class="btn btn-primary btn-lg px-5 py-2 pb-2" style="font-size: 15px; border-radius: 30px; background-color: #0449A6;" disabled><b>Login</b></button>
                                </div>
                            </div>
                            <div class="col-md-12 mt-3 mb-5 text-center">
    <small>Don't have an account? <a href="Admin Registration.php" style="color:#0449A6;"><b>Register</b></a></small>
</div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Username and Password Mismatch Modal -->
<div class="modal fade" id="usernamePasswordMismatchModal" tabindex="-1" role="dialog" aria-labelledby="usernamePasswordMismatchModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="usernamePasswordMismatchModalLabel">Login Failed</h5>
        <i data-feather="x-circle" class="text-end featherer" data-dismiss="modal">

</i>
      </div>
      <div class="modal-body">


<h2 style="letter-spacing: -1px; color:#5e6e82;"class="text-center m-0"><b>ACCOUNT NOT FOUND</b></h2>
<div class="text-center">
<small style="letter-spacing: -1px; color:#5e6e82;">The username and password you entered do not match any records.<br></small>
<small style="letter-spacing: -1px; color:#5e6e82;">Please try again or <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#forgotPasswordModal">reset your password</a>.</small>

</div>
<div class="align-items-center justify-content-center d-flex mb-3 mt-3">
<button type="button" style="background-color: #1DD1A1; border:none;" class="btn btn-success px-5 py-2" data-dismiss="modal"><b>OK</b></button>
</div>
</div>
</div>
</div>
</div>
 
<!-- Password Mismatch Modal -->
<div class="modal fade" id="passwordMismatchModal" tabindex="-1" role="dialog" aria-labelledby="passwordMismatchModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="passwordMismatchModalLabel"></h5>
        <i data-feather="x-circle" class="text-end featherer" data-dismiss="modal">
</i>
      </div>

      <div class="modal-body">

        <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>PASSWORD</b></h2>

        <h2 style="letter-spacing: -1px; color:#5e6e82;"class="text-center m-0"><b>MISMATCH</b></h2>
        <div class="text-center">
    <small style="letter-spacing: -1px; color:#5e6e82;">Passwords you have entered do<br></small>
    <small style="letter-spacing: -1px; color:#5e6e82;">NOT match.</small>
    
</div>
    <div class="align-items-center justify-content-center d-flex mb-3 mt-3">
    <button type="button" style="background-color: #1DD1A1; border:none;" class="btn btn-success px-5 py-2" data-dismiss="modal"><b>OK</b></button>
    </div>
    </div>
    </div>
  </div>
</div>

<!-- Forgot Password Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" role="dialog" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="forgotPasswordModalLabel" ></h5>
      <i data-feather="x-circle" class="text-end featherer" data-dismiss="modal">

</i>
        
        </button>
      </div>
      <div class="modal-body">
    <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>FORGOT</b></h2>
    <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>PASSWORD</b></h2>
    <div class="text-center">
        <small style="letter-spacing: -1px; color:#5e6e82;">To reset your own password,<br></small>
        <small style="letter-spacing: -1px; color:#5e6e82;">Enter your email address.</small>
    </div>
    <div class="col-md-12 w-100 px-5">
        <form action="backend/forgot_password.php" method="post" id="emailForgotPassword"> <!-- Change the action to your PHP script -->
            <div class="col-md-12 form-group mt-3 justify-content-center d-flex px-5" style="flex-direction: column;">
                <label for="inputEmail" class="d-block mb-1"><b>Email<span style="color:red;">*</span></b></label>
                <input type="email" name="email" id="inputEmail" class="form-control" placeholder="@gmail.com" maxlength="320">
                <small id="email-error" class="text-danger"></small>
            </div>
            <div class="align-items-center justify-content-center d-flex mb-3">
                <button type="submit" id="forgotPassSubmit" style="background-color: #1DD1A1; border:none;" class="btn btn-success" disabled><b>Submit</b></button>
            </div>
        </form>
    </div>
</div>
</div>
</div>
</div>
<!-- Forgot Password Modal -->
<div class="modal fade" id="forgotPassword2Modal" tabindex="-1" role="dialog" aria-labelledby="forgotPasswordModalLabel2" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="forgotPasswordModalLabel2" ></h5>
      <i data-feather="x-circle" class="text-end featherer" data-dismiss="modal">

</i>
        
        </button>
      </div>
      <div class="modal-body">
    <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>RESET CODE</b></h2>
    <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>DELIVERED</b></h2>
    <div class="text-center">
        <small style="letter-spacing: -1px; color:#5e6e82;">To reset your own password<br></small>
        <small style="letter-spacing: -1px; color:#5e6e82;">Enter reset code</small>
    </div>
    <div class="col-md-12 w-100 px-5">
    <form action="backend/verify_reset_code.php" method="post">
    <div class="col-md-12 form-group mt-3 justify-content-center d-flex px-5" style="flex-direction: column;">
        <label for="resetCode" class="d-block mb-1"><b>Reset Code:<span style="color:red;">*</span></b></label>
        <input type="text" name="resetCode" id="resetCode" class="form-control" placeholder="Reset Code" oninput="preventSpaces(event)">
    </div>
    <div class="align-items-center justify-content-center d-flex mb-3">
        <button type="submit" id="resetCodeSubmit" style="background-color: #1DD1A1; border:none;" class="btn btn-success" disabled><b>Submit</b></button>
    </div>
</form>
    </div>
</div>
</div>
</div>
</div>
<div class="modal fade" id="forgotPassword3Modal" tabindex="-1" role="dialog" aria-labelledby="forgotPasswordModalLabel3" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="forgotPasswordModalLabel3" ></h5>
      <i data-feather="x-circle" class="text-end featherer" data-dismiss="modal">

</i>
        
        </button>
      </div>
      <div class="modal-body">
    <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>NEW</b></h2>
    <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>PASSWORD</b></h2>
    <div class="text-center">
        <small style="letter-spacing: -1px; color:#5e6e82;">Create a new password that you<br></small>
        <small style="letter-spacing: -1px; color:#5e6e82;">don't use on any other site. </small>
    </div>
    <div class="col-md-12 w-100 px-5">
    <form action="backend/update_password.php" method="post" id="updatePasswordForm">
        <div class="col-md-12 form-group mt-3 px-5">
            <label for="newPassword" class="form-label"><b>New Password:<span style="color:red;">*</span></b></label>
            <div class="input-group">
                <input type="password" name="newPassword" id="newPassword" class="form-control" placeholder="New Password" maxlength="16">
                <button type="button" class="toggle-btn" onclick="togglePasswordVisibility('newPassword')">SHOW</button>
            </div>
            <small id="new-password-error" class="text-danger"></small>
        </div>
        <div class="col-md-12 form-group mt-3 px-5">
            <label for="confirmNewPassword" class="form-label"><b>Confirm New Password:<span style="color:red;">*</span></b></label>
            <div class="input-group">
                <input type="password" name="confirmNewPassword" id="confirmNewPassword" class="form-control" placeholder="New Password" maxlength="16">
                <button type="button" class="toggle-btn" onclick="togglePasswordVisibility('confirmNewPassword')">SHOW</button>
            </div>
            <small id="confirm-new-password-error" class="text-danger"></small>
        </div>
        <div class="d-flex justify-content-center mb-3">
            <button type="submit" id="newPasswordSubmission" class="btn btn-success" style="background-color: #1DD1A1; border:none;"><b>Submit</b></button>
        </div>
    </form>
</div>
</div>
</div>
</div>
</div>
<div class="modal fade" id="forgotPasswordSuccessModal" tabindex="-1" role="dialog" aria-labelledby="usernamePasswordMismatchModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="usernamePasswordMismatchModalLabel"></h5>
        <i data-feather="x-circle" class="text-end featherer" data-dismiss="modal">

</i>
      </div>
      <div class="modal-body">

<h2 style="letter-spacing: -1px; color:#5e6e82;"class="text-center m-0"><b>PASSWORD SUCCESSFULLY</b></h2>
<h2 style="letter-spacing: -1px; color:#5e6e82;"class="text-center m-0"><b>CHANGED</b></h2>
<div class="text-center">
<small style="letter-spacing: -1px; color:#5e6e82;">You may now log-in with<br></small>
<small style="letter-spacing: -1px; color:#5e6e82;">your new password.<br></small>
</div>
<div class="align-items-center justify-content-center d-flex mb-3 mt-3">
<button type="button" style="background-color: #1DD1A1; border:none;" class="btn btn-success px-5 py-2" data-dismiss="modal"><b>OK</b></button>
</div>
</div>
</div>
</div>
</div>
<div class="modal fade" id="EmailNotExistModal" tabindex="-1" role="dialog" aria-labelledby="usernamePasswordMismatchModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="usernamePasswordMismatchModalLabel"></h5>
        <i data-feather="x-circle" class="text-end featherer" data-dismiss="modal">

</i>
      </div>
      <div class="modal-body">
        <div class="justify-content-center d-flex"><img src="img/img-login-register/email-not-found.png" style="height:50px; width:auto;"></div>

<h2 style="letter-spacing: -1px; color:#5e6e82;"class="text-center m-0"><b>EMAIL</b></h2>
<h2 style="letter-spacing: -1px; color:#5e6e82;"class="text-center m-0"><b>NOT FOUND</b></h2>
<div class="text-center">
<small style="letter-spacing: -1px; color:#5e6e82;">The email you inserted does not<br></small>
<small style="letter-spacing: -1px; color:#5e6e82;">exist in our records.<br></small>
</div>
<div class="align-items-center justify-content-center d-flex mb-3 mt-3">
<button type="button" style="background-color: #1DD1A1; border:none;" class="btn btn-success px-5 py-2" data-dismiss="modal"><b>OK</b></button>
</div>
</div>
</div>
</div>
</div>
<div class="modal fade" id="CodeNotExistModal" tabindex="-1" role="dialog" aria-labelledby="usernamePasswordMismatchModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="usernamePasswordMismatchModalLabel"></h5>
        <i data-feather="x-circle" class="text-end featherer" data-dismiss="modal">

</i>
      </div>
      <div class="modal-body">

<h2 style="letter-spacing: -1px; color:#5e6e82;"class="text-center m-0"><b>INCORRECT RESET</b></h2>
<h2 style="letter-spacing: -1px; color:#5e6e82;"class="text-center m-0"><b>CODE</b></h2>
<div class="text-center">
<small style="letter-spacing: -1px; color:#5e6e82;">The reset code you inserted is<br></small>
<small style="letter-spacing: -1px; color:#5e6e82;">incorrect.<br></small>
</div>
<div class="align-items-center justify-content-center d-flex mb-3 mt-3">
<button type="button" style="background-color: #1DD1A1; border:none;" class="btn btn-success px-5 py-2" data-dismiss="modal"><b>OK</b></button>
</div>
</div>
</div>
</div>
</div>
<div class="modal fade" id="PasswordNotExistModal" tabindex="-1" role="dialog" aria-labelledby="usernamePasswordMismatchModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="usernamePasswordMismatchModalLabel"></h5>
        <i data-feather="x-circle" class="text-end featherer" data-dismiss="modal">

</i>
      </div>
      <div class="modal-body">

<h2 style="letter-spacing: -1px; color:#5e6e82;"class="text-center m-0"><b>NEW PASSWORD MISMATCH</b></h2>
<div class="text-center">
<small style="letter-spacing: -1px; color:#5e6e82;">The password you inserted does<br></small>
<small style="letter-spacing: -1px; color:#5e6e82;">not match.<br></small>
</div>
<div class="align-items-center justify-content-center d-flex mb-3 mt-3">
<button type="button" style="background-color: #1DD1A1; border:none;" class="btn btn-success px-5 py-2" data-dismiss="modal"><b>OK</b></button>
</div>
</div>
</div>
</div>
</div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
      feather.replace();
    </script>
         <script>
         function togglePasswordVisibility(id) {
            const input = document.getElementById(id);
            const toggleBtn = input.nextElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                toggleBtn.textContent = 'HIDE';
                toggleBtn.style.paddingLeft = '17px'; // Add padding-left
            } else {
                input.type = 'password';
                toggleBtn.textContent = 'SHOW';
                toggleBtn.style.paddingLeft = '10px'; // Add padding-left
            }
        }
        </script>
    <script>
          $(document).ready(function() {
    <?php if(isset($_SESSION['error_no_match'])) { ?>
      $('#PasswordNotExistModal').modal('show');
      <?php unset($_SESSION['error_no_match']); ?> // Unset the session variable
    <?php } ?>
  });
            $(document).ready(function() {
    <?php if(isset($_SESSION['error_message_code'])) { ?>
      $('#CodeNotExistModal').modal('show');
      <?php unset($_SESSION['error_message_code']); ?> // Unset the session variable
    <?php } ?>
  });
         $(document).ready(function() {
    <?php if(isset($_SESSION['error_message'])) { ?>
      $('#EmailNotExistModal').modal('show');
      <?php unset($_SESSION['error_message']); ?> // Unset the session variable
    <?php } ?>
  });
            $(document).ready(function() {
    <?php if(isset($_SESSION['success_message_password'])) { ?>
      $('#forgotPasswordSuccessModal').modal('show');
      <?php unset($_SESSION['success_message_password']); ?> // Unset the session variable
    <?php } ?>
  });
          $(document).ready(function() {
    <?php if(isset($_SESSION['code_verified']) && $_SESSION['code_verified'] === true) { ?>
      $('#forgotPassword3Modal').modal('show');
      <?php unset($_SESSION['code_verified']); ?> // Unset the session variable
    <?php } ?>
  });
          $(document).ready(function() {
    <?php if(isset($_SESSION['success_message'])) { ?>
      $('#forgotPassword2Modal').modal('show');
      <?php unset($_SESSION['success_message']); ?> // Unset the session variable
    <?php } ?>
  });
$(document).ready(function(){
    console.log("Document ready!"); // Debugging statement
    <?php if(isset($errorMessage)): ?>
        console.log("Error exists in session!"); // Debugging statement
        $('#usernamePasswordMismatchModal').modal('show');
        <?php unset($errorMessage); ?> // Clear the error message after showing the modal
    <?php endif; ?>
}); 
</script>
    <script>

document.getElementById('forgot-password').addEventListener('click', function() {
    $('#forgotPasswordModal').modal('show');
});
        </script>
             <script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const username = document.getElementById('username');
    const password = document.getElementById('password');
    const usernameError = document.getElementById('username-error');
    const passwordError = document.getElementById('password-error');


    // Password toggle functionality
    const passwordInput = document.getElementById('newPassword');


    loginForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        // Reset previous error messages and error borders
        usernameError.textContent = '';
        passwordError.textContent = '';

        removeErrorBorder(username);
        removeErrorBorder(password);


        // Validate username
        if (username.value.trim() === '') {
            usernameError.textContent = 'Please enter your username.';
            addErrorBorder(username);
            return;
        }

        // Validate password
        if (password.value.trim() === '') {
            passwordError.textContent = 'Please enter your password.';
            addErrorBorder(password);
            return;
        }




        // If validation passes, submit the form
        this.submit();
    });

    // Add input event listeners for real-time validation
    username.addEventListener('input', function() {
        usernameError.textContent = ''; // Clear previous error message
        removeErrorBorder(username);
        if (username.value.trim() === '') {
            usernameError.textContent = 'Please enter your username.';
            addErrorBorder(username);
        }
    });

    password.addEventListener('input', function() {
        passwordError.textContent = ''; // Clear previous error message
        removeErrorBorder(password);
        if (password.value.trim() === '') {
            passwordError.textContent = 'Please enter your password.';
            addErrorBorder(password);
        }
    });



    function addErrorBorder(element) {
        element.classList.add('error-border');
    }

    function removeErrorBorder(element) {
        element.classList.remove('error-border');
    }
});
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('#emailForgotPassword');
    const emailInput = form.querySelector('#inputEmail');
    const emailError = form.querySelector('#email-error');
    const submitButton = form.querySelector('#forgotPassSubmit');

    const emailRegex = /^[a-zA-Z0-9._+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    const allowedDomains = ['@gmail.com', '@outlook.com', '@yahoo.com'];

    emailInput.addEventListener('input', function() {
        // Clear previous error message
        emailError.textContent = '';

        const email = emailInput.value.trim().toLowerCase();

        // Check if the email matches the valid email format
        if (!emailRegex.test(email)) {
            emailError.textContent = 'Please enter a valid email address.';
            submitButton.disabled = true;
            return;
        }

        // Check if the email ends with one of the allowed domains
        const isValidDomain = allowedDomains.some(domain => email.endsWith(domain));
        if (!isValidDomain) {
            emailError.textContent = 'Please enter a valid email from Gmail, Outlook, or Yahoo.';
            submitButton.disabled = true;
            return;
        }

        // If all checks pass, enable the submit button
        submitButton.disabled = false;
    });

    emailInput.addEventListener('keydown', function(event) {
        // Prevent space character from being entered
        if (event.key === ' ') {
            event.preventDefault();
        }
    });

    form.addEventListener('submit', function(event) {
        if (emailError.textContent !== '') {
            event.preventDefault();
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
    document.addEventListener('DOMContentLoaded', function() {
        const newPasswordInput = document.getElementById('newPassword');
        const confirmNewPasswordInput = document.getElementById('confirmNewPassword');
        const newPasswordError = document.getElementById('new-password-error');
        const confirmNewPasswordError = document.getElementById('confirm-new-password-error');
        const submitPasswordButton = document.getElementById('newPasswordSubmission');
        const updatePasswordForm = document.getElementById('updatePasswordForm');

        const passwordRegex = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*()_+])[A-Za-z\d!@#$%^&*()_+]{8,16}$/;

        function validatePassword(password) {
            return passwordRegex.test(password);
        }

        function showPasswordError(errorElement, message) {
            errorElement.textContent = message;
            submitPasswordButton.disabled = true; // Disable submit button
        }

        function clearPasswordError(errorElement) {
            errorElement.textContent = '';
            submitPasswordButton.disabled = false; // Enable submit button
        }

        function checkPasswordsMatch() {
            const newPassword = newPasswordInput.value;
            const confirmNewPassword = confirmNewPasswordInput.value;

            if (newPassword !== confirmNewPassword) {
                showPasswordError(confirmNewPasswordError, 'Passwords do not match.');
            } else {
                clearPasswordError(confirmNewPasswordError);
            }
        }

        newPasswordInput.addEventListener('input', function() {
            const newPassword = newPasswordInput.value;

            if (!validatePassword(newPassword)) {
                showPasswordError(newPasswordError, 'Password must be 8-16 characters and include letters, numbers, and symbols.');
            } else {
                clearPasswordError(newPasswordError);
            }

            // Check if passwords match whenever a new password is entered
            checkPasswordsMatch();
        });

        confirmNewPasswordInput.addEventListener('input', function() {
            // Check if passwords match whenever the confirm password is entered
            checkPasswordsMatch();
        });

        // Prevent space character from being entered in password fields
        newPasswordInput.addEventListener('keydown', function(event) {
            if (event.key === ' ') {
                event.preventDefault();
            }
        });

        confirmNewPasswordInput.addEventListener('keydown', function(event) {
            if (event.key === ' ') {
                event.preventDefault();
            }
        });

        // Prevent form submission if there are errors
        updatePasswordForm.addEventListener('submit', function(event) {
            const newPassword = newPasswordInput.value;
            const confirmNewPassword = confirmNewPasswordInput.value;

            if (!validatePassword(newPassword)) {
                showPasswordError(newPasswordError, 'Password must be 8-16 characters and include letters, numbers, and symbols.');
                event.preventDefault(); // Prevent default form submission
                return;
            }

            if (newPassword !== confirmNewPassword) {
                showPasswordError(confirmNewPasswordError, 'Passwords do not match.');
                event.preventDefault(); // Prevent default form submission
                return;
            }
        });
    });
</script>
<script>
        function validateForm() {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();
        const submitBtn = document.getElementById('submitBtn');

        // Check if both fields are not empty
        if (username && password) {
            submitBtn.disabled = false; // Enable the button
        } else {
            submitBtn.disabled = true; // Disable the button
        }
    }

    // Add event listeners to input fields
    document.getElementById('username').addEventListener('input', validateForm);
    document.getElementById('password').addEventListener('input', validateForm);

    // Initial call to disable the button if fields are empty on page load
    document.addEventListener('DOMContentLoaded', function() {
        validateForm();
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const resetCodeInput = document.querySelector('#resetCode');
    const submitButton = document.querySelector('#resetCodeSubmit');

    resetCodeInput.addEventListener('input', function() {
        // Enable the submit button if the reset code has 6 or more characters
        if (resetCodeInput.value.length >= 6) {
            submitButton.disabled = false;
        } else {
            submitButton.disabled = true;
        }
    });
});
</script>
</body>
</html>
