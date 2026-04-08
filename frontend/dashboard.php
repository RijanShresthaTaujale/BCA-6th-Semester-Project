<?php
session_start();

// check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<header>
    <h1>Welcome to the Dashboard<?php if ($is_logged_in) echo ', ' . htmlspecialchars($_SESSION['username']); ?>!</h1>
    <nav>
        <?php if ($is_logged_in): ?>
            <a href="auth/logout.php">Logout</a>
        <?php else: ?>
            <a href="auth/login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <p>This is your dashboard where you can manage your account and view your orders.</p>
    <ul>
        <li><a href="cakes/cake.php">View Cakes</a></li>
        <li><a href="cakes/cart.php">View Cart</a></li>
        <?php if ($is_logged_in): ?>
            <li><a href="cakes/view_orders.php">My Orders</a></li>
        <?php endif; ?>
    </ul>
</main>

<footer>
    &copy; <?php echo date('Y'); ?> Cakeria. All rights reserved.
</footer>
</body>
</html>