<?php

include '../../backend/db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);

    $sql = "DELETE FROM orders WHERE id='$order_id'";
    if (mysqli_query($conn, $sql)) {
        echo "success";
    } else {
        echo "error: " . mysqli_error($conn);
    }
    exit();
}


mysqli_close($conn);
?>