<?php
include('includes/config.php');

$currentYear = date('Y'); 
$currentMonth = date('m');

$response = array('success' => false, 'data' => []);

try {
    $sql = "SELECT 
                MONTH(c_date) AS month,
                SUM(amount) AS totalAmount
            FROM payment
            WHERE YEAR(c_date) = :currentYear
              AND MONTH(c_date) <= :currentMonth
            GROUP BY MONTH(c_date)
            ORDER BY MONTH(c_date)";

    $query = $dbh->prepare($sql);
    $query->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
    $query->bindParam(':currentMonth', $currentMonth, PDO::PARAM_INT);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        $response['success'] = true;
        $response['data'] = $results;
    }
} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);
