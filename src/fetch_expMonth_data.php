<?php
include('includes/config.php');

$currentYear = date('Y');
$currentMonth = date('m');

$response = array('success' => false, 'data' => []);

try {
    $sql = "SELECT 
                MONTH(creation_date) AS month,
                SUM(amount) AS totalAmount
            FROM expense
            WHERE YEAR(creation_date) = :currentYear
              AND MONTH(creation_date) <= :currentMonth
            GROUP BY MONTH(creation_date)
            ORDER BY MONTH(creation_date)";

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
