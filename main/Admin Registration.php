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
    <label for="suffix">Suffix</label>
    <select id="suffix" name="suffix" class="form-control">
        <option value="" disabled selected>Select a suffix</option>
        <option value="Jr.">Jr.</option>
        <option value="Sr.">Sr.</option>
        <option value="II">II</option>
        <option value="III">III</option>
        <option value="IV">IV</option>
        <option value="Other">Other</option>
    </select>
    
    <input type="text" id="custom-suffix" name="custom-suffix" class="form-control mt-2" placeholder="Enter suffix" style="display: none;" maxlength="7" oninput="preventLeadingSpace(event)">
    <small id="suffix-error" class="text-danger"></small>
</div>

                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="username">Username<span class="red">*</span></label>
                                    <input type="text" id="username" name="username" class="form-control" placeholder="Username" oninput="preventSpaces(event)" maxlength="16">
                                    <small id="username-error" class="text-danger"></small>
                                </div>
                            </div>
                            <div class="col-md-12">
        <div class="form-group">
            <label for="phone-number">Phone Number<span class="red">*</span></label>
            <input type="tel" id="phone-number" name="phone-number" class="form-control" placeholder="09123456789" maxlength="11" oninput="formatPhoneNumber(event)">

            <small id="phone-number-error" class="text-danger"></small>
        </div>
    </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="email">Email<span class="red">*</span></label>
                                    <input type="text" id="email" name="email" class="form-control" placeholder="@gmail.com" maxlength="320" oninput="preventSpaces(event)">
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
                                    <div style="font-size: 12px; line-height: 10px;">
                                    <span id="password-message" class="text-secondary" >
    Password must be 8-16 characters long and contain at least one letter, one number, and one special character (@$!%*?&).
</span>
</div>

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
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script>
    $(document).ready(function() {
    $('#suffix').change(function() {
        if ($(this).val() === 'Other') {
            $('#custom-suffix').show();
        } else {
            $('#custom-suffix').hide();
        }
    });

    // Function to prevent leading space
    function preventLeadingSpace(event) {
        const input = event.target;
        if (input.value.startsWith(' ')) {
            input.value = input.value.trimStart();
        }
    }

    // Add event listener for custom suffix input
    $('#custom-suffix').on('input', function(event) {
        preventLeadingSpace(event);
    });
});

</script>
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
        const input = $('#' + id);
        const toggleBtn = input.next();
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            toggleBtn.text('HIDE');
            toggleBtn.css('padding-left', '17px'); // Add padding-left
        } else {
            input.attr('type', 'password');
            toggleBtn.text('SHOW');
            toggleBtn.css('padding-left', '10px'); // Add padding-left
        }
    }

    function preventLeadingSpace(event) {
        const input = $(event.target);
        if (input.val().startsWith(' ')) {
            input.val(input.val().trim()); // Remove leading space
        }
        // Replace multiple consecutive spaces with a single space
        input.val(input.val().replace(/\s{2,}/g, ' '));
    }

    function preventSpaces(event) {
        const input = $(event.target);
        if (input.val().includes(' ')) {
            input.val(input.val().replace(/\s/g, '')); // Remove all spaces
        }
    }

    function formatPhoneNumber(event) {
        const input = event.target;
        let value = input.value.trim(); // Trim any leading or trailing spaces
        const prefix = "09";

        // Ensure the value starts with the prefix
        if (!value.startsWith(prefix)) {
            value = prefix + value.slice(prefix.length); // Add prefix if not present
        }

        // Limit to maxlength
        if (value.length > input.maxLength) {
            value = value.slice(0, input.maxLength);
        }

        // Update the input value
        input.value = value;
    }

    </script>
