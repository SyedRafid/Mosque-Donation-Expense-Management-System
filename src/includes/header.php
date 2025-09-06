<div class="brand clearfix">
    <a href="add_profile.php" class="brand-text" style="font-size: 20px; color:#fff;">
        <img src="images/logo.ico" style="height: 55px; margin-right: 10px; vertical-align: middle;">
        প্রস্তমপুর জামে মসজিদ
    </a>
    <span class="menu-btn"><i class="fa fa-bars"></i></span>
    <ul class="ts-profile-nav">
        <li class="ts-account dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <img src="img/ts-avatar.jpg" class="ts-avatar hidden-side" alt="">
                <span>Account</span>
                <i class="fa fa-angle-down hidden-side"></i>
            </a>
            <ul class="dropdown-menu">
                <li><a href="change-password.php">Change Password</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </li>
    </ul>
</div>

<style>
    .brand {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 5px 20px;
        background-color: #2b7f19;
    }

    .brand-text {
        margin-left: 0;
    }

    .menu-btn {
        display: none; /* Default hidden */
        color: #fff;
        font-size: 24px;
        cursor: pointer;
        height: 65px;
        line-height: normal; /* Fixed height */
    }

    .ts-profile-nav {
        display: flex;
        align-items: center; /* Vertically aligns the Account button as well */
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .ts-profile-nav li {
        margin-left: 20px;
    }

    .ts-profile-nav a {
        color: #fff;
        text-decoration: none;
        padding: 10px 20px;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        background-color: #fff;
        right: 0;
        min-width: 150px;
        padding: 10px 0;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .ts-profile-nav li:hover .dropdown-menu {
        display: block; /* Keep hover effect for desktop */
    }

    .ts-profile-nav.active {
        margin-top: 26px; /* Space between the menu and dropdown */
    }

    @media (max-width: 1024px) {
        .menu-btn {
            display: block; /* Show menu button on tablets */
        }

        .ts-profile-nav {
            display: none; /* Hide the nav initially */
            flex-direction: column;
            position: absolute;
            right: 0;
            top: 60px;
            background-color: #333; /* Background color for the dropdown */
            width: 100%;
        }

        .ts-profile-nav.active {
            display: flex; /* Show nav when active */
        }

        .ts-profile-nav li {
            width: 100%; /* Full width for list items */
            text-align: center; /* Center align items */
        }
    }
</style>

<script src="js/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // Toggle the main menu on mobile
        $('.menu-btn').click(function() {
            $('.ts-profile-nav').toggleClass('active');
        });

        // Handle dropdown toggle for mobile
        $('.dropdown-toggle').click(function(event) {
            event.preventDefault(); // Prevent default link behavior
            const dropdownMenu = $(this).next('.dropdown-menu');

            // Close other dropdowns if needed
            $('.dropdown-menu').not(dropdownMenu).hide(); // Hide other dropdowns

            // Toggle display with immediate effect
            dropdownMenu.toggle(); // Toggle visibility (show/hide) with one click
        });

        // Close dropdown if clicking outside
        $(document).click(function(event) {
            if (!$(event.target).closest('.dropdown-toggle').length) {
                $('.dropdown-menu').hide(); // Hide dropdown if clicking outside
            }
        });
    });
</script>
