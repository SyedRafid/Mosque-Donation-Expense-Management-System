<?php
include('includes/config.php');
$currentMonthYear = date('Y-m');

$response = array('success' => false);

try {
    $sql = "SELECT 
                SUM(CASE WHEN DATE_FORMAT(creation_date, '%Y-%m') = :currentMonthYear THEN amount ELSE 0 END) AS monthExpense, 
                SUM(amount) AS totalExpense
            FROM expense";

    $query = $dbh->prepare($sql);
    $query->bindParam(':currentMonthYear', $currentMonthYear);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    $netExpense = (int)$result['totalExpense'] - (int)$result['monthExpense'];
    
    if ($result) {
        $response['success'] = true;
        $response['monthExpense'] = (int)$result['monthExpense'];
        $response['totalExpense'] = (int)$result['totalExpense'];
        $response['netExpense'] = $netExpense;
    }
} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
}

// Return JSON response
echo json_encode($response);
