<?php
session_start();
include('includes/config.php');

// Check if the user is logged in
if (strlen($_SESSION['alogin']) == 0) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Check if the POST request contains the expense ID
if (isset($_POST['id'])) {
    $expenseId = intval($_POST['id']);
    
    // Prepare the SQL query to fetch the expense record
    $sqlFetch = "SELECT image FROM expense WHERE ep_id = :expenseId";
    $stmtFetch = $dbh->prepare($sqlFetch);
    $stmtFetch->bindParam(':expenseId', $expenseId, PDO::PARAM_INT);
    $stmtFetch->execute();
    $expense = $stmtFetch->fetch(PDO::FETCH_ASSOC);
    
    if ($expense) {
        // Delete the associated image file if it exists
        $imagePath = 'uploads/' . $expense['image'];
        if (!empty($expense['image']) && file_exists($imagePath)) {
            unlink($imagePath); // Delete the file
        }

        // Prepare the SQL query to delete the expense
        $sqlDelete = "DELETE FROM expense WHERE ep_id = :expenseId";
        $stmtDelete = $dbh->prepare($sqlDelete);
        $stmtDelete->bindParam(':expenseId', $expenseId, PDO::PARAM_INT);

        if ($stmtDelete->execute()) {
            // Successfully deleted
            echo json_encode(['success' => true]);
        } else {
            // Failed to delete
            echo json_encode(['success' => false, 'message' => 'Failed to delete the expense']);
        }
    } else {
        // Expense record not found
        echo json_encode(['success' => false, 'message' => 'Expense not found']);
    }
} else {
    // No expense ID provided
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
