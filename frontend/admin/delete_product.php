<?php
session_start();

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../auth/admin_login.php");
    exit();
}

include '../../backend/db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);

    $query = "DELETE FROM cakes WHERE id='$id'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = "Product deleted successfully!";
    } else {
        $_SESSION['message'] = "Error: " . mysqli_error($conn);
    }

    header("Location: admin_dashboard.php");
    exit();
}
?>