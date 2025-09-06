<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    $qid = isset($_GET['profileId']) ? intval($_GET['profileId']) : 0;

    if ($qid == 0) {
        echo "<script>
            alert('Invalid Profile ID');
            window.location.href = 'monthly_support.php';
          </script>";
        exit;
    }

    // Fetch the profile creation year (con_date)
    $sql = "SELECT con_date FROM profile WHERE pr_id = :profileId";
    $query = $dbh->prepare($sql);
    $query->bindParam(':profileId', $qid, PDO::PARAM_INT);
    $query->execute();
    $profile = $query->fetch(PDO::FETCH_ASSOC);

    $con_date = $profile['con_date'];
    $creationYear = date('Y', strtotime($con_date)); // Extract the creation year
    $creationMonth = date('m', strtotime($con_date)); // Extract the creation month
    $currentYear = $creationYear; // Start from profile creation year

    // Fetch paid months from the payment table
    $sql = "SELECT MONTH(payment.date) as paidMonth, YEAR(payment.date) as paidYear 
        FROM payment 
        WHERE pr_id = :profileId";
    $query = $dbh->prepare($sql);
    $query->bindParam(':profileId', $qid, PDO::PARAM_INT);
    $query->execute();
    $paidMonths = $query->fetchAll(PDO::FETCH_ASSOC);

    // Determine which years should be included in the dropdown
    $yearDropdown = [];

    while (true) {
        // Filter paid months for the current year
        $paidMonthsForYear = array_filter($paidMonths, function ($item) use ($currentYear) {
            return $item['paidYear'] == $currentYear;
        });

        // Check if it's the contribution year
        if ($currentYear == $creationYear) {
            // Count unpaid months starting from the creation month
            $unpaidCount = 0;
            for ($month = $creationMonth; $month <= 12; $month++) {
                if (!in_array(['paidMonth' => $month, 'paidYear' => $currentYear], $paidMonthsForYear)) {
                    $unpaidCount++;
                }
            }

            // If there are unpaid months in the contribution year, break the loop
            if ($unpaidCount > 0) {
                $yearDropdown[] = $currentYear; // Add the current year to the dropdown
                break; // Exit loop if current year has unpaid months
            } else {
                // Move to the next year if all months are paid in the contribution year
                $currentYear++;
            }
        } else {
            // For other years, check if all 12 months are paid
            if (count($paidMonthsForYear) === 12) {
                $currentYear++; // Move to the next year
            } else {
                $yearDropdown[] = $currentYear; // Add the current year to the dropdown
                break; // Exit loop if current year has unpaid months
            }
        }
    }
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
        <title>Select Month(S)</title>
        <link rel="icon" href="images/logo.jpg" type="image/png">
        <!-- Font awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
        <!-- Sandstone Bootstrap CSS -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
        <!-- Admin Style -->

        <style>
            .month-card {
                margin-bottom: 20px;
                transition: transform 0.2s;
                cursor: pointer;
            }

            .month-card:hover {
                transform: scale(1.05);
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            }

            .month-toggle {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .month-label {
                font-size: 18px;
                font-weight: bold;
            }

            .custom-toggle {
                position: relative;
                display: inline-block;
                width: 40px;
                height: 20px;
            }

            .custom-toggle input {
                display: none;
            }

            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                transition: .4s;
                border-radius: 20px;
            }

            .slider:before {
                position: absolute;
                content: "";
                height: 12px;
                width: 12px;
                left: 4px;
                bottom: 4px;
                background-color: white;
                transition: .4s;
                border-radius: 50%;
            }

            input:checked+.slider {
                background-color: #337518;
            }

            input:checked+.slider:before {
                transform: translateX(20px);
            }

            .custom-toggle input:disabled+.slider {
                background-color: #dc3545;
            }

            .custom-toggle input:disabled:checked+.slider {
                background-color: #6c757d;
            }

            .disabled-input-conMonth {
                background-color: #ffb63078 !important;
                ;
            }

            .disabled-input-paidMonth {
                background-color: #55f21478 !important;
                ;
            }

            .enabled-input {
                background-color: #f0f0f0 !important;
                ;
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
                            <h2 class="page-title">Process Payment</h2>
                            <div class="panel panel-default">
                                <div class="panel-heading">Select Year and Month(s) to Pay</div>
                                <div class="panel-body">
                                    <form id="paymentForm" method="post">
                                        <div class="select-bar row">
                                            <?php
                                            $sql = "SELECT profile.* FROM profile WHERE profile.pr_id = :qid";
                                            $query = $dbh->prepare($sql);
                                            $query->bindValue(':qid', $qid, PDO::PARAM_INT);
                                            $query->execute();
                                            $result = $query->fetch(PDO::FETCH_ASSOC);

                                            if ($result) {
                                                $name = $result['name'];
                                                $phone = $result['phone'];
                                                $salary = $result['salary'];
                                            } else {
                                                $name = ''; // Default value if no result
                                                $phone = '';
                                                $salary = '';
                                            }
                                            ?>

                                            <div class="col-md-6">
                                                <label for="name">Name:</label>
                                                <input type="text" class="form-control" id="name" value="<?php echo htmlentities($name); ?>" readonly>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="phone">Phone:</label>
                                                <input type="text" class="form-control" id="phone" value="<?php echo htmlentities($phone); ?>" readonly>
                                            </div>

                                            <div class="w-100 mb-4"></div>

                                            <div class="col-md-6">
                                                <label for="salary">Monthly Contribution (৳):</label>
                                                <input type="number" class="form-control" id="salary" value="<?php echo htmlentities($salary); ?>" readonly>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="year">Select Year</label>
                                                <select id="year" name="year" class="form-control" disabled>
                                                    <?php
                                                    // Display the years in the dropdown
                                                    foreach ($yearDropdown as $year) {
                                                        echo "<option value='$year'>$year</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="kola mt-5">
                                            <div class="row">
                                                <?php
                                                $currentMonth = intval(date('m'));
                                                $unpaidMonth = null;

                                                for ($month = 1; $month <= 12; $month++) {
                                                    // Get the short month name (e.g., Jan, Feb, Mar)
                                                    $monthName = date('M', mktime(0, 0, 0, $month, 10));
                                                    $isPaid = in_array($month, array_column($paidMonths, 'paidMonth'));
                                                    $isDisabled = $isPaid || ($unpaidMonth !== null);

                                                    // Determine the first unpaid month
                                                    if (!$isPaid && $unpaidMonth === null) {
                                                        $unpaidMonth = $month;
                                                    }

                                                    // Start generating the month card
                                                    echo '<div class="col-md-4">';
                                                    echo '<div class="card month-card">';
                                                    echo '<div class="card-body month-toggle">';
                                                    echo '<span class="month-label">' . $monthName . '</span>';
                                                    echo '<input type="number" class="form-control mt-2" name="amounts[]" id="amount-' . $month . '" value="' . htmlentities($salary) . '" ' . 'disabled' . ' style="width: 80px; display: inline-block; margin-left: 10px;">';
                                                    echo '<label class="custom-toggle">';
                                                    echo '<input type="checkbox" class="month-checkbox" id="month-' . $month . '" name="months[]" value="' . $month . '" ' . ($isPaid ? 'checked disabled' : '') . ' ' . ($isDisabled ? 'disabled' : '') . '>';
                                                    echo '<span class="slider"></span>';
                                                    echo '</label>';
                                                    echo '</div>'; // Close card body
                                                    echo '</div>'; // Close month card
                                                    echo '</div>'; // Close column
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <div class="text-center mt-3 mb-3">
                                            <button type="submit" class="btn btn-sm p-3" style="background-color: #337518; color: white;">Submit Payment</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="js/jquery.min.js"></script>
        <script src="js/main.js"></script>
        <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.2.2/js/fileinput.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#myTable').DataTable(); // Replace '#myTable' with your actual table ID
            });
        </script>
        <script>
            $(document).ready(function() {
                $('#yourFileInputId').fileinput(); // Replace with your actual file input ID
            });
        </script>

        <script>
            $(document).ready(function() {
                let paidMonths = [];
                let conYears = [];
                let conMonths = [];
                // Function to update the months based on the selected year
                function updateMonthsForYear(selectedYear) {
                    $.ajax({
                        url: 'get_paid_months.php',
                        type: 'POST',
                        data: {
                            profileId: <?php echo $qid; ?>, // Use PHP to insert the profile ID dynamically
                            year: selectedYear
                        },
                        dataType: 'json',
                        success: function(response) {
                            // Save paid months to the global variable
                            paidMonths = response.paidMonths || [];

                            // Reset all months checkboxes
                            $('.month-checkbox').prop('checked', false).prop('disabled', false);

                            // Check if the selected year matches the year of the con_date
                            if (response.conYear == selectedYear) {

                                conYears = [response.conYear];

                                // Populate the array with month numbers from 1 to conMonth (inclusive)
                                for (let i = 1; i < response.conMonth; i++) {
                                    conMonths.push(i.toString()); // Convert the month number to a string
                                }

                                // Disable months and set them to checked as needed
                                for (let i = 1; i < response.conMonth; i++) {
                                    $('#month-' + i).prop('checked', true).prop('disabled', true);
                                    $('#amount-' + i).val(0).prop('disabled', true).addClass('disabled-input-conMonth');
                                }
                            }

                            // Disable all paid months for the selected year
                            if (response.paidMonths && response.paidMonths.length > 0) {
                                response.paidMonths.forEach(function(month) {
                                    $('#month-' + month).prop('checked', true).prop('disabled', true);

                                    if (response.paidAmounts && typeof response.paidAmounts[month] !== 'undefined') {
                                        $('#amount-' + month).val(response.paidAmounts[month]).prop('disabled', true).addClass('disabled-input-paidMonth');
                                    }
                                });
                            }

                            // Enable the first unpaid month, and disable future months until the previous month is paid
                            let firstUnpaidMonth = response.firstUnpaidMonth;
                            if (firstUnpaidMonth) {
                                for (let i = firstUnpaidMonth + 1; i <= 12; i++) {
                                    $('#month-' + i).prop('disabled', true);
                                }
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'An error occurred while fetching paid months for the selected year.', 'error');
                        }
                    });
                }

                // When a month is selected, enable the next unpaid month
                $('.month-checkbox').change(function() {
                    let currentMonth = parseInt($(this).val());
                    let salaryInput = $('#amount-' + currentMonth); // Get the associated salary input field

                    if ($(this).is(':checked')) {
                        // Enable the salary input field when the checkbox is checked
                        salaryInput.prop('disabled', false).addClass('enabled-input');                     
                        // Enable the next month only if the current month is selected
                        $('#month-' + (currentMonth + 1)).prop('disabled', false);
                    } else {
                        // Disable all subsequent months and uncheck them
                        salaryInput.prop('disabled', true);
                        for (let i = currentMonth + 1; i <= 12; i++) {
                            $('#month-' + i).prop('disabled', true).prop('checked', false);
                            $('#amount-' + i).prop('disabled', true);
                        }
                    }
                });

                // Handle year dropdown change
                $('#year').change(function() {
                    let selectedYear = $(this).val();
                    if (!selectedYear) {
                        Swal.fire('Error!', 'Please select a valid year.', 'error');
                        return;
                    }
                    updateMonthsForYear(selectedYear);
                });

                // Initial update for the selected year on page load
                updateMonthsForYear($('#year').val());

                // Handle form submission
                $('#paymentForm').on('submit', function(e) {
                    e.preventDefault(); // Prevent the default form submission

                    let selectedYear = $('#year').val();
                    let combinedMonths = [];
                    const selectedMonths = $('input[name="months[]"]:checked').map(function() {
                        return $(this).val();
                    }).get();

                    const salaryData = $('input[name="months[]"]:checked').map(function() {
                        const monthId = $(this).attr('id').split('-')[1]; // Get the month from the checkbox ID
                        return $('#amount-' + monthId).val(); // Get the salary for that month
                    }).get(); // Get the salary values corresponding to selected months

                    combinedMonths = conMonths.concat(paidMonths);

                    let filteredMonths = [];
                    let filtersalaryData = [];

                    // Check if selectedYear is in conYears array
                    if (conYears.map(String).includes(selectedYear) && paidMonths.length === 0) {
                        // Filter out months that are in conMonths
                        filteredMonths = selectedMonths.filter(month => !conMonths.map(String).includes(month));

                        // Get salaries for the filtered months
                        filtersalaryData = filteredMonths.map(month => $('#amount-' + month).val());

                    } else if (conYears.map(String).includes(selectedYear) && paidMonths.length > 0) {
                        filteredMonths = selectedMonths.filter(month => !combinedMonths.map(String).includes(month));

                        // Get salaries for the filtered months
                        filtersalaryData = filteredMonths.map(month => $('#amount-' + month).val());
                    } else {
                        // Convert paidMonths to strings before filtering
                        filteredMonths = selectedMonths.filter(month => !paidMonths.map(String).includes(month));

                        // Get salaries for the filtered months
                        filtersalaryData = filteredMonths.map(month => $('#amount-' + month).val());
                    }

                    if (filteredMonths.length === 0) {
                        Swal.fire('Warning!', 'Please select at least one month to proceed with the payment.', 'warning');
                        return; // Stop the form submission process
                    }

                    // Ensure salary data corresponds with selected months
                    if (salaryData.length !== selectedMonths.length) {
                        Swal.fire('Error!', 'Amounts must be provided for the selected month(s).', 'error');
                        return;
                    }

                    // Stop the form submission process if invalid salary exists
                    let invalidSalary = filtersalaryData.some(salary => salary === '' || salary === null || isNaN(salary));
                    if (invalidSalary) {
                        Swal.fire('Error!', 'Please enter a valid amount for all selected month(s). At least 0 is required.', 'error');
                        return;
                    }

                    // Stop the form submission process if invalid salary exists
                    let nagSalary = filtersalaryData.some(salary => (salary < 0));
                    if (nagSalary) {
                        Swal.fire('Error!', 'Please enter valid amount(s). They cannot be negative.', 'error');
                        return;
                    }

                    // Prepare data for SweetAlert
                    let monthNames = [];
                    let totalAmount = 0;

                    filteredMonths.forEach((month, index) => {
                        const monthName = new Date(0, month - 1).toLocaleString('default', {
                            month: 'long'
                        });
                        const salary = parseFloat(filtersalaryData[index]);
                        monthNames.push(`${monthName}: ${salary}৳`);
                        totalAmount += salary;
                    });

                    // Show SweetAlert with month names, salaries, and total amount
                    Swal.fire({
                        title: 'Confirm Payment',
                        html: `<p>Please confirm the payment details:</p>
                               <ul>${monthNames.map(item => `<li style="margin: 7px;">${item}</li>`).join('')}</ul>
                                      <hr style="border-top: 2px solid #545454; width: 70%; margin: revert;">
                               <p><strong>Total Amount: ৳${totalAmount}</strong></p>`,
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'Confirm Payment',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Proceed with the form submission
                            $.ajax({
                                url: 'process_payment.php',
                                type: 'POST',
                                data: {
                                    profileId: <?php echo $qid; ?>, // Use PHP to insert the profile ID dynamically
                                    months: filteredMonths,
                                    year: $('#year').val(),
                                    salaries: filtersalaryData // Pass the salary data as an array
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire({
                                            title: 'Success!',
                                            text: 'Payments processed successfully!',
                                            icon: 'success',
                                            confirmButtonText: 'OK'
                                        }).then(() => {
                                            window.location.href = 'contribution_report.php';
                                        });
                                    } else {
                                        Swal.fire('Error!', response.message, 'error');
                                    }
                                },
                                error: function() {
                                    Swal.fire('Error!', 'An error occurred while processing your payment.', 'error');
                                }
                            });
                        }
                    });
                });
            });
        </script>

    </body>

    </html>
<?php
}
?>