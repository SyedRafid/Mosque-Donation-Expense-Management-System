<?php
include('includes/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $nid = $_POST['nid'];
    $fName = $_POST['fName'];
    $phone = $_POST['phone'];
    $salary = $_POST['salary'];
    $monthYear = $_POST['monthYear'];

    // Check if all required fields are filled
    if (!empty($name) && !empty($nid) && !empty($fName) && !empty($phone) && !empty($salary) && !empty($monthYear)) {
        try {
            // Convert "May 2025" to "2025-05-01" (Adding the first day of the month)
            $date = DateTime::createFromFormat('F Y', $monthYear);
            if ($date === false) {
                throw new Exception("Invalid date format");
            }
            $formattedDate = $date->format('Y-m-01'); // First day of the selected month

            // Begin transaction
            $dbh->beginTransaction();

            // Prepare SQL statement to insert into profile table
            $sql = "INSERT INTO profile (name, nid, fName, phone, salary, con_date) 
                    VALUES (:name, :nid, :fName, :phone, :salary, :formattedDate)";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':nid', $nid, PDO::PARAM_STR);
            $stmt->bindParam(':fName', $fName, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':salary', $salary, PDO::PARAM_STR);
            $stmt->bindParam(':formattedDate', $formattedDate, PDO::PARAM_STR);

            // Execute the profile insertion
            if ($stmt->execute()) {
                // Commit the transaction if everything is successful
                $dbh->commit();
                echo "success";
            } else {
                // Rollback if profile insertion fails
                $dbh->rollBack();
                echo "error";
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
?>
