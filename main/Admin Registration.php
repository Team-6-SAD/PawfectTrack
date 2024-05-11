<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <link rel="icon" href="img/Favicon 2.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
     body{
        font-family: "Poppins";
        background-color: #EAEFF6;
        
        background-position: center center;
     }
     .red{
        color: red;
     }
    
     .error-border {
    border-color: red !important; /* Change border color to red */
    /* Add any other styling for error indication */
}

     
     .container{
        background-image: url('img/img-login-register/Group 2313.png');
        background-size:contain;
        background-position:top;
        background-repeat: no-repeat;
        
     }
     @media screen and (max-width: 768px) {
        .container{
        background-image: url('img/img-login-register/Group 2313.png');
        background-size:cover;
        background-repeat:repeat-y;
        background-position:  left center;
     }
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
    input.form-control {
            border: 1px solid #EAEFF6; /* Light gray border */
            background-color: #F9FAFD; /* Light blue background */
            border-radius: 0px !important;
        }
    </style>
</head>
<body>
    
    <div class="container d-flex justify-content-center align-items-center margin-bottom">
    
        <div class="row justify-content-center ">
           
            <div class="card pos-fix col-md-10 col-lg-9 mt-5 element " style="border-radius: 19px;">
                <div class="row">
                <div class="col-md-4 p-3" style="background-color: #0449A6;">
                 <div class="col-md-12 mt-5 align-items-center justify-content-center d-flex mb-2">
                    <img src="img/img-login-register/Login Logo.png" width="90px" height="78px">
                </div>
                <div class="col-md-12 align-items-center justify-content-center d-flex">
                 <h4 style="color: white; white-space:nowrap;"> <b>Pawfect Track </b></h4>
                </div>
                <div class="col-md-12 align-items-center justify-content-center d-none d-sm-none d-md-block d-lg-block mb-5 pb-5">
                    <span class="text-center d-sm-none d-md-block d-lg-block" style="color: white; font-size: 12px;"> Pawfect Track is an Anti-Rabies Vaccination Record System and Inventory Management with Predictive Analytics </span>
                   </div>
                
                   <div class="justify-content-center d-none align-items-center d-sm-none d-md-flex d-lg-flex d-xl-flex padding-top">
                    <div class="card mt-5 px-2 py-1" style="border-radius: 20px;" >
                    <div class="col-md-12 align-items-center justify-content-center d-flex mb-3" >
                     <img src="img/img-dashboard/ABC-Sign.png" width="150" height="90" style="min-height: 100;min-width:170; ">
                     </div>
                 </div>
          
                </div>
                <div class="col-md-12 align-items-center justify-content-center d-flex mt-5">
                    <span class="text-center" style="color: white; font-size: 12px;"> Already have an Account? </span>
                </div>
                <div class="justify-content-center d-flex mb-5">
                <a class="text-center" style="text-decoration: underline;"  href="Admin Login.php">Login</a>
                </div>
            </div>
            
                <div class="col-md-8 px-5 py-3 mt-5">
                    <div class="pl-4 pr-4">
                        <h4 class="text-center pb-4" style="color:#5E6E82;"><b> Register</b></h4>
                        <div id="alertMessages" class="mt-1"> </div>
                        <form id="registrationForm" method="post" action="backend/pawfectRegistration.php">
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="first-name"><b>First Name</b><span class="red">*</span></label>
                                <input type="text" id="first-name" name="first-name" class="form-control" placeholder="First Name" oninput="preventLeadingSpace(event)" maxlength="50">
                                <small id="first-name-error" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="middle-name"><b>Middle Name</b></label>
                                <input type="text" id="middle-name" name="middle-name" class="form-control" placeholder="Middle Name" oninput="preventLeadingSpace(event)" maxlength="50">
                                <small id="middle-name-error" class="text-danger"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="last-name"><b>Last Name</b><span class="red">*</span></label>
                                <input type="text" id="last-name" name="last-name" class="form-control" placeholder="Last Name" oninput="preventLeadingSpace(event)" maxlength="50">
                                <small id="last-name-error" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username"><b>Username</b><span class="red">*</span></label>
                                <input type="text" id="username" name ="username" class="form-control" placeholder="Username"   oninput="preventSpaces(event)" maxlength="16">
                                <small id="username-error" class="text-danger"></small>                            
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="phone-number"><b>Phone Number</b><span class="red">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="color: white; background-color: #5E6E82; font-size: 14px;"><b>PH </b></span>
                                    </div>
                                    
                                    <input type="text" id="phone-number" name="phone-number" class="form-control" placeholder="09123456789" >
                                   
                                </div>
                                <small id="phone-number-error" class="text-danger"></small>
                           
                            </div>
                        </div>
                        
                    
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="email"><b>Email</b><span class="red">*</span></label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="@gmail.com" >
                                <small id="email-error" class="text-danger"></small>
                            </div>
                        </div>
                        
            
                        <div class="col-md-12">
                            <div class="form-group">
                              <label for="password"><b>Password</b><span class="red">*</span></label>
                              <div class="password-input-container">
                                <input type="password" id="password" name="password" class="form-control" placeholder="Password" oninput="preventSpaces(event)" maxlength="16">
                                <i class="toggle-password fas fa-eye"></i>
                
                            </div>
                            <small id="password-error" class="text-danger"></small>
                            </div>
                          </div>
                        
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="password"><b>Confirm Password</b><span class="red">*</span></label>
                            <div class="password-input-container">
                              <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Confirm Password" oninput="preventSpaces(event)" maxlength="16">
                              <i class="toggle-confirm-password fas fa-eye"></i>
                            </div>
                            <small id="confirmPassword-error" class="text-danger"></small>
                          </div>
                            
                    </div>
                <div class="col-md-12 mt-3 mb-0">
                    <div class="form-group  justify-content-center d-flex">
                       <button type="submit" class="btn btn-primary btn-lg px-5 py-2 pb-2" style="font-size: small; border-radius: 8px; background-color: #0449A6;"><b>Register</b></button>
            
                    </form>
                    </div>
                    
            </div>
                
        </div>
    </div>  
      
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const formFields = document.querySelectorAll('input, select, textarea');
    
            formFields.forEach(function(field) {
                field.addEventListener('focus', function () {
                    this.classList.add('glow-on-focus');
                });
    
                field.addEventListener('blur', function () {
                    this.classList.remove('glow-on-focus');
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const phoneNumberInput = document.getElementById('phone-number');
            
            const inputGroupPrepend = phoneNumberInput.parentElement.querySelector('.input-group-prepend');
    
            phoneNumberInput.addEventListener('focus', function () {
                inputGroupPrepend.classList.add('glow-on-focus');
                phoneNumberInput.classList.add('glow-on-focus');
            });
    
            phoneNumberInput.addEventListener('blur', function () {
                inputGroupPrepend.classList.remove('glow-on-focus');
                phoneNumberInput.classList.remove('glow-on-focus');
            });
        });
    </script>
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
        function validateForm() {
            // Get form inputs
            const firstName = document.getElementById('first-name');
            const middleName = document.getElementById('middle-name');
            const lastName = document.getElementById('last-name');
            const username = document.getElementById('username');
            const phoneNumber = document.getElementById('phone-number');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirmPassword');

            // Remove existing error styles
            removeError([firstName, middleName, lastName, username, phoneNumber, email, password, confirmPassword]);

            // Validation flag
            let isValid = true;

            // Validate First Name
            if (firstName.value.trim() === '' && touchedFields['first-name']) {
                isValid = false;
                showError(firstName, 'Please enter your First Name.');
            }

            // Validate Last Name
            if (lastName.value.trim() === '' && touchedFields['last-name']) {
                isValid = false;
                showError(lastName, 'Please enter your Last Name.');
            }

            // Validate Username
// Validate Username
// Validate Username
if (username.value.trim() === '' && touchedFields['username']) {
    isValid = false;
    showError(username, 'Please enter a Username.');
} else if ((username.value.trim().length < 8 || username.value.trim().length > 16) && touchedFields['username'])  {
    isValid = false;
    showError(username, 'Username must be between 8 and 16 characters.');
}



            // Validate Phone Number
            if ((phoneNumber.value.trim() === '' || !validatePhoneNumber(phoneNumber.value.trim())) && touchedFields['phone-number']) {
                isValid = false;
                if (phoneNumber.value.trim() === '') {
                    showError(phoneNumber, 'Please enter your Phone Number.');
                } else {
                    showError(phoneNumber, 'Please enter a valid Phone Number starting with "09" and containing exactly 11 digits.');
                }
            }

            // Validate Email
            if ((email.value.trim() === '' || !validateEmail(email.value.trim())) && touchedFields['email']) {
                isValid = false;
                if (email.value.trim() === '') {
                    showError(email, 'Please enter your Email.');
                } else {
                    showError(email, 'Please enter a valid Email.');
                }
            }

            // Validate Password
            if ((password.value.trim() === '' || !validatePassword(password.value.trim())) && touchedFields['password']) {
    isValid = false;
    if (password.value.trim() === '') {
        showError(password, 'Please enter a Password.');
    } else {
        showError(password, 'Password must be 8-16 characters long and contain at least one letter, one number, and one special character (@$!%*?&).');
    }
}

            // Validate Confirm Password
            if (confirmPassword.value.trim() === '' && touchedFields['confirmPassword']) {
                isValid = false;
                showError(confirmPassword, 'Please confirm your Password.');
            } else if (password.value.trim() !== confirmPassword.value.trim()) {
                isValid = false;
                showError(confirmPassword, 'Passwords do not match.');
            }

            return isValid;
        }
        const registrationForm = document.getElementById('registrationForm');

        // Create an object to track the touched state of each input field
        const touchedFields = {
            'first-name': false,
            'middle-name': false,
            'last-name': false,
            'username': false,
            'phone-number': false,
            'email': false,
            'password': false,
            'confirmPassword': false
        };

        // Define a function to handle form validation
       

        // Add event listeners for real-time validation
        const formFields = document.querySelectorAll('input, select, textarea');
        formFields.forEach(function(field) {
            field.addEventListener('input', function () {
                // Set the touched flag to true for the current field
                touchedFields[field.id] = true;
                validateForm(); // Trigger form validation on input event
            });
        });

        // Add submit event listener to the form
        registrationForm.addEventListener('submit', function(event) {
            // Set the touched flag to true for all fields when the form is submitted
            for (const field in touchedFields) {
                touchedFields[field] = true;
            }

            // Prevent form submission if the form is not valid
            if (!validateForm()) {
                event.preventDefault();
            }
        });
    

    // Function to validate email format
    function validateEmail(email) {
    const re = /^[\w-]+(\.[\w-]+)*@(?:(?:gmail|yahoo|outlook)\.com)$/;
    return re.test(email);
}


    // Function to validate phone number format
    function validatePhoneNumber(phoneNumber) {
        const re = /^09\d{9}$/; // Matches "09" followed by 9 digits
        return re.test(phoneNumber);
    }
    function validatePassword(password) {
    const re = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,16}$/;
    return re.test(password);
}

$(document).ready(function() {
    // Submit form via AJAX
    $('#registrationForm').submit(function(event) {
        // Prevent default form submission
        event.preventDefault();

        // Validate form
        if (!validateForm()) {
            return; // Exit if validation fails
        }

        // AJAX request to register.php
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(), // Serialize form data
            success: function(response) {
                console.log(response);
                try {
                    var result = JSON.parse(response);
                    $('#alertMessages').html('');
                    if (result.status === 'success') {
                        // Redirect on success
                        window.location.href = 'Admin Login.php';
                    } else if (result.status === 'error') {
                        // Display error message
                        $('#alertMessages').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            result.message +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span>' +
                            '</button>' +
                            '</div>');
                    }
                } catch (error) {
                    // Handle parsing error here, maybe display a generic error message
                    console.error('Error parsing JSON:', error);
                }
            }
        });
    });
});



  

    // Function to show error message and apply error styles
    function showError(input, message) {
    const errorElement = document.getElementById(input.id + '-error');
    if (errorElement) {
        errorElement.textContent = message;
        if (input.id === 'password' || input.id === 'confirmPassword') {
            input.classList.add('error-border'); // Add error-border class for password fields
        } else {
            input.classList.add('is-invalid'); // Add is-invalid class for other fields
        }
    }
}


    // Function to remove error styles
    function removeError(inputs) {
        // Ensure inputs is an array
        if (!Array.isArray(inputs)) {
            inputs = [inputs]; // Convert single input to an array with one element
        }

        inputs.forEach(function (input) {
            input.classList.remove('is-invalid');
            input.classList.remove('error-border');
            const errorElement = document.getElementById(input.id + '-error');
            if (errorElement) {
                errorElement.textContent = '';
            }
        });
    }

