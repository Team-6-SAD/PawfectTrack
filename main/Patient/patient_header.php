<?php
// Get the current file name
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-light" style="background-color:#1DD1A1;">
    <div class="container pl-0 ml-3">
        <!-- Brand -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="#" style="max-width: 200px;">
            <img src="System Logo Patient.png" width="120px" height="44px;" alt="System Logo">
        </a>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Navigation Links -->
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'patientdashboard.php' ? 'active' : ''; ?>" href="patientdashboard.php" style="color: white;">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'patientvaccination.php' ? 'active' : ''; ?>" href="patientvaccination.php" style="color: white;">Vaccination</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'patient-appointments.php' ? 'active' : ''; ?>" href="patient-appointments.php" style="color: white; white-space: nowrap;">Schedule Dates</a>
                </li>
            </ul>
        </div>

        <!-- Profile Dropdown -->
        <div class="ml-auto">
            <div class="profile-container" style="background-color:#1BB58D;" id="profileDropdown">
                <?php
                // Check if profile picture is available
                if (!empty($profilePicture)) {
                    echo '<img src="../uploads/'. $profilePicture . '" alt="Profile Icon" class="profile-icon ml-2">';
                } else {
                    echo '<img src="../uploads/placeholder.png" alt="Placeholder Image" class="profile-icon ml-2">';
                }
                ?>
                <div class="profile-text-container">
                    <span class="profile-text text-left pl-0"><?php echo $firstName . ' ' . $lastName; ?></span>
                    <span class="profile-role text-left pl-2" style="color: white;">Patient</span>
                </div>
                <div class="dropdown-content" id="dropdownContent">
                    <!-- Dropdown content -->
                    <div class="col-md-12 py-2 navbar-item">
                        <a href="patient-profile.php" style="font-size: 12px; color:#5e6e82;" class="text-left">
                            <img src="../img/img-navbar/My Profile.png" class="navbar-pictures mr-2"> My Profile
                        </a>
                    </div>
                    <div class="col-md-12 py-2 navbar-item">
                        <a href="patient-settings.php" style="font-size: 12px; color:#5e6e82;" class="text-left">
                            <img src="../img/img-navbar/settings.png" class="navbar-pictures mr-2"> Settings
                        </a>
                    </div>
                    <div class="col-md-12 py-2 navbar-item" style="border-top:1px solid #5e6e82;">
                        <a href="patient-backend/patient-logout.php" style="font-size: 12px; color:#5e6e82;" class="text-left">
                            <img src="../img/img-navbar/logout.png" class="navbar-pictures-sub ml-1 mr-2"> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
.navbar-brand img {
    max-width: 200px;
}

@media (max-width: 991px) {
    .navbar-collapse {
        background-color: rgba(0, 0, 0, 0.5) !important;
        z-index: 1000;
        top: 60px;
        width: 100%;
        left: 0% !important;
    }
}

.navbar-brand {
    position: absolute;
    left: 10%; /* Center horizontally */
    transform: translateX(-50%); /* Adjust to the center */
}

@media (max-width: 992px) {
    .navbar-brand {
        left: 20%; /* Center horizontally */
        transform: translateX(-50%); /* Adjust to the center */
    }
}

@media (max-width: 768px) {
    .navbar-brand {
        left: 25% !important; /* Center horizontally */
        transform: translateX(-50%); /* Adjust to the center */
    }
}

@media (max-width: 650px) {
    .navbar-brand {
        left: 30% !important; /* Center horizontally */
        transform: translateX(-50%); /* Adjust to the center */
    }
}

@media (max-width: 520px) {
    .navbar-brand {
        left: 35% !important; /* Center horizontally */
        transform: translateX(-50%); /* Adjust to the center */
    }
}

@media (max-width: 420px) {
    .navbar-brand {
        left: 40% !important; /* Center horizontally */
        transform: translateX(-50%); /* Adjust to the center */
    }
}

.navbar-collapse {
    position: absolute;
    z-index: 1000;
    left: 20%;
}

.profile-icon {
    height: 40px;
    width: 40px;
    border-radius: 50%;
}

/* Highlight active link */
.nav-link.active {
    color: #0449A6 !important;
    text-decoration: underline;
    font-weight: bold;
}
</style>
