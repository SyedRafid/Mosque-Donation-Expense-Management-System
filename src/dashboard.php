<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
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
        <title>Dashboard</title>
        <link rel="icon" href="images/logo.jpg" type="image/png">
        <!-- Font awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

        <style>
            #chartContainer {
                max-width: 300px;
                margin: 20px auto;
            }

            #chartContainer2 {
                max-width: 100%;
                width: 100%;
                margin: 20px auto;
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
                            <div class="panel panel-default mt-4">
                                <div class="panel-heading" style="text-align: center; font-size: 20px; padding: 13px;">Dashboard</div>
                                <div class="panel-body">

                                    <!-- First Pie Chart -->
                                    <div style="font-size: 24px; font-weight: bold; font-family: cursive; background-color: #13acc591; color: #000000; border-radius: 10px; text-align: center; margin: 15px; margin-bottom: 30px; padding: 10px;">
                                        Fund Balance Chart
                                    </div>
                                    <div id="chartContainer" style="margin-bottom: 50px;">
                                        <div id="myDonutChart1"></div>
                                        <div id="totalAmount" style="font-size: 14px; font-weight: bold; color: #000000; text-align: center; margin: 20px;"></div>
                                    </div>

                                    <h2 class="page-title" style="margin-bottom: 50px;"></h2>

                                    <!-- First Bar Chart -->
                                    <div style="font-size: 24px; font-weight: bold; font-family: cursive; background-color: #13acc591; color: #000000; border-radius: 10px; text-align: center; margin: 15px; margin-bottom: 30px; padding: 10px;">
                                        Monthly Contribution Chart
                                    </div>
                                    <div id="chartContainer2" style="margin-bottom: 30px;">
                                        <div id="myBarChart1"></div>
                                        <div id="mContribution" style="font-size: 14px; font-weight: bold; color: #000000; text-align: center; margin: 20px;"></div>
                                    </div>

                                    <h2 class="page-title" style="margin-bottom: 50px;"></h2>

                                    <!-- Second Pie Chart -->
                                    <div style="font-size: 24px; font-weight: bold; font-family: cursive; background-color: #13acc591; color: #000000; border-radius: 10px; text-align: center; margin: 15px; margin-bottom: 30px; padding: 10px;">
                                        Expense Chart
                                    </div>
                                    <div id="chartContainer" style="margin-bottom: 50px;">
                                        <div id="myDonutChart2"></div>
                                        <div id="tExpense" style="font-size: 14px; font-weight: bold; color: #000000; text-align: center; margin: 20px;"></div>
                                    </div>

                                    <h2 class="page-title" style="margin-bottom: 50px;"></h2>

                                    <!-- Second Bar Chart -->
                                    <div style="font-size: 24px; font-weight: bold; font-family: cursive; background-color: #13acc591; color: #000000; border-radius: 10px; text-align: center; margin: 15px; margin-bottom: 30px; padding: 10px;">
                                        Monthly Expense Chart
                                    </div>
                                    <div id="chartContainer2" style="margin-bottom: 30px;">
                                        <div id="myBarChart2"></div>
                                        <div id="mContribution" style="font-size: 14px; font-weight: bold; color: #000000; text-align: center; margin: 20px;"></div>
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

        <script>
            function fetchDataAndUpdateChart1() {
                $.ajax({
                    url: 'fetch_dashboard_data.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const totalAmount = response.totalAmount;
                            const amountAdded = response.amountAdded;
                            const tAmount = totalAmount + amountAdded;
                            document.querySelector('#totalAmount').innerText = `Total Fund Balance: ${tAmount} ৳`;
                            chart1.updateSeries([totalAmount, amountAdded]);
                        } else {
                            console.error('Error fetching data:', response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', error);
                    }
                });
            }

            var options1 = {
                chart: {
                    type: 'pie',
                    width: 300,
                    height: 300,
                },
                series: [0, 0],
                labels: ['Previous Amount', 'Amount Added'],
                colors: ['#189d9c', '#ca9d15'],
                legend: {
                    position: 'bottom',
                    fontSize: '14px',
                    formatter: function(seriesName, opts) {
                        let amount = opts.w.config.series[opts.seriesIndex];
                        let total = opts.w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                        let percentage = ((amount / total) * 100).toFixed(2);
                        return `${seriesName}: ${amount} ৳ (${percentage}%)`;
                    },
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val, opts) {
                        let total = opts.w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                        let percentage = ((opts.w.config.series[opts.seriesIndex] / total) * 100).toFixed(2);
                        return `${percentage}%`;
                    },
                    style: {
                        colors: ['#000000'],
                        fontSize: '13px',
                        fontFamily: 'Arial, sans-serif',
                        fontWeight: 'bold'
                    },
                    dropShadow: {
                        enabled: false
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + " ৳";
                        }
                    }
                }
            };

            var chart1 = new ApexCharts(document.querySelector("#myDonutChart1"), options1);
            chart1.render();

            fetchDataAndUpdateChart1();



            function fetchDataAndUpdateChart2() {
                $.ajax({
                    url: 'fetch_contribution _data.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

                            // Initialize amounts with 0 for each month
                            let monthlyData = Array(12).fill(0);

                            // Populate monthlyData with values from response
                            response.data.forEach(item => {
                                monthlyData[item.month - 1] = item.totalAmount;
                            });

                            // Log data to verify
                            console.log("Months:", monthNames);
                            console.log("Amounts:", monthlyData);

                            // Update the bar chart with fetched data
                            chart2.updateOptions({
                                xaxis: {
                                    categories: monthNames
                                },
                                series: [{
                                    name: 'Total Amount',
                                    data: monthlyData
                                }]
                            });
                        } else {
                            console.error('Error fetching data:', response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', error);
                    }
                });
            }

            // Initialize the bar chart with empty data
            var options2 = {
                chart: {
                    type: 'bar',
                    height: '800px',
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                    }
                },
                series: [{
                    name: 'Total Amount',
                    data: []
                }],
                xaxis: {
                    categories: [],
                    title: {
                        text: 'Amount (৳)'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Months'
                    }
                },
                title: {
                    text: 'Monthly Contribution'
                },
                colors: ['#189d9c'],
                dataLabels: {
                    enabled: true,
                    style: {
                        colors: ['#000000bd'],
                        fontSize: '15px',
                        fontFamily: 'Arial, sans-serif',
                        fontWeight: 'bold'
                    }
                }
            };

            var chart2 = new ApexCharts(document.querySelector("#myBarChart1"), options2);
            chart2.render();

            // Fetch and update the chart data
            fetchDataAndUpdateChart2();



            function fetchDataAndUpdateChart3() {
                $.ajax({
                    url: 'fetch_expense_data.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const monthExpense = response.monthExpense;
                            const netExpense = response.netExpense;
                            const totalExpense = response.totalExpense;
                            document.querySelector('#tExpense').innerText = `Total Amount: ${totalExpense} ৳`;
                            chart3.updateSeries([netExpense, monthExpense]);
                        } else {
                            console.error('Error fetching data:', response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', error);
                    }
                });
            }

            var options3 = {
                chart: {
                    type: 'pie',
                    width: 300,
                    height: 300,
                },
                series: [0, 0],
                labels: ['Past Expenses', 'Current Expenses'],
                colors: ['#189d9c', '#ca9d15'],
                legend: {
                    position: 'bottom',
                    fontSize: '14px',
                    formatter: function(seriesName, opts) {
                        let amount = opts.w.config.series[opts.seriesIndex];
                        let total = opts.w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                        let percentage = ((amount / total) * 100).toFixed(2);
                        return `${seriesName}: ${amount} ৳ (${percentage}%)`;
                    },
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val, opts) {
                        let total = opts.w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                        let percentage = ((opts.w.config.series[opts.seriesIndex] / total) * 100).toFixed(2);
                        return `${percentage}%`;
                    },
                    style: {
                        colors: ['#000000'],
                        fontSize: '13px',
                        fontFamily: 'Arial, sans-serif',
                        fontWeight: 'bold'
                    },
                    dropShadow: {
                        enabled: false
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + " ৳";
                        }
                    }
                }
            };

            var chart3 = new ApexCharts(document.querySelector("#myDonutChart2"), options3);
            chart3.render();

            fetchDataAndUpdateChart3();

            function fetchDataAndUpdateChart4() {
                $.ajax({
                    url: 'fetch_expMonth_data.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

                            // Initialize amounts with 0 for each month
                            let monthlyData = Array(12).fill(0);

                            // Populate monthlyData with values from response
                            response.data.forEach(item => {
                                monthlyData[item.month - 1] = item.totalAmount;
                            });

                            // Log data to verify
                            console.log("Months:", monthNames);
                            console.log("Amounts:", monthlyData);

                            // Update the bar chart with fetched data
                            chart4.updateOptions({
                                xaxis: {
                                    categories: monthNames
                                },
                                series: [{
                                    name: 'Total Amount',
                                    data: monthlyData
                                }]
                            });
                        } else {
                            console.error('Error fetching data:', response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', error);
                    }
                });
            }

            // Initialize the bar chart with empty data
            var options4 = {
                chart: {
                    type: 'bar',
                    height: '800px',
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barweight: '10%',
                    }
                },
                series: [{
                    name: 'Total Amount',
                    data: []
                }],
                xaxis: {
                    categories: [],
                    title: {
                        text: 'Amount (৳)'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Months'
                    }
                },
                title: {
                    text: 'Monthly Expense'
                },
                colors: ['#189d9c'],
                dataLabels: {
                    enabled: true,
                    style: {
                        colors: ['#000000bd'],
                        fontSize: '15px',
                        fontFamily: 'Arial, sans-serif',
                        fontWeight: 'bold'
                    }
                }
            };

            var chart4 = new ApexCharts(document.querySelector("#myBarChart2"), options4);
            chart4.render();

            // Fetch and update the chart data
            fetchDataAndUpdateChart4();
        </script>
    </body>

    </html>
<?php } ?>