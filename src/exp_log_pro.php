<?php
include('includes/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aPurpose = $_POST['aPurpose'];
    $amount = $_POST['amount'];
    $note = !empty($_POST['note']) ? $_POST['note'] : null;

    // Handle file upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "uploads/"; // Make sure this directory exists
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));

        // Generate a unique random filename using uniqid() and the original extension
        $uniqueFileName = uniqid() . '.' . $imageFileType;
        $targetFile = $targetDir . $uniqueFileName;

        // Check if the file is an image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $image = $uniqueFileName; // Store only the unique file name
            } else {
                echo "error: Failed to upload the image.";
                exit();
            }
        } else {
            echo "error: File is not an image.";
            exit();
        }
    }

    // Check if required fields are filled
    if (!empty($aPurpose) && !empty($amount)) {
        try {
            // Begin a transaction
            $dbh->beginTransaction();

            // Prepare SQL statement to insert into the expense table
            $sql = "INSERT INTO expense (aPurpose, amount, note, image) 
                    VALUES (:aPurpose, :amount, :note, :image)";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':aPurpose', $aPurpose, PDO::PARAM_STR);
            $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
            $stmt->bindParam(':note', $note, PDO::PARAM_STR);
            $stmt->bindParam(':image', $image, PDO::PARAM_STR);

            // Execute the statement and check if successful
            if ($stmt->execute()) {
                // Commit the transaction if everything is successful
                $dbh->commit();
                echo "success";
            } else {
                // Rollback if the insertion fails
                $dbh->rollBack();
                echo "error: Failed to insert expense entry.";
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
