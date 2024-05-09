<header>
    
    <nav class="navbar navbar-expand-lg" style="background-color:#0449A6;">
 
        <div class="ml-auto profile-container" id="profileDropdown">
            <img src="Group 250.png" class="profile-icon" alt="Description of the image">
            <div class="profile-text-container">
            <span class="profile-text"><?php echo $firstName . ' ' . $lastName ?? "User"; ?></span>


                <span class="profile-role">Admin</span>
            </div>
            <div class="dropdown-content" id="dropdownContent">
                <!-- Dropdown content goes here -->
                <a href="admin-profile.php">Profile</a>
                <a href="admin-settings.php">Settings</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
</header>
