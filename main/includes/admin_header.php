<header>
    <nav class="navbar navbar-expand-lg" style="background-color:#0449A6;">
        <div class="ml-auto d-flex align-items-center">
            <span id="group" class="mr-3 position-relative">
            <button type="button" class="btn button-notif" style="background-color:white; border-radius: 50% !important; height: 40px; width: 40px;">
    <div class="d-flex justify-content-center align-items-center">
        <img src="img/img-dashboard/notif-bell-blue.png" style="height: 25px; width: auto;">
    </div>
</button>

                <span class="badge badge-danger badge-notif p-1" style="height:17px; width:17px; font-size:8px; border:none;"></span>
                <div class="dropdown-content2 notifications-dropdown mt-3" id="notificationsDropdown">
                    <div class="notifications-dropdown-content">
                        <h5 class="notification-title mt-3 ml-3 font-weight-bold" style="color:#5e6082;">Notifications</h5>
                        <div class="notification-filters ml-3 mt-2">
                            <button id="allFilter" class="btn btn-link badge-pill p-0 px-2" style="color:#5e6082; border-radius:27.5px !important;">All</button>
                            <button id="unreadFilter" class="btn btn-link p-0 px-2" style="color:#5e6082; border-radius:27.5px !important;">Unread</button>
                        </div>
                        <div id="notificationContainer" class="pt-4 p-2">
                            <!-- Dynamic notifications will be inserted here -->
                        </div>
                    </div>
                </div>
            </span>
            <div class="d-flex align-items-center profile-container" id="profileDropdown">
                <?php
                if (!empty($adminPhoto)) {
                    echo '<img src="uploads/'. $adminPhoto . '" alt="Admin Photo" class="profile-icon ml-2">';
                } else {
                    echo '<img src="uploads/placeholder.png" alt="Placeholder Image" class="profile-icon ml-2">';
                }
                ?>
                <div class="profile-text-container ml-2">
                    <span class="profile-text text-left m-0"><?php echo $firstName . ' ' . $lastName ?? "User"; ?></span>
                    <span class="profile-role text-left">Admin</span>
                </div>
                <div class="dropdown-content ml-2" id="dropdownContent">
                    <div class="col-md-12 py-2 navbar-item">
                        <a href="admin-profile.php" style="font-size: 12px; color:#5e6e82;" class="text-left">
                            <img src="img/img-navbar/My Profile.png" class="navbar-pictures mr-2"> My Profile
                        </a>
                    </div>
                    <div class="col-md-12 py-2 navbar-item">
                        <a href="admin-settings.php" style="font-size: 12px; color:#5e6e82;" class="text-left">
                            <img src="img/img-navbar/settings.png" class="navbar-pictures mr-2"> Settings
                        </a>
                    </div>
                    <div class="col-md-12 py-2 navbar-item" style="border-top:1px solid #5e6e82;">
                        <a href="backend/logout.php" id="logoutLink" style="font-size: 12px; color:#5e6e82;" class="text-left">
                            <img src="img/img-navbar/logout.png" class="navbar-pictures-sub ml-1 mr-2">Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>

<style>
    .notifications-dropdown {
        width: 350px;
        height: 500px;
        overflow: hidden;
        padding: 10px;
        box-sizing: border-box;
    }

    .notifications-dropdown-content {
        height: 100%;
        overflow-y: auto;
    }

    .profile-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #ffffff;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
        right: 0;
    }

    #profileDropdown:hover .dropdown-content {
        display: block;
    }

    .navbar-item {
        border-bottom: 1px solid #ddd;
    }

    .badge-notif {
        position: relative;
        top: -15px;
        left: -15px;
        border: 1px solid black;
        border-radius: 50%;
    }

    .notification-header {
        position: sticky;
        top: 0;
        background-color: #0449A6;
        z-index: 1;
        width: 100%;
    }

    .notification-item {
        position: relative;
    }

    .notification-item.unread::after {
        content: "";
        position: absolute;
        top: 50%;
        right: 10px;
        width: 10px;
        height: 10px;
        background-color: #0449A6;
        border-radius: 50%;
    }

    .notification-filters button {
        margin-right: 10px;
        text-decoration: none;
        cursor: pointer;
    }
    .active-filter{
        background-color:#dce8f9;
    }
    /* CSS for hover effect */
.notification-item:hover {
    background-color: #f0f0f0; /* Change to desired hover background color */
    transition: background-color 0.3s ease; /* Optional: Smooth transition */
}

/* CSS for unread indicator hover effect */
.unread-indicator:hover {
    opacity: 0.7; /* Adjust opacity on hover */
    transition: opacity 0.3s ease; /* Optional: Smooth transition */
}

</style>
