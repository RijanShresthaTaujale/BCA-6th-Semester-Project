<?php
session_start();

include '../../backend/db_config.php';
include '../../backend/algorithms.php';

if (!isset($_SESSION['user_id'])) {
    header('Location:../auth/login.php');
    exit();
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $cake_id = isset($_GET['id']) ? $_GET['id'] : null;

    if ($action == 'add' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $cake_id = $_POST['id'];
        $quantity = intval($_POST['quantity']);
        $user_id = $_SESSION['user_id'];

        // Validate quantity: minimum 1, maximum 100
        if ($quantity < 1 || $quantity > 100) {
            echo "Error: Quantity must be between 1 and 100";
        } else {
            error_log("Form Data: Cake ID=$cake_id, Quantity=$quantity, User ID=$user_id");

            $sql = "INSERT INTO cart (user_id, cake_id, quantity) VALUES ('$user_id', '$cake_id', '$quantity')";

            if (mysqli_query($conn, $sql)) {
                echo "Added to cart successfully!";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }
    } elseif ($action == 'remove' && $cake_id) {
        $sql = "DELETE FROM cart WHERE id = '$cake_id'";
        if (!mysqli_query($conn, $sql)) {
            die("Error: " . $sql . "<br>" . mysqli_error($conn));
        }
    } elseif ($action == 'clear') {
        $sql = "DELETE FROM cart";
        if (!mysqli_query($conn, $sql)) {
            die("Error: " . $sql . "<br>" . mysqli_error($conn));
        }
    }
}

$query = "
    SELECT cart.id, cakes.name AS cake_name, cakes.price AS cake_price, cart.quantity
    FROM cart
    JOIN cakes ON cart.cake_id = cakes.id
    WHERE cart.user_id = '{$_SESSION['user_id']}'
";
$cart_result = mysqli_query($conn, $query);

if (!$cart_result) {
    die("Query failed: " . mysqli_error($conn));
}

// Convert result to array
$cart_items = resultToArray($cart_result);

// ========== SORTING ALGORITHM ==========
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';

$sort_field = 'cake_name';
$sort_order = 'asc';

switch ($sort) {
    case 'name_asc':
        $sort_field = 'cake_name';
        $sort_order = 'asc';
        break;
    case 'name_desc':
        $sort_field = 'cake_name';
        $sort_order = 'desc';
        break;
    case 'price_asc':
        $sort_field = 'cake_price';
        $sort_order = 'asc';
        break;
    case 'price_desc':
        $sort_field = 'cake_price';
        $sort_order = 'desc';
        break;
    default:
        $sort_field = 'cake_name';
        $sort_order = 'asc';
        break;
}

// Sort cart items using Merge Sort
if (count($cart_items) > 0) {
    mergeSort($cart_items, 0, count($cart_items) - 1, $sort_field, $sort_order);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<header>
    <h1>Your Cart</h1>
    <nav>
        <a href="../dashboard.php">Go to Dashboard</a>
        <a href="cake.php">Cakes</a>
        <a href="view_orders.php">My Orders</a>
    </nav>
</header>
<main>
    <table>
        <tr>
            <th>Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Action</th>
        </tr>

        <tr>
            <td colspan="5">
                <form action="cart.php" method="GET" style="margin-bottom: 10px;">
                    <label for="sort">Sort By:</label>
                    <select name="sort" id="sort">
                        <option value="name_asc" <?php if ($sort == 'name_asc') echo 'selected'; ?>>Name (A-Z)</option>
                        <option value="name_desc" <?php if ($sort == 'name_desc') echo 'selected'; ?>>Name (Z-A)</option>
                        <option value="price_asc" <?php if ($sort == 'price_asc') echo 'selected'; ?>>Price (Low to High)</option>
                        <option value="price_desc" <?php if ($sort == 'price_desc') echo 'selected'; ?>>Price (High to Low)</option>
                    </select>

                    
                    <button type="submit">Sort</button>
                </form>
            </td>
        </tr>

        <?php
        $total = 0;
        foreach ($cart_items as $row):
            $total += $row['cake_price'] * $row['quantity'];
        ?>
        <tr>
            <td><?php echo $row['cake_name']; ?></td>
            <td><?php echo $row['cake_price']; ?></td>
            <td><?php echo $row['quantity']; ?></td>
            <td><?php echo $row['cake_price'] * $row['quantity']; ?></td>
            <td>
                <a href="cart.php?action=remove&id=<?php echo $row['id']; ?>">Remove</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3">Total</td>
            <td><?php echo $total; ?></td>
            <td>
                <a href="cart.php?action=clear">Clear Cart</a>
            </td>
        </tr>
    </table>
    <br>
    <?php if ($total > 0): ?>
        <a href="order.php">Place Order</a>
    <?php endif; ?>
</main>

<footer>
    &copy; <?php echo date('Y'); ?> Cakeria. All rights reserved.
</footer>
</body>
</html>