<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Visibility Toggle</title>
</head>
<body>
<?php
// Define PHP variables
$initialValue = ''; // Store the initial value of the input field
$isPasswordVisible = false; // Store the state of password visibility

// Check if the form is submitted
if(isset($_POST['toggleButton'])) {
    // Toggle the state of password visibility
    $isPasswordVisible = !$_POST['isPasswordVisible'];
    // Update the initial value if the state is changed
    if($isPasswordVisible) {
        $initialValue = $_POST['passwordField'];
    }
}
?>

<form method="post">
    <input type="text" name="passwordField" value="<?php echo $initialValue; ?>" />
    <button type="submit" name="toggleButton"><?php echo $isPasswordVisible ? 'Hide' : 'Show'; ?></button>
    <input type="hidden" name="isPasswordVisible" value="<?php echo $isPasswordVisible ? '1' : '0'; ?>" />
</form>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Get the toggle button element
    var toggleButton = document.querySelector('button[name="toggleButton"]');
    // Get the password field element
    var passwordField = document.querySelector('input[name="passwordField"]');
    // Get the input field's initial value
    var initialValue = "<?php echo $initialValue; ?>";
    // Store the state of password visibility
    var isPasswordVisible = <?php echo $isPasswordVisible ? 'true' : 'false'; ?>;

    // Function to toggle password visibility
    function togglePasswordVisibility() {
        if (isPasswordVisible) {
            // If password is currently visible, hide it
            passwordField.value = passwordField.value.replace(/./g, '*');
            toggleButton.textContent = "Show";
        } else {
            // If password is currently hidden, show it
            passwordField.value = initialValue;
            toggleButton.textContent = "Hide";
        }
        isPasswordVisible = !isPasswordVisible;
    }

    // Attach click event listener to the toggle button
    toggleButton.addEventListener("click", function(event) {
        event.preventDefault();
        togglePasswordVisibility();
    });

    // Call togglePasswordVisibility function initially to set the correct state
    togglePasswordVisibility();
});
</script>

</body>
</html>
