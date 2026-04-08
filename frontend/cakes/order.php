<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}


include '../../backend/db_config.php';

$user_id = $_SESSION['user_id'];
$total_price = 0;

$user_query = "SELECT username, email FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);

if (mysqli_num_rows($user_result) == 0) {
    die("User not found.");
}

$user = mysqli_fetch_assoc($user_result);
$user_email = $user['email'];
$user_name = $user['username'];

$customer_query = "SELECT id FROM customers WHERE email = '$user_email'";
$customer_result = mysqli_query($conn, $customer_query);

if (mysqli_num_rows($customer_result) == 0) {
    $insert_customer_query = "INSERT INTO customers (name, email) VALUES ('$user_name', '$user_email')";
    if (!mysqli_query($conn, $insert_customer_query)) {
        die("Error creating customer: " . mysqli_error($conn));
    }
    $customer_id = mysqli_insert_id($conn);
} else {
    $customer = mysqli_fetch_assoc($customer_result);
    $customer_id = $customer['id'];
}

$cart_query = "SELECT cakes.id, cakes.name, cakes.price, cart.quantity 
               FROM cart JOIN cakes ON cart.cake_id = cakes.id 
               WHERE cart.user_id = '$user_id'";
$cart_result = mysqli_query($conn, $cart_query);

while ($row = mysqli_fetch_assoc($cart_result)) {
    $cake_id = $row['id'];
    $quantity = $row['quantity'];
    $price = $row['price'];
    $total_price += $price * $quantity;

    $order_query = "INSERT INTO orders (customer_id, cake_id, quantity, total_price) 
                    VALUES ('$customer_id', '$cake_id', '$quantity', '$total_price')";

    if (!mysqli_query($conn, $order_query)) {
        die("Error placing order: " . mysqli_error($conn));
    }
}

$clear_query = "DELETE FROM cart WHERE user_id = '$user_id'";
mysqli_query($conn, $clear_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<header>
    <h1>Order Confirmation</h1>
    <nav>
        <a href="../dashboard.php">Go to Dashboard</a>
    </nav>
</header>

<main>
    <p>Thank you for your order! Your order has been placed successfully.</p>
</main>

<footer>
    &copy; <?php echo date('Y'); ?> Cakeria. All rights reserved.
</footer>
</body>
</html>