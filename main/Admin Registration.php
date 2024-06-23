<?php
session_start();
if (isset($_SESSION['registered']) && $_SESSION['registered'] === true) {
    header('Location: Registration Success.php');
    exit();
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
    <title>Registration Page</title>
    <link rel="icon" href="img/Favicon 2.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
<body>
<div class="container mb-5">
    <div class="row justify-content-center px-5 ">
        <div class="col-lg-6 mt-3 pb-0 pt-5 px-0 image-container">
            <img src="img/img-login-register/Admin Authentication.png" alt="Admin Authentication" style="height:522px; width:600px;">
            <img src="img/img-login-register/PawfectTrackSign.png" alt="Pawfect Track Sign" style="margin-top: -60px; width:450px;">
        </div>
        <div class="card pos-fix col-lg-6 mt-5 form-container white-shadow px-4">
            <div class="col-md-12 px-3 pb-3 mt-4">
                <div class="pl-4 pr-4">
                <div class="header-container">
                        <h2>Register</h2>
                        <img src="img/img-dashboard/ABC-Sign.png" alt="ABC Sign">
                    </div>
                    <div id="alertMessages" class="mt-1"></div>
                    <form id="registrationForm" method="post" action="backend/pawfectRegistration.php">
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first-name">First Name<span class="red">*</span></label>
                                    <input type="text" id="first-name" name="first-name" class="form-control" placeholder="First Name" oninput="preventLeadingSpace(event)" maxlength="50">
                                    <small id="first-name-error" class="text-danger"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="middle-name">Middle Name</label>
                                    <input type="text" id="middle-name" name="middle-name" class="form-control" placeholder="Middle Name" oninput="preventLeadingSpace(event)" maxlength="50">
                                    <small id="middle-name-error" class="text-danger"></small>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last-name">Last Name<span class="red">*</span></label>
                                    <input type="text" id="last-name" name="last-name" class="form-control" placeholder="Last Name" oninput="preventLeadingSpace(event)" maxlength="50">
                                    <small id="last-name-error" class="text-danger"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username">Username<span class="red">*</span></label>
                                    <input type="text" id="username" name="username" class="form-control" placeholder="Username" oninput="preventSpaces(event)" maxlength="16">
                                    <small id="username-error" class="text-danger"></small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="phone-number">Phone Number<span class="red">*</span></label>
                                    <input type="tel" id="phone-number" name="phone-number" class="form-control" placeholder="09123456789" maxlength="11">
                                    <small id="phone-number-error" class="text-danger"></small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="email">Email<span class="red">*</span></label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="@gmail.com" maxlength="320">
                                    <small id="email-error" class="text-danger"></small>
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
                                </div>
                            </div>
                            <div class="col-md-12">
        <div class="form-group">
            <label for="confirmPassword">Confirm Password<span class="red">*</span></label>
            <div class="password-input-wrapper">
                <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Confirm Password" oninput="preventSpaces(event)" maxlength="16">
                <button type="button" class="toggle-btn" onclick="togglePasswordVisibility('confirmPassword')">SHOW</button>
            </div>
            <small id="confirmPassword-error" class="text-danger"></small>
        </div>
    </div>

                            <div class="col-md-12" >
                                <div class="form-group justify-content-center d-flex py-4 "style="border-bottom:2px solid #ECECEC;">
                                    <button type="submit" id="submit-button" class="btn btn-primary btn-lg px-4 py-2 pb-2" style="font-size: 15px; border-radius: 30px; background-color: #0449A6;" disabled><b>Register</b></button>
                                </div>
                            </div>
                            <div class="col-md-12 mt-2 mb-0 text-center">
    <small>Already have an account? <a href="Admin Login.php" style="color:#0449A6;"><b>Login</b></a></small>
</div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('phone-number').addEventListener('input', function (e) {
            const input = e.target;
            const value = input.value;
            const errorElement = document.getElementById('phone-number-error');

            // Allow only digits
            if (!/^\d*$/.test(value)) {
                input.value = value.replace(/\D/g, '');
                errorElement.textContent = "Only numbers are allowed.";
            } else {
                errorElement.textContent = "";
            }

            // Check length
            if (value.length > 11) {
                input.value = value.slice(0, 11);
                errorElement.textContent = "Phone number cannot exceed 11 digits.";
            }
        });
    </script>
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
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    const submitButton = document.getElementById('submit-button');

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

      // Save form data to sessionStorage on input
      form.querySelectorAll('input').forEach(function(field) {
        field.value = sessionStorage.getItem(field.id) || ''; // Load saved value

        field.addEventListener('input', function() {
            sessionStorage.setItem(field.id, field.value); // Save value
            touchedFields[field.id] = true;
            validateField(field);
            validateForm();
        });
    });

    function validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';
        let successMessage = '';

        switch (field.id) {
            case 'first-name':
                if (value === '') {
                    isValid = false;
                    errorMessage = 'Please enter your First Name.';
                }
                break;
            case 'last-name':
                if (value === '') {
                    isValid = false;
                    errorMessage = 'Please enter your Last Name.';
                }
                break;
            case 'username':
                if (value === '') {
                    isValid = false;
                    errorMessage = 'Please enter a Username.';
                } else if (value.length < 8 || value.length > 16) {
                    isValid = false;
                    errorMessage = 'Username must be between 8 and 16 characters.';
                }
                break;
            case 'phone-number':
                if (value === '' || !validatePhoneNumber(value)) {
                    isValid = false;
                    errorMessage = 'Please enter a valid Phone Number starting with "09" and containing exactly 11 digits.';
                }
                break;
            case 'email':
                if (value === '' || !validateEmail(value)) {
                    isValid = false;
                    errorMessage = 'Please enter a valid Email.';
                }
                break;
            case 'password':
                if (value === '' || !validatePassword(value)) {
                    isValid = false;
                    errorMessage = 'Password must be 8-16 characters long and contain at least one letter, one number, and one special character (@$!%*?&).';
                }
                break;
            case 'confirmPassword':
                const password = document.getElementById('password').value.trim();
                if (value === '') {
                    isValid = false;
                    errorMessage = 'Please confirm your Password.';
                } else if (value !== password) {
                    isValid = false;
                    errorMessage = 'Passwords do not match.';
                } else {
                    successMessage = 'Passwords match!';
                }
                
                break;
        }

        if (!isValid && touchedFields[field.id]) {
            showError(field, errorMessage);
        } else if (isValid && touchedFields[field.id]) {
            removeError(field);
            if (field.id === 'confirmPassword') {
                showSuccess(field, successMessage);
            }
        }

        return isValid;
    }

    function validateForm() {
        let isFormValid = true;

        form.querySelectorAll('input').forEach(function(field) {
            if (!validateField(field)) {
                isFormValid = false;
            }
        });

        submitButton.disabled = !isFormValid;

        return isFormValid;
    }

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        for (const field in touchedFields) {
            touchedFields[field] = true;
        }

        if (validateForm()) {
            submitForm();
        }
    });

    form.querySelectorAll('input').forEach(function(field) {
        field.addEventListener('input', function() {
            touchedFields[field.id] = true;
            validateField(field);
            validateForm();
        });
    });

    // Validation functions
    function validateEmail(email) {
        const re = /^[\w-]+(\.[\w-]+)*@(?:(?:gmail|yahoo|outlook)\.com)$/;
        return re.test(email);
    }

    function validatePhoneNumber(phoneNumber) {
        const re = /^09\d{9}$/; // Matches "09" followed by 9 digits
        return re.test(phoneNumber);
    }

    function validatePassword(password) {
        const re = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,16}$/;
        return re.test(password);
    }

    function showError(input, message) {
        const errorElement = document.getElementById(input.id + '-error');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.remove('text-success');
            errorElement.classList.add('text-danger');
            input.classList.add('error-border'); // Add error-border class
        }
    }

    function removeError(input) {
        input.classList.remove('error-border');
        const errorElement = document.getElementById(input.id + '-error');
        if (errorElement) {
            errorElement.textContent = '';
        }
    }
    function showSuccess(input, message) {
        const errorElement = document.getElementById(input.id + '-error');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.remove('text-danger');
            errorElement.classList.add('text-success'); // Add success class
        }
    }

    function submitForm() {
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                sessionStorage.clear();
                window.location.href = 'Registration Success.php';
            } else if (result.status === 'error') {
                const alertMessages = document.getElementById('alertMessages');
                alertMessages.innerHTML = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                    result.message +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span>' +
                    '</button>' +
                    '</div>';
            }
        })
        .catch(error => {
            console.error('Error submitting form:', error);
        });
    }
});


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






</body>
</html>
