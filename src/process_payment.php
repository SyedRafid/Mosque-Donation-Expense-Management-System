<?php
session_start(); // Start session to manage user login state
include('includes/config.php');

// Check if the request is made via POST and necessary data is provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['profileId'], $_POST['months'], $_POST['year'], $_POST['salaries'])) {
    $profileId = intval($_POST['profileId']); // Ensure profileId is an integer
    $selectedMonths = $_POST['months']; // Array of selected months
    $year = intval($_POST['year']); // Get the selected year
    $salaryData = $_POST['salaries']; // Array of salary amounts for each selected month
    $receiptNo = 1; // Default to 1 if no records exist

    // Query to fetch the largest receipt_no
    $sql = "SELECT MAX(receipt_no) AS max_receipt_no FROM payment";
    $query = $dbh->prepare($sql);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    // Check if a result is found and increment the value
    if ($result && !empty($result['max_receipt_no'])) {
        $receiptNo = intval($result['max_receipt_no']) + 1;
    }

    // Fetch the contribution start month and year from the profile
    $conSql = "SELECT MONTH(con_date) AS conMonth, YEAR(con_date) AS conYear FROM profile WHERE pr_id = :profileId";
    $conQuery = $dbh->prepare($conSql);
    $conQuery->bindParam(':profileId', $profileId, PDO::PARAM_INT);
    $conQuery->execute();
    $profileData = $conQuery->fetch(PDO::FETCH_ASSOC);

    $conMonth = $profileData['conMonth'];
    $conYear = $profileData['conYear'];

    // Prepare the SQL statement to check for existing payments
    $checkSql = "SELECT COUNT(*) FROM payment WHERE pr_id = :profileId AND YEAR(date) = :year AND MONTH(date) = :month";
    $checkQuery = $dbh->prepare($checkSql);

    // Prepare the SQL statement for inserting payments
    $insertSql = "
        INSERT INTO payment (pr_id, receipt_no, amount, date )
        VALUES (:profileId, :receiptNo, :salary, :paymentDate )
    ";
    $insertQuery = $dbh->prepare($insertSql);

    $success = true; // Initialize success status

    foreach ($selectedMonths as $index => $month) {
        // Format the date as YYYY-MM-01 using the selected year
        $paymentDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';

        // Check if the selected year is the contribution year
        if ($year > $conYear || ($year == $conYear && $month >= $conMonth)) {
            // Check if payment for the current month already exists
            $checkQuery->bindParam(':profileId', $profileId, PDO::PARAM_INT);
            $checkQuery->bindParam(':year', $year, PDO::PARAM_INT);
            $checkQuery->bindParam(':month', $month, PDO::PARAM_INT);
            $checkQuery->execute();

            // Fetch the count of existing payments
            if ($checkQuery->fetchColumn() == 0) {
                // Only insert if no existing payment for that month
                $insertQuery->bindParam(':profileId', $profileId, PDO::PARAM_INT);
                $insertQuery->bindParam(':receiptNo', $receiptNo, PDO::PARAM_INT);
                $insertQuery->bindParam(':salary', $salaryData[$index], PDO::PARAM_INT); 
                $insertQuery->bindParam(':paymentDate', $paymentDate, PDO::PARAM_STR);

                if (!$insertQuery->execute()) {
                    // If the query fails, set success to false and capture the error message
                    $success = false;
                    break; // Exit the loop if there's an error
                }
            }
        }
    }

    // Return response based on success status
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error inserting payment']);
    }
} else {
    // If the required parameters are missing or if the request method is incorrect
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
