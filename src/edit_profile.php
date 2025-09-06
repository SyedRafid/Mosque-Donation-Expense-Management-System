<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    $qid = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($qid == 0) {
        echo "<script>
            alert('Invalid Profile ID');
            window.location.href = 'manage_application.php';
          </script>";
        exit;
    }
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

        <title>Edit Application</title>
        <link rel="icon" href="images/logo.jpg" type="image/png">
        <!-- Font Awesome 6.0.0 CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <!-- Sandstone Bootstrap CSS -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <!-- Bootstrap Datatables -->
        <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
        <!-- Bootstrap select -->
        <link rel="stylesheet" href="css/bootstrap-select.css">
        <!-- Admin Style -->
        <link rel="stylesheet" href="css/style.css">
        <style>
            .errorWrap {
                padding: 10px;
                margin: 0 0 20px 0;
                background: #fff;
                border-left: 4px solid #dd3d36;
                box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
            }

            .succWrap {
                padding: 10px;
                margin: 0 0 20px 0;
                background: #fff;
                border-left: 4px solid #5cb85c;
                box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
            }

            .table-responsive {
                overflow-x: auto;
            }
        </style>
    </head>

    <body>
        <?php include('includes/header.php'); ?>

        <div class="ts-main-content">
            <?php include('includes/leftbar.php'); ?>
            <div class="content-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="page-title">Edit Application</h2>
                            <div class="panel panel-default">
                                <div class="panel-heading">Edit Details</div>
                                <div class="panel-body">
                                    <form id="addProfileForm" method="post" enctype="multipart/form-data">
                                        <?php
                                        $sql = "SELECT * from profile where pr_id = :qid";
                                        $query = $dbh->prepare($sql);
                                        $query->bindParam(':qid', $qid, PDO::PARAM_INT);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        if ($query->rowCount() > 0) {
                                            foreach ($results as $result) {
                                                // Default values if the result is null
                                                $name = isset($result->name) ? htmlentities($result->name) : '';
                                                $nid = isset($result->nid) ? htmlentities($result->nid) : '';
                                                $fName = isset($result->fName) ? htmlentities($result->fName) : '';
                                                $phone = isset($result->phone) ? htmlentities($result->phone) : '';
                                                $salary = isset($result->salary) ? htmlentities($result->salary) : '';
                                        ?>

                                                <div class="form-group">
                                                    <label for="fullName">Name</label>
                                                    <input type="text" name="name" class="form-control" value="<?php echo $name; ?>" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="nid">NID</label>
                                                    <input type="number" name="nid" class="form-control" value="<?php echo $nid; ?>" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="fName">Father's Name</label>
                                                    <input type="text" name="fName" class="form-control" value="<?php echo $fName; ?>" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="phone">Phone</label>
                                                    <input type="tel" name="phone" id="phone" onBlur="userAvailability()" class="form-control" value="<?php echo $phone; ?>" required>
                                                    <span id="user-availability-status1" style="font-size:12px; color: gray;"></span>
                                                </div>

                                                <div class="form-group">
                                                    <label for="salary">Monthly Contribution (৳)</label>
                                                    <input type="number" name="salary" class="form-control" value="<?php echo $salary; ?>" required>
                                                </div>

                                                <div class="form-group">
                                                    <input type="hidden" name="id" class="form-control" value="<?php echo $qid; ?>" required>
                                                </div>
                                        <?php
                                            }
                                        }
                                        ?>
                                        <button type="submit" id="submit" class="btn" style="background-color: #2b7f19; color: white;">Update</button>
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
    function userAvailability() {
        $("#loaderIcon").show();
        var phone = $("#phone").val();
        var qid = <?php echo $qid; ?>; 
        $.ajax({
            url: "check_availability2.php",
            type: "POST",
            data: { phone: phone, qid: qid },
            success: function(data) {
                $("#user-availability-status1").html(data);
                $("#loaderIcon").hide();
            },
            error: function() {
                $("#loaderIcon").hide();
                $("#user-availability-status1").html("<span style='color:red'> An error occurred. Please try again. </span>");
            }
        });
    }
</script>


        <script>
            $('#addProfileForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    url: 'editProfile_pro.php', // Ensure the URL is correct
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        response = response.trim(); // Trim any whitespace around the response

                        if (response === "success") {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Profile information has been successfully updated.',
                                icon: 'success',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'manage_profile.php';
                                }
                            });
                        } else if (response === "error") {
                            Swal.fire({
                                title: 'Error!',
                                text: 'There was an issue updating the profile. Please try again.',
                                icon: 'error',
                            });
                        } else if (response === "warning") {
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
    </body>

    </html>

<?php } ?>