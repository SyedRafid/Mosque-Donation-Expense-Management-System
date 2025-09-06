<?php
include_once('include/config.php');
if(isset($_POST['name']))
{
    $name = $_POST['name'];
  
    $stmt = $con->prepare("INSERT INTO applica (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    
    if($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    $stmt->close();
}
?>
