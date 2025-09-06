<?php
include('includes/config.php');
$currentMonthYear = date('Y-m'); // Format for the SQL query (YYYY-MM)

$response = array('success' => false);

try {
    $sql = "SELECT 
                SUM(CASE WHEN DATE_FORMAT(c_date, '%Y-%m') = :currentMonthYear THEN amount ELSE 0 END) AS amountAdded, 
                SUM(amount) AS totalAmount1 
            FROM payment";

    $query = $dbh->prepare($sql);
    $query->bindParam(':currentMonthYear', $currentMonthYear);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    $sql2 = "SELECT SUM(amount) AS totalExpense FROM expense";

    $query2 = $dbh->prepare($sql2);
    $query2->execute();
    $result2 = $query2->fetch(PDO::FETCH_ASSOC);

    $netAdded = (int)$result['totalAmount1'] - (int)$result['amountAdded'];
    $totalAmount = $netAdded - (int)$result2['totalExpense'];
    
    if ($result) {
        $response['success'] = true;
        $response['amountAdded'] = (int)$result['amountAdded'];
        $response['totalAmount'] = $totalAmount;
    }
} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
}

// Return JSON response
echo json_encode($response);
