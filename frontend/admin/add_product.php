<?php

session_start();
include '../../backend/db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $target_dir = '../../frontend/images/';
    $upload_ok = 1;

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    if (!empty($_FILES["image"]["name"])) {
        $image_name = basename($_FILES["image"]["name"]);
        $image_extension = pathinfo($image_name, PATHINFO_EXTENSION);
        $unique_filename = uniqid() . '.' . $image_extension;
        $target_file = $target_dir . $unique_filename;
        $image_file_type = strtolower($image_extension);

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            $upload_ok = 1;
        } else {
            echo "File is not an image.";
            $upload_ok = 0;
        }

        if ($_FILES["image"]["size"] > 500000) { 
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
            if (file_exists($target_file)) {
                unlink($target_file);
            }

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $relative_file_path = 'images/' . $unique_filename;

                $sql = "INSERT INTO cakes (name, description, price, image) VALUES ('$name', '$description', '$price', '$relative_file_path')";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['message'] = "Product added successfully!";
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
                    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        echo "No image file was uploaded.";
    }
}

mysqli_close($conn);
?>