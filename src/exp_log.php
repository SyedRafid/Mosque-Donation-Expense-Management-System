<?php
session_start();
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
        <title>Expense Log</title>
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
                            <h2 class="page-title">Expense Log</h2>
                            <div class="panel panel-default">
                                <div class="panel-heading">Record Expenses</div>
                                <div class="panel-body">
                                    <form id="addExSupport" method="post" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="aPurpose">Expenditure Purpose</label>
                                            <input type="text" name="aPurpose" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="Amount">Amount Spent (৳)</label>
                                            <input type="number" name="amount" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="note">Additional Note (optional)</label>
                                            <textarea name="note" class="form-control"></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label for="image">Upload Receipt</label>
                                            <input type="file" name="image" class="form-control" accept="image/*">
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

        <script>
            $('#addExSupport').on('submit', function(e) {
                e.preventDefault(); // Prevent form submission

                var formData = new FormData(this); // Use FormData for file upload
                var warnings = [];

                // Check if the note is empty and warn the user
                if (!$('textarea[name="note"]').val().trim()) {
                    warnings.push('Additional Note is empty.');
                }

                // Check if the image is empty and warn the user
                if (!$('input[name="image"]').val().trim()) {
                    warnings.push('Receipt (Image) is not uploaded.');
                }

                if (warnings.length > 0) {
                    // Display warning if optional fields are empty
                    Swal.fire({
                        title: 'Warning',
                        text: warnings.join('  '), // Combine warnings into a single string
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Submit Anyway',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // If user confirms, submit the form
                            submitForm(formData);
                        }
                    });
                } else {
                    // No warnings, submit the form directly
                    submitForm(formData);
                }
            });

            function submitForm(formData) {
                $.ajax({
                    url: 'exp_log_pro.php',
                    type: 'POST',
                    data: formData,
                    processData: false, // Prevent jQuery from converting the data into a query string
                    contentType: false, // Tell jQuery not to set content type
                    success: function(response) {
                        if (response.trim() === "success") {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Expense entry successfully added.',
                                icon: 'success',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'exp_log.php';
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.trim(), // Show error message from the server
                                icon: 'error',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("XHR Error:", xhr);
                        console.error("Status:", status);
                        console.error("Error:", error);
                        Swal.fire({
                            title: 'Unexpected Error',
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