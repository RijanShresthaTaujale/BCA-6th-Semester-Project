<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

include '../../backend/db_config.php';

$user_id = $_SESSION['user_id'];

// Get user email
$user_query = "SELECT email FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);
$user_email = $user['email'];

// Get customer_id from email
$customer_query = "SELECT id FROM customers WHERE email = '$user_email'";
$customer_result = mysqli_query($conn, $customer_query);

if (mysqli_num_rows($customer_result) == 0) {
    $orders = [];
} else {
    $customer = mysqli_fetch_assoc($customer_result);
    $customer_id = $customer['id'];

    // Get all orders for this customer
    $orders_query = "SELECT orders.id, orders.cake_id, orders.quantity, orders.total_price, orders.status, orders.order_date, cakes.name 
                     FROM orders 
                     JOIN cakes ON orders.cake_id = cakes.id 
                     WHERE orders.customer_id = '$customer_id'
                     ORDER BY orders.order_date DESC";
    $orders_result = mysqli_query($conn, $orders_query);
    $orders = [];
    while ($row = mysqli_fetch_assoc($orders_result)) {
        $orders[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        table tr:hover {
            background-color: #f5f5f5;
        }
        .status-pending {
            color: #ff9800;
            font-weight: bold;
        }
        .status-completed {
            color: #4caf50;
            font-weight: bold;
        }
        .status-cancelled {
            color: #f44336;
            font-weight: bold;
        }
        .no-orders {
            text-align: center;
            margin-top: 20px;
            font-size: 18px;
            color: #666;
        }
    </style>
</head>
<body>
<header>
    <h1>My Orders</h1>
    <nav>
        <a href="../dashboard.php">Go to Dashboard</a>
        <a href="cake.php">View Cakes</a>
        <a href="cart.php">View Cart</a>
    </nav>
</header>

<main>
    <?php if (count($orders) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Cake Name</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['id']); ?></td>
                        <td><?php echo htmlspecialchars($order['name']); ?></td>
                        <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                        <td>NRS <?php echo htmlspecialchars($order['total_price']); ?></td>
                        <td class="status-<?php echo strtolower($order['status']); ?>"><?php echo htmlspecialchars(ucfirst($order['status'])); ?></td>
                        <td><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-orders">
            <p>You haven't placed any orders yet.</p>
            <p><a href="cake.php">Start ordering now!</a></p>
        </div>
    <?php endif; ?>
</main>

<footer>
    &copy; <?php echo date('Y'); ?> Cakeria. All rights reserved.
</footer>
</body>
</html>
