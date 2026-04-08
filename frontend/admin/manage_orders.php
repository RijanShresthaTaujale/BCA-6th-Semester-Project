<?php
session_start();

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../auth/admin_login.php");
    exit();
}

include '../../backend/db_config.php';
include '../../backend/algorithms.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'order_date_desc';

// Fetch all orders from database
$order_query = "SELECT orders.*, customers.name AS customer_name, cakes.name AS cake_name 
                FROM orders
                JOIN customers ON orders.customer_id = customers.id
                JOIN cakes ON orders.cake_id = cakes.id";

$result = mysqli_query($conn, $order_query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Convert result to array
$orders = resultToArray($result);

// ========== KMP PATTERN MATCHING SEARCH ==========
if (!empty($search)) {
    // Search using KMP Algorithm in multiple fields
    $filtered_orders = [];
    foreach ($orders as $order) {
        $matches_name = kmpSearch(strtolower($order['customer_name']), strtolower($search), false);
        $matches_cake = kmpSearch(strtolower($order['cake_name']), strtolower($search), false);
        $matches_status = kmpSearch(strtolower($order['status']), strtolower($search), false);
        
        if (count($matches_name) > 0 || count($matches_cake) > 0 || count($matches_status) > 0) {
            $filtered_orders[] = $order;
        }
    }
    $orders = $filtered_orders;
}

// ========== SORTING ALGORITHMS ==========
// Determine sort field and order
$sort_field = 'order_date';
$sort_order = 'desc';

switch ($sort) {
    case 'order_date_asc':
        $sort_field = 'order_date';
        $sort_order = 'asc';
        break;
    case 'customer_name_asc':
        $sort_field = 'customer_name';
        $sort_order = 'asc';
        break;
    case 'customer_name_desc':
        $sort_field = 'customer_name';
        $sort_order = 'desc';
        break;
    case 'cake_name_asc':
        $sort_field = 'cake_name';
        $sort_order = 'asc';
        break;
    case 'cake_name_desc':
        $sort_field = 'cake_name';
        $sort_order = 'desc';
        break;
    case 'status_asc':
        $sort_field = 'status';
        $sort_order = 'asc';
        break;
    case 'status_desc':
        $sort_field = 'status';
        $sort_order = 'desc';
        break;
    default:
        $sort_field = 'order_date';
        $sort_order = 'desc';
        break;
}

// Sort orders using Merge Sort
if (count($orders) > 0) {
    mergeSort($orders, 0, count($orders) - 1, $sort_field, $sort_order);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateOrderStatus(orderId, status) {
            $.ajax({
                url: 'update_order_status.php',
                type: 'POST',
                data: {
                    order_id: orderId,
                    status: status
                },
                success: function(response) {
                    $('#status-' + orderId).text(status);
                    $('#message').html('<p style="color: green;">Order status updated successfully</p>');
                },
                error: function() {
                    $('#message').html('<p style="color: red;">Failed to update order status</p>');
                }
            });
        }

        function deleteOrder(orderId) {
            $.ajax({
                url: 'delete_order.php',
                type: 'POST',
                data: {
                    order_id: orderId
                },
                success: function(response) {
                    if (response == 'success') {
                        $('#order-' + orderId).remove();
                        $('#message').html('<p style="color: green;">Order deleted successfully</p>');
                    } else {
                        $('#message').html('<p style="color: red;">Failed to delete order</p>');
                    }
                },
                error: function() {
                    $('#message').html('<p style="color: red;">Failed to delete order</p>');
                }
            });
        }
    </script>
</head>
<body>
<header>
    <h1>Manage Orders</h1>
    <nav>
        <a href="../admin/admin_dashboard.php">Go to Dashboard</a>
        <a href="../admin/admin_login.php">Logout</a>
    </nav>
</header>
<main>
    <div id="message"></div>

    <form action="manage_orders.php" method="GET">
        <label for="sort">Sort Orders By:</label>
        <select name="sort" id="sort">
            <option value="order_date_desc" <?php if ($sort == 'order_date_desc') echo 'selected'; ?>>Order Date (Newest First)</option>
            <option value="order_date_asc" <?php if ($sort == 'order_date_asc') echo 'selected'; ?>>Order Date (Oldest First)</option>
            <option value="customer_name_asc" <?php if ($sort == 'customer_name_asc') echo 'selected'; ?>>Customer Name (A-Z)</option>
            <option value="customer_name_desc" <?php if ($sort == 'customer_name_desc') echo 'selected'; ?>>Customer Name (Z-A)</option>
            <option value="cake_name_asc" <?php if ($sort == 'cake_name_asc') echo 'selected'; ?>>Cake Name (A-Z)</option>
            <option value="cake_name_desc" <?php if ($sort == 'cake_name_desc') echo 'selected'; ?>>Cake Name (Z-A)</option>
            <option value="status_asc" <?php if ($sort == 'status_asc') echo 'selected'; ?>>Status (A-Z)</option>
            <option value="status_desc" <?php if ($sort == 'status_desc') echo 'selected'; ?>>Status (Z-A)</option>
        </select>

        <label for="search">Search Orders:</label>
        <input type="text" name="search" id="search" placeholder="Search by customer, cake, or status" value="<?php echo htmlspecialchars($search); ?>">
        
        <button type="submit">Sort & Search</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Cake</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Order Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $row): ?>
                <tr id="order-<?php echo $row['id']; ?>">
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['cake_name']); ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td><?php echo $row['total_price']; ?></td>
                    <td id="status-<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo $row['order_date']; ?></td>
                    <td>
                        <select onchange="updateOrderStatus(<?php echo $row['id']; ?>, this.value)">
                            <option value="pending" <?php if ($row['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                            <option value="completed" <?php if ($row['status'] == 'completed') echo 'selected'; ?>>Completed</option>
                            <option value="cancelled" <?php if ($row['status'] == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                        </select>
                        <button onclick="deleteOrder(<?php echo $row['id']; ?>)">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>
<footer>
    &copy; <?php echo date('Y'); ?> Cakeria. All rights reserved.
</footer>
</body>
</html>

<?php

mysqli_close($conn);
?>