$(document).ready(function() {
    
    // Submit form via AJAX
    $('#registrationForm').submit(function(event) {
        // Prevent default form submission
        event.preventDefault();

        // Validate form
        if (!validateForm()) {
            return; // Exit if validation fails
        }

        // AJAX request to register.php
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(), // Serialize form data
            success: function(response) {
                console.log(response);
                try {
                    var result = JSON.parse(response);
                    $('#alertMessages').html('');
                    if (result.status === 'success') {
                        // Redirect on success
                        window.location.href = 'Admin Login.php';
                    } else if (result.status === 'error') {
                        // Display error message
                        $('#alertMessages').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            result.message +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span>' +
                            '</button>' +
                            '</div>');
                    }
                } catch (error) {
                    // Handle parsing error here, maybe display a generic error message
                    console.error('Error parsing JSON:', error);
                }
            }
        });
    });
});
});

</script>
<script>
  
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var phoneNumberInput = document.getElementById("phone-number");
        var phoneNumberError = document.getElementById("phone-number-error");

        phoneNumberInput.addEventListener("keypress", function(event) {
            var key = event.key;
            if (!/^\d$/.test(key)) {
                event.preventDefault();
                phoneNumberError.textContent = "Please enter numbers only.";
            } else {
                phoneNumberError.textContent = "";
            }
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var emailInput = document.getElementById("email");
        var emailError = document.getElementById("email-error");

        emailInput.addEventListener("keypress", function(event) {
            if (event.key === ' ') {
                event.preventDefault();
            }
        });

        emailInput.addEventListener("input", function() {
            var inputValue = emailInput.value.trim(); // Remove leading and trailing spaces
            var atIndex = inputValue.indexOf('@');
            var domainPart = atIndex !== -1 ? inputValue.substring(atIndex) : "";

            var allowedDomains = ["@gmail.com", "@yahoo.com", "@outlook.com"];
            var isValid = atIndex > 0 && allowedDomains.includes(domainPart);

            if (!isValid) {
                emailError.textContent = "Please enter a valid email address with @gmail.com, @yahoo.com, or @outlook.com domain.";
            } else {
                emailError.textContent = "";
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





</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const phoneNumberInput = document.getElementById('phone-number');

    phoneNumberInput.addEventListener('input', function(event) {
        const input = event.target;
        const maxLength = 11; // Maximum allowed length

        if (input.value.length > maxLength) {
            input.value = input.value.slice(0, maxLength); // Truncate input if it exceeds maxLength
        }
    });
});

    function preventSpaces(event) {
        const input = event.target;
        if (input.value.includes(' ')) {
            input.value = input.value.replace(/\s/g, ''); // Remove all spaces
        }
    }
</script>

</body>
</html>
