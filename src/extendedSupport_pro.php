<?php
include('includes/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = !empty($_POST['phone']) ? $_POST['phone'] : null;
    $amount = $_POST['amount'];
    $note = !empty($_POST['note']) ? $_POST['note'] : null;
    $pr_id = null; // Explicitly setting pr_id to null
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

    // Check if required fields are filled
    if (!empty($name) && !empty($amount)) {
        try {
            // Begin a transaction
            $dbh->beginTransaction();

            // Prepare SQL statement for inserting into extra_support
            $sql = "INSERT INTO extra_support (ex_name, phone, note) VALUES (:name, :phone, :note)";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':note', $note, PDO::PARAM_STR);

            // Execute the statement and check if successful
            if ($stmt->execute()) {
                // Retrieve the last inserted ex_id
                $ex_id = $dbh->lastInsertId();

                // Prepare SQL statement for inserting into payment
                $sqlPayment = "INSERT INTO payment (pr_id, ex_id, receipt_no, amount) VALUES (:pr_id, :ex_id, :receipt_no, :amount)";
                $stmtPayment = $dbh->prepare($sqlPayment);
                $stmtPayment->bindParam(':pr_id', $pr_id, PDO::PARAM_NULL); // Ensure pr_id is null
                $stmtPayment->bindParam(':ex_id', $ex_id, PDO::PARAM_INT);
                $stmtPayment->bindParam(':receipt_no', $receiptNo, PDO::PARAM_INT); // Correct variable name
                $stmtPayment->bindParam(':amount', $amount, PDO::PARAM_INT); // Correct binding for amount

                // Execute the statement for payment
                if ($stmtPayment->execute()) {
                    // Commit the transaction if everything is successful
                    $dbh->commit();
                    echo "success";
                } else {
                    // Rollback if the payment insertion fails
                    $dbh->rollBack();
                    echo "error in payment insertion";
                }
            } else {
                // Rollback if the extra_support insertion fails
                $dbh->rollBack();
                echo "error in extra_support insertion";
            }
        } catch (Exception $e) {
            // Rollback if any exception occurs
            $dbh->rollBack();
            echo "error: " . $e->getMessage();
        }
    } else {
        echo "warning"; // If required fields are not filled
    }
    exit(); // Ensure no further code executes after returning the response
}
