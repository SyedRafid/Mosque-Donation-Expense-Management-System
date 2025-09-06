<?php
include('includes/config.php'); // Ensure config uses PDO

if (!empty($_POST["phone"])) {
    $phone = $_POST["phone"]; // Use the correct variable for phone

    // Prepare and execute the query using PDO
    $stmt = $dbh->prepare("SELECT * FROM profile WHERE phone = :phone");
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
    $stmt->execute();

    $count = $stmt->rowCount(); // Check how many rows were returned
    if ($count > 0) {
        // Phone number already exists
        echo "<span style='color:red'> Phone number already exists.</span>";
        echo "<script>$('#submit').prop('disabled', true);</script>";
    } else {
        // Phone number is available
        echo "<span style='color:green'> Phone number available for registration.</span>";
        echo "<script>$('#submit').prop('disabled', false);</script>";
    }
}
?>
