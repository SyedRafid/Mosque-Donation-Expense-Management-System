<?php
// Include configuration or database connection file
include('includes/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $nid = $_POST['nid'];
    $fName = $_POST['fName'];
    $phone = $_POST['phone'];
    $salary = $_POST['salary'];

    // Check if all required fields are filled
    if (!empty($name) && !empty($nid) && !empty($fName) && !empty($phone) && !empty($salary)) {
        // Prepare SQL statement to prevent SQL injection
        $sql = "UPDATE profile SET name=:name, nid=:nid, fName=:fName, phone=:phone, salary=:salary WHERE pr_id=:id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':nid', $nid, PDO::PARAM_STR);
        $stmt->bindParam(':fName', $fName, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindParam(':salary', $salary, PDO::PARAM_INT);

        // Execute and check if the update was successful
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "warning"; // For missing required fields
    }
    exit(); // Ensure no further code executes after returning the response
} else {
    // Handle cases where the request method is not POST
    echo "Invalid request method!";
    exit();
}
?>
