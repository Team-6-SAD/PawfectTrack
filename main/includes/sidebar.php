<style>
/* Active sidebar item */
.sidebar-item.active {
    background-color: #5E6E82; /* Change background color of active sidebar item */
}

/* Hover effect */

.sidebar-item.active .sidebar-text {
    color: #FFFFFF; /* Change font color of sidebar text on hover/active */
}

.sidebar-item:hover .sidebar-text{
    color: #5e6e82; 
}
/* Disable active sidebar item link */
.sidebar-link.active {
    pointer-events: none; /* Disable pointer events on the link */
    cursor: default; /* Change cursor to default */
    opacity: 1; /* Reduce opacity to visually indicate that it's disabled */
    font-weight: bold;
}
</style>

<nav id="sidebar" class="sidebar">
    <div class="container p-0 m-0 mt-2">
        <button class="hamburger hamburger--arrowturn-r mr-4 pl-3" type="button" id="sidebarCollapse1">
            <span class="hamburger-box">
                <span class="hamburger-inner"></span>
            </span>
        </button>
        <img src="img/img-navbar/System Logo.png" class="img-small mr-5 mt-2" alt="Description of the image">
    </div>

    <div class="sidebar-content">
        <div class="sidebar-heading-container">
            <p class="sidebar-heading">MAIN</p>
            <div class="sidebar-heading-line"></div>
        </div>
        
        <?php
        $activePage = basename($_SERVER['PHP_SELF']);
        ?>

        <a href="admindashboard.php" class="sidebar-link <?php if ($activePage === 'admindashboard.php') echo 'active'; ?>">
            <div class="sidebar-item <?php if ($activePage === 'admindashboard.php') echo 'active'; ?>" data-default-icon="img/img-navbar/Dashboard.png" data-hover-icon="img/img-navbar/Dashboard.png">
                <img src="<?php echo ($activePage === 'admindashboard.php') ? 'img/img-navbar/Dashboard-hover.png' : 'img/img-navbar/Dashboard.png'; ?>" alt="Dashboard Icon" class="sidebar-icon ml-1">
                <span class="sidebar-text">Dashboard</span>
            </div>
        </a>

        <a href="patient-list.php" class="sidebar-link <?php if ($activePage === 'patient-list.php') echo 'active'; ?>">
            <div class="sidebar-item <?php if ($activePage === 'patient-list.php') echo 'active'; ?>" data-default-icon="img/img-navbar/Patient List.png" data-hover-icon="img/img-navbar/Patient List.png">
                <img src="<?php echo ($activePage === 'patient-list.php') ? 'img/img-navbar/Patient List-hover.png' : 'img/img-navbar/Patient List.png'; ?>" alt="Floor Viewer Icon" class="sidebar-icon ml-1">
                <span class="sidebar-text">Patient List</span>
            </div>
        </a>

        <a href="Inventory.php" class="sidebar-link <?php if ($activePage === 'Inventory.php') echo 'active'; ?>">
            <div class="sidebar-item <?php if ($activePage === 'Inventory.php') echo 'active'; ?>" data-default-icon="img/img-navbar/Inventory.png" data-hover-icon="img/img-navbar/Inventory.png">
                <img src="<?php echo ($activePage === 'Inventory.php') ? 'img/img-navbar/Inventory-hover.png' : 'img/img-navbar/Inventory.png'; ?>" alt="Floor Viewer Icon" class="sidebar-icon ml-1">
                <span class="sidebar-text">Inventory</span>
            </div>
        </a>

        <a href="Reports and Analytics.php" class="sidebar-link <?php if ($activePage === 'Reports and Analytics.php') echo 'active'; ?>">
            <div class="sidebar-item <?php if ($activePage === 'Reports and Analytics.php') echo 'active'; ?>" data-default-icon="img/img-navbar/Reports and Analytics.png" data-hover-icon="img/img-navbar/Reports and Analytics.png">
                <img src="<?php echo ($activePage === 'Reports and Analytics.php') ? 'img/img-navbar/Reports and Analytics-hover.png' : 'img/img-navbar/Reports and Analytics.png'; ?>" alt="Floor Viewer Icon" class="sidebar-icon ml-1">
                <span class="sidebar-text">Reports and Analytics</span>
            </div>
        </a>

        <a href="Archival.php" class="sidebar-link <?php if ($activePage === 'Archival.php') echo 'active'; ?>">
            <div class="sidebar-item <?php if ($activePage === 'Archival.php') echo 'active'; ?>" data-default-icon="img/img-navbar/Archival.png" data-hover-icon="img/img-navbar/Archival.png">
                <img src="<?php echo ($activePage === 'Archival.php') ? 'img/img-navbar/Archival-hover.png' : 'img/img-navbar/Archival.png'; ?>" alt="Floor Viewer Icon" class="sidebar-icon ml-1">
                <span class="sidebar-text">Archival</span>
            </div>
        </a>
    </div>
</nav>
