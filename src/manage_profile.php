<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
} else {
    // Initialize search variable
    $search = "";
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
    }

    if (isset($_GET['del']) && isset($_GET['id'])) {
        $id = $_GET['id'];
    
        // Step 1: Check if any related entries exist in the payment table for the given pr_id
        $sqlCheck = "SELECT COUNT(*) FROM payment WHERE pr_id = :id";
        $queryCheck = $dbh->prepare($sqlCheck);
        $queryCheck->bindParam(':id', $id, PDO::PARAM_INT);
        $queryCheck->execute();
    
        $paymentCount = $queryCheck->fetchColumn(); 
    
        // Step 2: If payment records exist, show error, otherwise delete profile
        if ($paymentCount > 0) {
            // Step 3: There are payment entries, show error message
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Unable to delete the profile because there is existing payment information associated with it.',
                        icon: 'error'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'manage_profile.php';
                        }
                    });
                });
            </script>";
            
        } else {
            // No payment entries, proceed to delete the profile
            $sqlDeleteProfile = "DELETE FROM profile WHERE pr_id = :id";
            $queryDeleteProfile = $dbh->prepare($sqlDeleteProfile);
            $queryDeleteProfile->bindParam(':id', $id, PDO::PARAM_INT);
            $queryDeleteProfile->execute();
    
            // Use JavaScript to trigger SweetAlert after deletion
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Profile deleted successfully.',
                        icon: 'success'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'manage_profile.php';
                        }
                    });
                });
            </script>";
        }
    }  
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
    <title>Manage Profile</title>
    <link rel="icon" href="images/logo.jpg" type="image/png">
    <!-- Font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Sandstone Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Admin Style -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        .card-text-indent {
            margin-top: 10px;
            margin-left: 15px;
        }

        .card {
            margin-bottom: 20px;
            border: 2px solid #337518;
            border-radius: 5px;
            margin: 15px 0;
            box-shadow: -3px 3px 11px 1px rgb(0 0 0 / 20%);
        }

        .card-body {
            padding: 0;
        }

        .card-title {
            background-color: #337518;
            color: white;
            padding: 10px 15px;
            margin: 0;
            border-radius: 0 0 0 0;
        }

        .card-divider {
            border-top: 2px solid #337518;
            margin: 10px 0;
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .btn-transparent {
            border: none;
            background: transparent;
            color: #333;
        }

        .btn-transparent:hover {
            color: #007bff;
        }

        .search-bar {
            margin-bottom: 20px;
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
                        <h2 class="page-title">Manage Profile</h2>

                        <!-- Search Form -->
                        <form method="GET" class="search-bar" onsubmit="trimSearchInput()">
                            <div class="form-group">
                                <label for="search">Search:</label>
                                <input type="text" class="form-control" id="search" name="search" placeholder="Search by name or phone" value="<?php echo htmlentities($search); ?>">
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn" style="background-color: #337518; color: white;">Search</button>
                                <!-- Reset Button -->
                                <button type="reset" class="btn" style="background-color: #337518; color: white; margin-left: 10px;" onclick="resetSearch()">Reset</button>
                            </div>
                        </form>

                        <div class="panel panel-default">
                            <div class="panel-heading">Listed Profile</div>
                            <div class="panel-body">
                                <div class="row">
                                    <?php
                                    // Modify the SQL query based on the search input
                                    $sql = "SELECT * FROM profile WHERE name LIKE :search OR phone LIKE :search ORDER BY profile.con_date";
                                    $query = $dbh->prepare($sql);
                                    $query->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
                                    $query->execute();
                                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                                    if ($query->rowCount() > 0) {
                                        foreach ($results as $result) {
                                    ?>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><?php echo htmlentities($result->name); ?></h5>
                                                        <p class="card-text card-text-indent">NID: <?php echo htmlentities($result->nid); ?></p>
                                                        <p class="card-text card-text-indent">Phone: <?php echo htmlentities($result->phone); ?></p>
                                                        <p class="card-text card-text-indent">Monthly Contribution: <?php echo htmlentities($result->salary) . " ৳"; ?></p>
                                                        <p class="card-text card-text-indent">Father's Name: <?php echo htmlentities($result->fName); ?></p>
                                                        <p class="card-text card-text-indent">First Contribution:
                                                            <?php
                                                            $date = new DateTime($result->con_date); // Create a DateTime object
                                                            echo $date->format('M Y');
                                                            ?>
                                                        </p>
                                                        <div class="card-divider"></div>
                                                        <div class="action-buttons">
                                                            <a href="edit_profile.php?id=<?php echo htmlentities($result->pr_id); ?>" class="btn btn-transparent btn-xs" tooltip-placement="top" tooltip="Edit">
                                                                <i class="fa fa-pencil fa-2x"></i>
                                                            </a>
                                                            <a href="#" class="btn btn-transparent btn-xs" tooltip-placement="top" tooltip="Remove" onclick="confirmDelete(<?php echo htmlentities($result->pr_id); ?>)">
                                                                <i class="fa fa-times fa fa-white fa-2x"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    <?php
                                        }
                                    } else {
                                        echo "<p><strong>&nbsp;&nbsp;&nbsp;No results found.</strong></p>";
                                    }
                                    ?>
                                </div> <!-- End row -->
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Loading Scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/main.js"></script>
    <script>
        function trimSearchInput() {
            const searchInput = document.getElementById('search');
            searchInput.value = searchInput.value.trim();
        }
    </script>

    <script>
        function confirmDelete(pr_id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'manage_profile.php?id=' + pr_id + '&del=delete';
                }
            });
        }
    </script>

    <script>
        function resetSearch() {
            document.getElementById('search').value = ''; // Clear the search input
            window.location.href = 'manage_profile.php'; // Redirect to the same page
        }
    </script>

</body>

</html>