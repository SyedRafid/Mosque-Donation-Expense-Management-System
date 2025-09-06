<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    if (isset($_GET['prev']) || isset($_GET['next'])) {
        if (isset($_GET['monthYear'])) {
            // Get the current month and year from "F Y" format
            $dateParts = explode(' ', $_GET['monthYear']);
            $month = date('n', strtotime($dateParts[0] . ' 1')); // Convert "October" to 10
            $year = (int)$dateParts[1]; // Extract the year as an integer

            // Check if "prev" button is pressed, decrement month; else, increment month for "next"
            if (isset($_GET['prev'])) {
                if ($month == 1) {
                    $month = 12;
                    $year--;
                } else {
                    $month--;
                }
            } elseif (isset($_GET['next'])) {
                if ($month == 12) {
                    $month = 1;
                    $year++;
                } else {
                    $month++;
                }
            }

            // Format the new month and year as "F Y" (e.g., "September 2024" or "November 2024")
            $formattedDate = date('F Y', strtotime("$year-$month-01"));

            // Redirect to the new URL with the formatted month and year
            header("Location: ?monthYear=" . urlencode($formattedDate));
            exit;
        }
    }


    $dateParts = explode(' ', isset($_GET['monthYear']) ? $_GET['monthYear'] : date('F Y'));
    $selectedMonth = date('n', strtotime($dateParts[0] . ' 1'));
    $selectedYear = isset($dateParts[1]) ? (int)$dateParts[1] : date('Y');

    // Pagination settings
    $recordsPerPage = 21;
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($currentPage - 1) * $recordsPerPage;

    // SQL query to join payment with either profile or extra_support, depending on whether pr_id or ex_id is used
    $sql = "SELECT 
    p.receipt_no, 
    COALESCE(pr.name, es.ex_name) AS name, 
    COALESCE(pr.phone, es.phone) AS phone, 
    IF(p.pr_id IS NOT NULL, 'profile', 'extra_support') AS source, 
    SUM(p.amount) AS total_amount, 
    GROUP_CONCAT(p.amount) AS individual_amounts, 
    GROUP_CONCAT(p.date) AS payment_dates, 
    GROUP_CONCAT(DISTINCT MONTH(p.date)) AS months, 
    GROUP_CONCAT(DISTINCT YEAR(p.date)) AS YEARS, 
    MAX(p.c_date) AS pTime,
    DATE(p.c_date) AS date_only,
    pr.fName AS fName,
    es.note AS ex_note
    FROM payment p 
    LEFT JOIN profile pr ON p.pr_id = pr.pr_id 
    LEFT JOIN extra_support es ON p.ex_id = es.ex_id 
    WHERE MONTH(p.c_date) = :month AND YEAR(p.c_date) = :year
    GROUP BY p.receipt_no 
    ORDER BY p.receipt_no DESC
    LIMIT :limit OFFSET :offset";

    // Prepare and execute the query
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':month', $selectedMonth, PDO::PARAM_INT);
    $stmt->bindParam(':year', $selectedYear, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $recordsPerPage, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);


    $stmt->execute();

    // Fetch all results
    $contributions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total number of records for pagination
    $totalRecordsSql = "SELECT COUNT(DISTINCT p.receipt_no) AS total FROM payment p 
                        LEFT JOIN profile pr ON p.pr_id = pr.pr_id 
                        LEFT JOIN extra_support es ON p.ex_id = es.ex_id
                        WHERE MONTH(p.c_date) = :month AND YEAR(p.c_date) = :year";

    $totalRecordsStmt = $dbh->prepare($totalRecordsSql);
    $totalRecordsStmt->bindParam(':month', $selectedMonth, PDO::PARAM_INT);
    $totalRecordsStmt->bindParam(':year', $selectedYear, PDO::PARAM_INT);
    $totalRecordsStmt->execute();
    $totalRecords = $totalRecordsStmt->fetchColumn();

    // Calculate total pages
    $totalPages = ceil($totalRecords / $recordsPerPage);

    $monthYear = isset($_GET['monthYear']) ? $_GET['monthYear'] : '';