<script>
$(document).ready(function() {
    const validDomains = []; // Array to store valid domains

    // Fetch valid domains from CSV file
    fetch('free-domains-2.csv')
        .then(response => response.text())
        .then(csvData => {
            validDomains.push(...csvData.split('\n').map(domain => domain.trim()));
            console.log('Valid domains:', validDomains); // Debug log to check valid domains
        })
        .catch(error => console.error('Error fetching CSV file:', error));

    // Function to validate email format and domain
    function validateEmail(email) {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const emailDomain = email.split('@')[1];

        if (!emailPattern.test(email)) {
            return 'Invalid email format.';
        } else if (!validDomains.includes(emailDomain)) {
            return 'Please enter an email with a known domain.';
        } else {
            return ''; // Email is valid
        }
    }

    // Function to validate password format
    function validatePassword(password) {
        const re = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,16}$/;
        return re.test(password);
    }
    function validateconfirmPassword(confirmPassword) {
        const re = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,16}$/;
        return re.test(confirmPassword);
    }

    // Function to validate phone number format
    function validatePhoneNumber(phoneNumber) {
        const re = /^09\d{9}$/; // Matches "09" followed by 9 digits
        return re.test(phoneNumber);
    }

    // Function to validate name format
    function validateName(name) {
        const namePattern = /^[A-Za-z]+(?:[ '-][A-Za-z]+)*$/; // Allows letters, spaces, hyphens, and apostrophes
        const repeatingCharsPattern = /(.)\1{2,}/; // Matches any character repeated 3 or more times

        if (!namePattern.test(name)) {
            return 'Names should only contain letters, spaces, hyphens, and apostrophes.';
        } else if (repeatingCharsPattern.test(name)) {
            return 'Names should not contain repeating characters.';
        } else if (/[A-Z]{2,}/.test(name) || /^[a-z]/.test(name) && /[A-Z]/.test(name.substring(1))) {
            return 'Names should not have unusual capitalization.';
        } else {
            return '';
        }
    }

    // Function to show error message for a field
    function showError(input, message) {
        const errorElement = $('#' + input.attr('id') + '-error');
        errorElement.text(message);
        errorElement.removeClass('text-success').addClass('text-danger');
        input.addClass('error-border');
    }

    // Function to remove error message for a field
    function removeError(input) {
        input.removeClass('error-border');
        const errorElement = $('#' + input.attr('id') + '-error');
        errorElement.text('');
    }

    // Function to check individual field validity
    function checkFieldValidity(fieldId, value) {
        let errorMessage = '';

        switch (fieldId) {
            case 'username':
                if (value && value.length < 8 || value.length > 16) {
                    errorMessage = 'Username must be between 8 and 16 characters.';
                }
                break;
            case 'phone-number':
                if (!validatePhoneNumber(value)) {
                    errorMessage = 'Please enter a valid Phone Number starting with "09" and containing exactly 11 digits.';
                }
                break;
            case 'email':
                errorMessage = validateEmail(value); // Validate email format and domain
                break;
            case 'password':
                if (!validatePassword(value)) {
                    errorMessage = 'Please enter a valid password with at least one letter, one number, and one special character (@$!%*?&).';
                }
                break;
            case 'confirmPassword':
                const password = $('#password').val().trim();
                if (!validateconfirmPassword(value)) {
                    errorMessage = 'Password must be a combination of text, numbers, and symbols (@$!%*?&).';
                } else if (value && value !== password) {
                    errorMessage = 'Passwords do not match.';
                }
                break;
            case 'first-name':
            case 'last-name':
                errorMessage = validateName(value);
                break;
        }

        return errorMessage;
    }

    // Function to check overall form validity
    function checkFormValidity() {
        let isFormValid = true;

        $('input').each(function() {
            const field = $(this);
            const value = field.val().trim();
            const errorMessage = checkFieldValidity(field.attr('id'), value);

            if (errorMessage) {
                isFormValid = false; // Mark form as invalid if any field has an error
            }
        });

        // Enable or disable the submit button based on form validity
        $('#submit-button').prop('disabled', !isFormValid);
    }

    // Event listener for input fields to enable/disable submit button
    $('input').on('input', function() {
        checkFormValidity();
    });

    // Event listener for showing error messages
    $('input').on('input', function() {
        $(this).toggleClass('touched');
    });

    $('input').on('input', function() {
        const field = $(this);
        const value = field.val().trim();
        const errorMessage = checkFieldValidity(field.attr('id'), value);

        if (errorMessage) {
            showError(field, errorMessage);
        } else {
            removeError(field);
        }
    });

    // Validate the entire form on submit
    $('#registrationForm').on('submit', function(event) {
        event.preventDefault();

        let isFormValid = true;
        $('input').each(function() {
            const field = $(this);
            const value = field.val().trim();
            const errorMessage = checkFieldValidity(field.attr('id'), value);

            if (errorMessage) {
                showError(field, errorMessage);
                isFormValid = false; // Mark form as invalid if any field has an error
            } else {
                removeError(field);
            }
        });

        console.log('Form validity:', isFormValid); // Debug log

        // If form is valid, submit it via AJAX
        if (isFormValid) {
            submitForm();
        }
    });

    // Function to submit the form via AJAX
    function submitForm() {
        console.log('Submitting form...'); // Debug log
        const formData = new FormData($('#registrationForm')[0]);

        $.ajax({
            url: $('#registrationForm').attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(result) {
                console.log('Form submitted successfully:', result); // Debug log

                // Parse result as JSON
                try {
                    result = JSON.parse(result);
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    return;
                }

                // Continue with handling the parsed result
                if (result.status === 'success') {
                    console.log('Redirecting to Registration Success.php');
                    sessionStorage.clear();
                    window.location.href = 'Registration Success.php';
                } else if (result.status === 'error') {
                    console.log('Server returned an error:', result.message); // Log the error message
                    $('#alertMessages').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        result.message +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '</div>');
                } else {
                    console.log('Unexpected server response:', result); // Log unexpected response
                }
            },
            error: function(error) {
                console.error('Error submitting form:', error);
            }
        });
    }
});

</script>






</body>
</html>
