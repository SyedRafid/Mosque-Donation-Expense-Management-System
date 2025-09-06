<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
} else {
?>
    <!doctype html>
    <html lang="en" class="no-js">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="theme-color" content="#3e454c">

        <title>Add Application</title>
        <link rel="icon" href="images/logo.jpg" type="image/png">
        <!-- Font awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <!-- Sandstone Bootstrap CSS -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <!-- Bootstrap Datatables -->
        <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
        <!-- Bootstrap select -->
        <link rel="stylesheet" href="css/bootstrap-select.css">
        <!-- Admin Style -->
        <link rel="stylesheet" href="css/style.css">
        <!-- Include Flatpickr CSS and JS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <!-- Include Month Select Plugin -->
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">

    </head>

    <body>
        <?php include('includes/header.php'); ?>
        <div class="ts-main-content">
            <?php include('includes/leftbar.php'); ?>
            <div class="content-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="page-title">Add Profile</h2>
                            <div class="panel panel-default">
                                <div class="panel-heading">Add Information</div>
                                <div class="panel-body">

                                    <form id="addProfileForm" method="post" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="fullName">Name</label>
                                            <input type="text" name="name" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="nid">NID</label>
                                            <input type="number" name="nid" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="fName">Father's Name</label>
                                            <input type="text" name="fName" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="phone">Phone</label>
                                            <input type="tel" name="phone" id="phone" onBlur="userAvailability()" class="form-control" required>
                                            <span id="user-availability-status1" style="font-size:12px; color: gray;"></span>
                                        </div>

                                        <div class="form-group">
                                            <label for="salary">Monthly Contribution (৳)</label>
                                            <input type="number" name="salary" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="monthYear">Beginning of Contribution</label>
                                            <input id="monthYear" name="monthYear" class="form-control">
                                        </div>

                                        <button type="submit" id="submit" class="btn" style="background-color: #2b7f19; color: white;">Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Scripts -->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap-select.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/jquery.dataTables.min.js"></script>
        <script src="js/dataTables.bootstrap.min.js"></script>
        <script src="js/main.js"></script>
        <script src="assets/js/script.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
        <script src="vendor/modernizr/modernizr.js"></script>
        <script src="vendor/jquery-cookie/jquery.cookie.js"></script>
        <script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
        <script src="vendor/switchery/switchery.min.js"></script>
        <script src="vendor/jquery-validation/jquery.validate.min.js"></script>
        <script src="assets/js/main.js"></script>
        <script src="assets/js/login.js"></script>

        <script>
            function userAvailability() {
                $("#loaderIcon").show();
                $.ajax({
                    url: "check_availability.php",
                    data: 'phone=' + $("#phone").val(),
                    type: "POST",
                    success: function(data) {
                        $("#user-availability-status1").html(data);
                        $("#loaderIcon").hide();
                    },
                    error: function() {}
                });
            }
        </script>

        <script>
            $('#addProfileForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    url: 'addprofile_pro.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.trim() === "success") {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Profile information has been added successfully.',
                                icon: 'success',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'manage_profile.php';
                                }
                            });
                        } else if (response.trim() === "error") {
                            Swal.fire({
                                title: 'Error!',
                                text: 'There was an issue submitting your query. Please try again.',
                                icon: 'error',
                            });
                        } else if (response.trim() === "warning") {
                            Swal.fire({
                                title: 'Warning',
                                text: 'Please fill all required fields correctly.',
                                icon: 'warning',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("XHR Error:", xhr);
                        console.error("Status:", status);
                        console.error("Error:", error);
                        Swal.fire({
                            title: 'Unexpected Response',
                            text: 'An unexpected error occurred. Please contact support.',
                            icon: 'error',
                        });
                    }
                });
            });
        </script>

        <script>
            flatpickr("#monthYear", {
                plugins: [new monthSelectPlugin({
                    shorthand: true, // Display shorthand month (e.g., Jan, Feb)
                    dateFormat: "F Y", // Format display as 'Month Year'
                    altFormat: "F Y", // Alternative display format
                })],
                defaultDate: new Date(),
                disableMobile: true
            });
        </script>
    </body>

    </html>
<?php } ?>