?>

    <!doctype html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="theme-color" content="#3e454c">
        <title>Contribution Report</title>
        <link rel="icon" href="images/logo.jpg" type="image/png">
        <!-- Font awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
        <!-- Include Month Select Plugin -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">

        <style>
            /* Flexbox container for cards */
            .row {
                display: flex;
                flex-wrap: wrap;
                align-items: stretch;
            }

            /* Each card column should stretch */
            .col-lg-4,
            .col-md-6,
            .col-sm-12 {
                display: flex;
                justify-content: center;
                flex-basis: 100%;
                max-width: 100%;
            }

            @media (min-width: 576px) {
                .col-sm-12 {
                    flex-basis: 50%;
                    max-width: 50%;
                }
            }

            @media (min-width: 768px) {
                .col-md-6 {
                    flex-basis: 33.33%;
                    max-width: 33.33%;
                }
            }

            @media (min-width: 992px) {
                .col-lg-4 {
                    flex-basis: 33.33%;
                    max-width: 33.33%;
                }
            }

            .card-custom {
                display: flex;
                flex-direction: column;
                /* Change to column for vertical stacking */
                justify-content: space-between;
                align-items: stretch;
                padding: 20px;
                background-color: #fff;
                border-radius: 10px;
                box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
                margin-bottom: 30px;
                transition: all 0.3s ease;
                min-height: 150px;
                max-height: auto;
                /* Allow height to be dynamic */
                width: 100%;
            }

            .card-custom:hover {
                box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.2);
            }

            .ex_circle {
                background-color: #197f78;
                color: white;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 18px;
                flex-shrink: 0;
            }

            .profile_circle {
                background-color: #6a25d7;
                color: white;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 18px;
                flex-shrink: 0;
            }

            .details {
                flex-grow: 1;
                display: flex;
                flex-direction: column;
                justify-content: center;
                flex-wrap: nowrap;
                overflow: hidden;
            }

            .details-title {
                font-weight: bold;
                font-size: 18px !important;
                color: #be6912 !important;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .details p {
                margin: 0;
                font-size: 16px;
                color: #333;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: normal;
            }

            .months {
                color: #3e3f3a;
                font-size: 15px !important;
                font-weight: bold;
                text-align: right;
                flex-shrink: 0;
                margin-top: 15px;
            }

            .amount {
                font-size: 19px;
                color: #2b7f19;
                font-weight: bold;
                text-align: right;
                flex-shrink: 0;
                margin-bottom: auto;
            }

            /* Expanded card content styling */
            .card-expanded {
                display: none;
                /* Hide by default */
                background-color: #f8f9fa;
                padding: 10px;
                border-radius: 5px;
                margin-top: 10px;
                box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
                transition: max-height 0.3s ease;
                /* Add transition for max-height */
            }

            .amount-circle-container {
                display: flex;
                /* Use flexbox for alignment */
                align-items: center;
                /* Center vertically */
                justify-content: space-between;
                /* Space between amount and circle */
                margin-bottom: 15px;
                /* Space between this container and the details */

                .pagination {
                    margin-top: 20px;
                    /* Optional: space above */
                    margin-bottom: 20px;
                    /* Optional: space below */
                    width: 100%;
                    /* Ensure the pagination takes full width */
                }
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
                            <h2 class="page-title">Contribution Report</h2>

                            <div class="row mb-3">
                                <div class="col-md-12 text-center">
                                    <form method="GET" action="">
                                        <div class="d-inline-block">
                                            <button type="submit" name="prev" class="btn btn-secondary">Previous Month</button>
                                        </div>

                                        <div class="d-inline-block" style="min-width: 200px; max-width: 100%;">
                                            <input id="monthPicker" name="monthYear" type="text" class="form-control d-inline-block"
                                                value="<?= isset($_GET['monthYear']) ? $_GET['monthYear'] : date('F Y'); ?>">
                                        </div>

                                        <div class="d-inline-block">
                                            <button type="submit" name="next" class="btn btn-secondary">Next Month</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading">List of Contributions</div>
                                <div class="panel-body">
                                    <div class="row">
                                        <?php foreach ($contributions as $contribution): ?>
                                            <div class="col-lg-4 col-md-6 col-sm-12">
                                                <div class="card-custom" onclick="toggleCard(this)">
                                                    <div class="amount-circle-container">
                                                        <?php
                                                        $circleText = ($contribution['source'] == 'profile') ? 'MS' : 'ES';
                                                        echo "<div class='" . ($contribution['source'] == 'profile' ? 'profile_circle' : 'ex_circle') . "'>$circleText</div>";

                                                        if ($contribution['source'] == 'profile') {
                                                            $years = $contribution['YEARS'];
                                                            $formattedYear = '(' . substr($years, -2) . ')';
                                                            $months = explode(',', $contribution['months']);
                                                            $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                                            $monthString = (count($months) == 1) ? $monthNames[$months[0] - 1] : $monthNames[$months[0] - 1] . '-' . $monthNames[end($months) - 1];
                                                            echo "<div class='months'><p>{$monthString}{$formattedYear}</p></div>";
                                                        } else {
                                                            $formattedDate = (new DateTime($contribution['date_only']))->format('d-M(y)');
                                                            echo "<div class='months'><p>{$formattedDate}</p></div>";
                                                        }
                                                        ?>
                                                        <div class="amount"><?php echo number_format($contribution['total_amount'], 0); ?>৳</div>
                                                    </div>
                                                    <div class="details">
                                                        <p class="details-title">Receipt No: <?php echo $contribution['receipt_no']; ?></p>
                                                        <p style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 300px;">
                                                            <strong>Name:</strong> <?php echo htmlentities($contribution['name']); ?>
                                                        </p>
                                                        <?php if (!empty($contribution['fName'])): ?>
                                                            <p><strong>Father:</strong> <?php echo htmlentities($contribution['fName']); ?></p>
                                                        <?php endif; ?>
                                                        <p><strong>Phone:</strong> <?php echo !empty($contribution['phone']) ? htmlentities($contribution['phone']) : 'N/A'; ?></p>
                                                    </div>
                                                    <?php
                                                    $pTime = $contribution['pTime'];
                                                    if ($contribution['source'] == 'profile'): ?>
                                                        <div class="card-expanded">
                                                            <p><strong>Additional Info:</strong></p>
                                                            <?php
                                                            $amounts = explode(',', $contribution['individual_amounts']);
                                                            $dates = explode(',', $contribution['payment_dates']);
                                                            $paymentDetails = [];

                                                            foreach ($amounts as $index => $amount) {
                                                                $date = DateTime::createFromFormat('Y-m-d', $dates[$index]);
                                                                $paymentDetails[$date->format('Y-m')] = $amount;
                                                            }

                                                            ksort($paymentDetails);

                                                            foreach ($paymentDetails as $dateKey => $amount) {
                                                                $date = DateTime::createFromFormat('Y-m', $dateKey);
                                                                echo "<p><strong>Payment:</strong> " . $date->format('M Y') . " - " . number_format($amount, 2) . "৳</p>";
                                                            }
                                                            ?>
                                                            <div style="padding: 10px; background-color: #6a25d745; border-radius: 5px; margin-top: 10px; text-align: center;">
                                                                <strong>Payment Time:</strong> <?php echo date('d M Y h:i A', strtotime($pTime)); ?><br>
                                                            </div>
                                                        </div>
                                                    <?php else:
                                                        $ex_note = empty($contribution['ex_note']) ? 'N/A' : htmlentities($contribution['ex_note']); ?>
                                                        <div class="card-expanded">
                                                            <p><strong>Additional Note:</strong></p>
                                                            <p><?php echo $ex_note; ?></p>
                                                            <div style="padding: 10px; background-color: #197f7857; border-radius: 5px; margin-top: 10px; text-align: center;">
                                                                <strong>Payment Time:</strong> <?php echo date('d M Y h:i A', strtotime($pTime)); ?><br>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                        <?php if (empty($contributions)): ?>
                                            <div class="col-md-12" style="text-align: center; margin-top: 20px;">
                                                <h4>No contribution record found!</h4>
                                                <p>Please add some contributions to see them listed here.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div> <!-- End row -->

                                    <?php
                                    if (!empty($currentPage) && !empty($totalPages)) : ?>
                                        <div style="text-align: center; color: #98978b;">
                                            Page: <?= $currentPage ?> of <?= $totalPages ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Pagination controls -->
                                    <div style="padding: 20px; text-align: center;">
                                        <div class="btn-group" role="group">
                                            <?php if ($currentPage > 1): ?>
                                                <a style="background-color: #2b7f19; color: white; width:70px;"
                                                    href="?page=<?= $currentPage - 1 ?><?= !empty($monthYear) ? '&monthYear=' . urlencode($monthYear) : '' ?>"
                                                    class="btn">Previous</a>
                                        </div>
                                        <div class="btn-group" role="group">
                                        <?php endif; ?>
                                        <?php if ($currentPage < $totalPages): ?>
                                            <a style="background-color: #2b7f19; color: white; width:70px;"
                                                href="?page=<?= $currentPage + 1 ?><?= !empty($monthYear) ? '&monthYear=' . urlencode($monthYear) : '' ?>"
                                                class="btn">Next</a> <?php
                                                                    endif; ?>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Script imports -->
        <script src="js/jquery.min.js"></script>
        <script src="js/main.js"></script>
        <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.2.2/js/fileinput.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
        <script>
            function toggleCard(card) {
                const expandedContent = card.querySelector('.card-expanded'); // Get the expanded content within the card
                if (expandedContent.style.display === 'block') {
                    expandedContent.style.display = 'none'; // Hide the expanded content
                } else {
                    expandedContent.style.display = 'block'; // Show the expanded content
                }
            }
        </script>

        <script>
            flatpickr("#monthPicker", {
                plugins: [
                    new monthSelectPlugin({
                        shorthand: true, // Use shorthand (like Jan, Feb)
                        dateFormat: "F Y", // Format display as 'Month Year'
                        altFormat: "F Y", // Alternative display format
                    })
                ],
                onChange: function(selectedDates, dateStr, instance) {

                    // Automatically submit the form when a date is selected
                    document.querySelector('form').submit();
                    const selectedDate = new Date(selectedDates[0]);
                    monthPicker.setDate(selectedDate, true);
                },
                disableMobile: true
            });
        </script>


    </body>

    </html>

<?php
}
?>