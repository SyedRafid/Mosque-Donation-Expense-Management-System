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

        <title>Extended Support</title>
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
    </head>

    <body>
        <?php include('includes/header.php'); ?>
        <div class="ts-main-content">
            <?php include('includes/leftbar.php'); ?>
            <div class="content-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="page-title">Extended Support</h2>
                            <div class="panel panel-default">
                                <div class="panel-heading">Add Information</div>
                                <div class="panel-body">

                                    <form id="addExSupport" method="post" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="fullName">Name/Institution/Occurrence</label>
                                            <input type="text" name="name" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="phone">Phone (optional)</label>
                                            <input type="tel" name="phone" class="form-control">
                                        </div>

                                        <div class="form-group">
                                            <label for="amount">Amount</label>
                                            <input type="number" name="amount" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="note">Note (optional)</label>
                                            <textarea name="note" class="form-control"></textarea>
                                        </div>

                                        <button type="submit" class="btn" style="background-color: #2b7f19; color: white;">Submit</button>
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
        <script>
            $('#addExSupport').on('submit', function(e) {
                e.preventDefault(); // Prevent form submission
                var formData = $(this).serialize();
                var phone = $('input[name="phone"]').val().trim();
                var note = $('textarea[name="note"]').val().trim();

                // Check if Phone or Note is empty
                var missingFields = [];
                if (phone === '') missingFields.push('Phone');
                if (note === '') missingFields.push('Note');

                if (missingFields.length > 0) {
                    // Determine singular/plural form for "field" and "is/are"
                    var fieldText = missingFields.length > 1 ? "fields" : "field";
                    var isAreText = missingFields.length > 1 ? "are" : "is";

                    // Display SweetAlert2 warning
                    Swal.fire({
                        title: 'Warning',
                        text: missingFields.join(' and ') + " " + fieldText + " " + isAreText + " empty. Do you want to continue?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, submit it!',
                        cancelButtonText: 'No, go back'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // If confirmed, submit the form
                            submitForm(formData);
                        }
                    });
                } else {
                    // Submit the form if no fields are empty
                    submitForm(formData);
                }
            });

            function submitForm(formData) {
                $.ajax({
                    url: 'extendedSupport_pro.php', // Changed URL to correct file
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.trim() === "success") {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Extended support entry successfully added.',
                                icon: 'success',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'extended_support.php';
                                }
                            });
                        } else if (response.trim() === "error") {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to add extended support entry. Please try again.',
                                icon: 'error',
                            });
                        } else if (response.trim() === "warning") {
                            Swal.fire({
                                title: 'Warning',
                                text: 'Required fields missing. Please complete all mandatory fields.',
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
            }
        </script>
    </body>
    </html>
<?php } ?>