<?php

include '../../backend/db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cake_id = mysqli_real_escape_string($conn, $_POST['cake_id']);
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["cake_image"]["name"]);
    $upload_ok = 1;
    $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["cake_image"]["tmp_name"]);
    if ($check !== false) {
        $upload_ok = 1;
    } else {
        echo "File is not an image.";
        $upload_ok = 0;
    }

    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $upload_ok = 0;
    }

    if ($_FILES["cake_image"]["size"] > 500000) { 
        echo "Sorry, your file is too large.";
        $upload_ok = 0;
    }

    if ($image_file_type != "jpg" && $image_file_type != "png" && $image_file_type != "jpeg" && $image_file_type != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $upload_ok = 0;
    }

    if ($upload_ok == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["cake_image"]["tmp_name"], $target_file)) {
            $sql = "UPDATE cakes SET image='$target_file' WHERE id='$cake_id'";
            if (mysqli_query($conn, $sql)) {
                echo "The file ". htmlspecialchars(basename($_FILES["cake_image"]["name"])). " has been uploaded and the database updated.";
            } else {
                echo "Error updating record: " . mysqli_error($conn);
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}


mysqli_close($conn);
?>