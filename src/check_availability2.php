<?php
include('includes/config.php'); // Ensure config uses PDO

if (!empty($_POST["phone"]) && !empty($_POST["qid"])) {
    $phone = $_POST["phone"];
    $qid = $_POST["qid"]; // Get the qid (pr_id)

    // Prepare and execute the query using PDO
    $stmt = $dbh->prepare("SELECT phone FROM profile WHERE phone = :phone AND pr_id != :qid");
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
    $stmt->bindParam(':qid', $qid, PDO::PARAM_INT); // Exclude the current profile ID
    $stmt->execute();

    $count = $stmt->rowCount(); // Check how many rows were returned
    if ($count > 0) {
        // Phone number already exists for another profile
        echo "<span style='color:red'> Phone number already exists for another profile.</span>";
        echo "<script>$('#submit').prop('disabled', true);</script>";
    } else {
        // Phone number is available
        echo "<span style='color:green'> Phone number available for update.</span>";
        echo "<script>$('#submit').prop('disabled', false);</script>";
    }
}
?>
