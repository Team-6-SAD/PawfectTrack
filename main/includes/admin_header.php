<header>
    
    <nav class="navbar navbar-expand-lg" style="background-color:#0449A6;">
 
        <div class="ml-auto profile-container" id="profileDropdown">
        <?php
// Check if adminPhoto is empty
if (!empty($adminPhoto)) {
    // Display the admin photo
    echo '<img src="uploads/'. $adminPhoto . '" alt="Admin Photo" class="profile-icon ml-2">';
} else {
    // Display the placeholder image
    echo '<img src="uploads/placeholder.png" alt="Placeholder Image" class="profile-icon ml-2">';
}
?>
            <div class="profile-text-container">
            <span class="profile-text text-left mt-2"><?php echo $firstName . ' ' . $lastName ?? "User"; ?></span>


                <span class="profile-role text-left pl-2">Admin</span>
            </div>
            <div class="dropdown-content" id="dropdownContent">
                <!-- Dropdown content goes here -->
                <div class="col-md-12 py-2 navbar-item">
                <a href="admin-profile.php" style="font-size: 12px; color:#5e6e82;" class="text-left"><img src="img/img-navbar/My Profile.png" class="navbar-pictures mr-2" > My Profile</a>
                </div>
                <div class="col-md-12 py-2 navbar-item">
                <a href="admin-settings.php" style="font-size: 12px; color:#5e6e82;"  class="text-left"><img src="img/img-navbar/settings.png" class="navbar-pictures mr-2"> Settings</a>
                </div>
                <div class="col-md-12 py-2 navbar-item" style=" border-top:1px solid #5e6e82;">
                <a href="backend/logout.php" id="logoutLink" style="font-size: 12px; color:#5e6e82;"  class="text-left"><img src="img/img-navbar/logout.png" class="navbar-pictures-sub ml-1 mr-2">Logout</a>
                </div>
            </div>
        </div>
    </nav>
</header>

