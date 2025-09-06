<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Check if the user is logged in
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
} else {
    // Initialize variables
    $selectedMonth = date('m'); // Default to current month
    $selectedYear = date('Y'); // Default to current year
    $search = ''; // Initialize search variable

    // Check if month and year are set in the GET parameters
    if (isset($_GET['month'])) {
        $selectedMonth = $_GET['month'];
    }

    if (isset($_GET['year'])) {
        $selectedYear = $_GET['year'];
    }

    // Check if search is set
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
    }

    $convartDate = $selectedYear . '-' . str_pad($selectedMonth, 2, '0', STR_PAD_LEFT) . '-01';

    // Prepare SQL query based on search input
    if (!empty($search)) {
        $sql = "SELECT * FROM profile WHERE name LIKE :search OR phone LIKE :search";
        $query = $dbh->prepare($sql);
        $query->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);
    } else {
        // SQL query to fetch profiles whose pr_id is not found in the payment table for the selected month and year
        $sql = "
        SELECT profile.* 
        FROM profile 
        LEFT JOIN payment 
        ON profile.pr_id = payment.pr_id 
        AND MONTH(payment.date) = :selectedMonth 
        AND YEAR(payment.date) = :selectedYear
         WHERE 
        profile.con_date <= :convartDate 
        AND payment.pr_id IS NULL
        ORDER BY profile.con_date";

        $query2 = $dbh->prepare($sql);
        $query2->bindValue(':selectedMonth', $selectedMonth, PDO::PARAM_INT);
        $query2->bindValue(':selectedYear', $selectedYear, PDO::PARAM_INT);
        $query2->bindValue(':convartDate', $convartDate, PDO::PARAM_INT);
        $query2->execute();
        $results2 = $query2->fetchAll(PDO::FETCH_OBJ);
        $totalRemainingPeople = $query2->rowCount();

        // SQL query to fetch profiles whose pr_id is found in the payment table for the selected month and year
        $sql = "
        SELECT profile.* 
        FROM profile 
        INNER JOIN payment 
        ON profile.pr_id = payment.pr_id 
        AND MONTH(payment.date) = :selectedMonth 
        AND YEAR(payment.date) = :selectedYear";

        $query3 = $dbh->prepare($sql);
        $query3->bindValue(':selectedMonth', $selectedMonth, PDO::PARAM_INT);
        $query3->bindValue(':selectedYear', $selectedYear, PDO::PARAM_INT);
        $query3->execute();
        $results3 = $query3->fetchAll(PDO::FETCH_OBJ);
        $totalPaidPeople = $query3->rowCount();

        // Fetch the current year for the year dropdown
        $currentYear = date('Y');
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
    <title>Monthly Support</title>
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
            box-shadow:-3px 3px 11px 1px rgb(0 0 0 / 20%);
        }

        .card-body {
            padding: 0;
        }

        .card-title {
            background-color: #337518;
            color: white;
            padding: 10px 15px;
            margin: 0;
            border-radius: 0;
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .search-bar {
            margin-bottom: 20px;
        }

        .select-bar {
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
                        <h2 class="page-title">Monthly Support</h2>

                        <!-- Month and Year Selection Side by Side -->
                        <div class="select-bar row">
                            <div class="col-md-6">
                                <label for="month">Select Month:</label>
                                <select id="month" name="month" class="form-control" onchange="updateMonthYear()">
                                    <?php
                                    for ($i = 1; $i <= 12; $i++) {
                                        $selected = ($i == $selectedMonth) ? 'selected' : '';
                                        echo "<option value='$i' $selected>" . date('F', mktime(0, 0, 0, $i, 1)) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="year">Select Year:</label>
                                <select id="year" name="year" class="form-control" onchange="updateMonthYear()">
                                    <?php
                                    $startYear = $currentYear - 6; // Start year
                                    $endYear = $currentYear + 4; // Show up to 3 years after the current year

                                    for ($year = $startYear; $year <= $endYear; $year++) {
                                        $selected = ($year == $selectedYear) ? 'selected' : '';
                                        echo "<option value='$year' $selected>$year</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

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

                        <?php
                        if (!empty($search)) {
                        ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">Enlisted People</div>
                                <div class="panel-body">
                                    <div class="row">
                                        <?php
                                        if ($query->rowCount() > 0) {
                                            foreach ($results as $result) {
                                        ?>
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="card" onclick="redirectToPaymentPage(<?php echo $result->pr_id; ?>)">
                                                        <div class="card-body">
                                                            <h5 class="card-title"><?php echo htmlentities($result->name); ?></h5>
                                                            <p class="card-text card-text-indent">NID: <?php echo htmlentities($result->nid); ?></p>
                                                            <p class="card-text card-text-indent">Phone: <?php echo htmlentities($result->phone); ?></p>
                                                            <p class="card-text card-text-indent">Monthly Contribution: <?php echo htmlentities($result->salary) . " ৳"; ?></p>
                                                            <p class="card-text card-text-indent">Father's Name: <?php echo htmlentities($result->fName); ?></p>
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
                        <?php } else {
                        ?>
                            <div class="panel panel-default">
                                <div class="panel-heading" style="color: red;">
                                    <?php
                                    if ($totalRemainingPeople === 0) {
                                        echo "* All Profiles Have Settled Their Balances!";
                                    } else {
                                        $remainingText = ($totalRemainingPeople === 1) ? "Profile" : "Profiles";
                                        echo "* " . $totalRemainingPeople . " " . $remainingText . " with Outstanding Balances!";
                                    }
                                    ?>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <?php
                                        if ($totalRemainingPeople > 0) {
                                            foreach ($results2 as $result) {
                                        ?>
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="card" onclick="redirectToPaymentPage(<?php echo $result->pr_id; ?>)">
                                                        <div class="card-body">
                                                            <h5 class="card-title"><?php echo htmlentities($result->name); ?></h5>
                                                            <p class="card-text card-text-indent">NID: <?php echo htmlentities($result->nid); ?></p>
                                                            <p class="card-text card-text-indent">Phone: <?php echo htmlentities($result->phone); ?></p>
                                                            <p class="card-text card-text-indent">Monthly Contribution: <?php echo htmlentities($result->salary) . " ৳"; ?></p>
                                                            <p class="card-text card-text-indent">Father's Name: <?php echo htmlentities($result->fName); ?></p>
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

                            <div class="panel panel-default">
                                <div class="panel-heading" style="color: #337518;">
                                    <?php
                                    if ($totalPaidPeople === 0) {
                                        echo "* No Profiles Have Made Their Payments for This Month.";
                                    } else {
                                        $profileText = ($totalPaidPeople === 1) ? "Profile" : "Profiles";
                                        echo "* " . $totalPaidPeople . " " . $profileText . " Have Made Their Payments for This Month Successfully!";
                                    }
                                    ?>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <?php
                                        if ($totalPaidPeople > 0) {
                                            foreach ($results3 as $result) {
                                        ?>
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="card" onclick="redirectToPaymentPage(<?php echo $result->pr_id; ?>)">
                                                        <div class="card-body">
                                                            <h5 class="card-title"><?php echo htmlentities($result->name); ?></h5>
                                                            <p class="card-text card-text-indent">NID: <?php echo htmlentities($result->nid); ?></p>
                                                            <p class="card-text card-text-indent">Phone: <?php echo htmlentities($result->phone); ?></p>
                                                            <p class="card-text card-text-indent">Monthly Contribution: <?php echo htmlentities($result->salary) . " ৳"; ?></p>
                                                            <p class="card-text card-text-indent">Father's Name: <?php echo htmlentities($result->fName); ?></p>
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
                        <?php
                        }
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Loading Scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        function trimSearchInput() {
            const searchInput = document.getElementById('search');
            searchInput.value = searchInput.value.trim();
        }
    </script>
    <script>
        function updateMonthYear() {
            const month = document.getElementById('month').value;
            const year = document.getElementById('year').value;
            const search = document.getElementById('search').value;
            // Redirect to the same page with updated month and year
            window.location.href = `monthly_support.php?month=${month}&year=${year}&search=${search}`; // Include search parameter
        }

        function resetSearch() {
            document.getElementById('search').value = '';
            document.getElementById('month').selectedIndex = 0;
            document.getElementById('year').selectedIndex = document.getElementById('year').options.length - 1;
            window.location.href = 'monthly_support.php';
        }
    </script>

    <script>
        function redirectToPaymentPage(profileId) {
            let url = `select_month.php?profileId=${profileId}`;
            window.location.href = url;
        }
    </script>

</body>

</html>