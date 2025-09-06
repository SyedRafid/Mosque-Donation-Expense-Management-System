<nav class="ts-sidebar">
    <ul class="ts-sidebar-menu">
        <li class="ts-label">Main</li>
        <li class="has-submenu">
            <a href="dashboard.php"><i class="fa-solid fa-house-chimney fa-2x"></i> &nbsp;<span class="menu-text">Dashboard</span></a>
        </li>
        <li class="has-submenu">
            <a href="javascript:void(0);" class="menu-toggle"><i class="fa fa-users fa-2x"></i> <span class="menu-text">Profile</span></a>
            <ul class="submenu">
                <li><a href="add_profile.php"><i class="fa fa-user fa-lg"></i>&nbsp;&nbsp;&nbsp;<span class="sub-menu-text">Add Profile</span></a></li>
                <li><a href="manage_profile.php"><i class="fa-solid fa-user-pen fa-lg"></i> <span class="sub-menu-text">Manage Profile</span></a></li>
            </ul>
        </li>
        <li class="has-submenu">
            <a href="javascript:void(0);" class="menu-toggle"><i class="fas fa-hand-holding-dollar fa-2x"></i> <span class="menu-text">Contributions</span></a>
            <ul class="submenu">
                <li><a href="monthly_support.php"><i class="fa-regular fa-calendar-check fa-lg"></i>&nbsp;&nbsp;&nbsp;<span class="sub-menu-text">Monthly Support</span></a></li>
                <li><a href="extended_support.php"><i class="fa-solid fa-hand-holding-heart fa-lg"></i> <span class="sub-menu-text">Extended Support</span></a></li>
            </ul>
        </li>
        <li class="has-submenu">
            <a href="exp_log.php"><i class="fa-solid fa-cash-register fa-2x"></i>&nbsp;&nbsp;<span class="menu-text">Expense Log</span></a>
        </li>
        <li class="has-submenu">
            <a href="javascript:void(0);" class="menu-toggle"><i class="fa-solid fa-file-lines fa-2x"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span class="menu-text">Report</span></a>
            <ul class="submenu">
                <li><a href="contribution_report.php"><i class="fa-solid fa-file-invoice fa-lg"></i> <span class="sub-menu-text">Contribution Report</span></a></li>
                <li><a href="expense_report.php"><i class="fa-solid fa-file-invoice-dollar fa-lg"></i> <span class="sub-menu-text">Expense Report</span></a></li>
            </ul>
        </li>
    </ul>
</nav>

<style>
    /* Sidebar menu styling */
    .ts-sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .ts-sidebar-menu ul {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
        /* Smooth transition effect */
    }

    .ts-sidebar-menu li {
        position: relative;
        /* Keep it for submenu positioning */
    }

    .ts-sidebar-menu a {
        display: block;
        padding: 15px;
        color: #fff;
        text-decoration: none;
    }

    .ts-sidebar-menu .submenu {
        display: none;
        /* Hide the submenu by default */
        list-style: none;
        padding: 0;
        margin: 0;
        background-color: #1f4443;
        /* Adjust background for submenu */
    }

    .menu-text {
        font-size: 18px;
        vertical-align: middle;
    }

    .sub-menu-text {
        font-size: 15px;
        vertical-align: middle;
    }
</style>

<script>
    document.querySelectorAll('.menu-toggle').forEach(item => {
        item.addEventListener('click', function() {
            const submenu = this.nextElementSibling;
            if (submenu.style.display === 'block') {
                submenu.style.display = 'none'; // Hide the submenu
            } else {
                submenu.style.display = 'block'; // Show the submenu
            }
        });
    });
</script>