<?php
session_start();

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../auth/admin_login.php");
    exit();
}

include '../../backend/db_config.php';
include '../../backend/algorithms.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';

// Fetch all products from database
$product_query = "SELECT * FROM cakes";
$product_result = mysqli_query($conn, $product_query);

// Convert result to array
$products = resultToArray($product_result);

// ========== KMP PATTERN MATCHING SEARCH ==========
if (!empty($search)) {
    $filtered_products = [];
    foreach ($products as $product) {
        $matches_name = kmpSearch(strtolower($product['name']), strtolower($search), false);
        $matches_desc = kmpSearch(strtolower($product['description']), strtolower($search), false);
        
        if (count($matches_name) > 0 || count($matches_desc) > 0) {
            $filtered_products[] = $product;
        }
    }
    $products = $filtered_products;
}

// ========== SORTING ALGORITHMS ==========
$sort_field = 'name';
$sort_order = 'asc';

switch ($sort) {
    case 'name_asc':
        $sort_field = 'name';
        $sort_order = 'asc';
        break;
    case 'name_desc':
        $sort_field = 'name';
        $sort_order = 'desc';
        break;
    case 'price_asc':
        $sort_field = 'price';
        $sort_order = 'asc';
        break;
    case 'price_desc':
        $sort_field = 'price';
        $sort_order = 'desc';
        break;
    case 'id_asc':
        $sort_field = 'id';
        $sort_order = 'asc';
        break;
    case 'id_desc':
        $sort_field = 'id';
        $sort_order = 'desc';
        break;
    default:
        $sort_field = 'name';
        $sort_order = 'asc';
        break;
}

// Sort products using Merge Sort
if (count($products) > 0) {
    mergeSort($products, 0, count($products) - 1, $sort_field, $sort_order);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<header>
    <h1>Admin Dashboard</h1>
    <nav>
        <a href="change_admin_credentials.php">Change Admin Credentials</a>
        <a href="admin_logout.php">Logout</a>
        <a href="manage_orders.php">Manage Orders</a>
    </nav>
</header>

<main>
    <?php if (isset($_SESSION['message'])): ?>
        <div style="padding: 15px; margin-bottom: 20px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px;">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>
    
    <h2>Manage Products</h2>

    <form action="admin_dashboard.php" method="GET">
        <label for="search">Search Products:</label>
        <input type="text" name="search" id="search" placeholder="Enter cake name or description" value="<?php echo htmlspecialchars($search); ?>">
        
        <label for="sort">Sort By:</label>
        <select name="sort" id="sort">
            <option value="name_asc" <?php if ($sort == 'name_asc') echo 'selected'; ?>>Name (A-Z)</option>
            <option value="name_desc" <?php if ($sort == 'name_desc') echo 'selected'; ?>>Name (Z-A)</option>
            <option value="price_asc" <?php if ($sort == 'price_asc') echo 'selected'; ?>>Price (Low to High)</option>
            <option value="price_desc" <?php if ($sort == 'price_desc') echo 'selected'; ?>>Price (High to Low)</option>
            <option value="id_asc" <?php if ($sort == 'id_asc') echo 'selected'; ?>>ID (Oldest First)</option>
            <option value="id_desc" <?php if ($sort == 'id_desc') echo 'selected'; ?>>ID (Newest First)</option>
        </select>

        
        <button type="submit">Search & Sort</button>
    </form>

    <form action="add_product.php" method="POST" enctype="multipart/form-data">
        <h3>Add New Product</h3>
        <label for="name">Product Name:</label>
        <input type="text" name="name" id="name" required>

        <label for="description">Description:</label>
        <textarea name="description" id="description" required></textarea>

        <label for="price">Price (NRS):</label>
        <input type="number" step="0.01" name="price" id="price" required>

        <label for="image">Image:</label>
        <input type="file" name="image" id="image" required>

        <button type="submit">Add Product</button>
    </form>

    <h3>Existing Products</h3>
    <div class="cake-container">
        <?php foreach ($products as $row): ?>
            <div class="cake-item">
                <img src="../<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                <h3><?php echo $row['name']; ?></h3>
                <p><?php echo $row['description']; ?></p>
                <p><strong>Price: NRS <?php echo number_format($row['price'], 2); ?></strong></p>

                <form action="update_product.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <label for="name">Product Name:</label>
                    <input type="text" name="name" value="<?php echo $row['name']; ?>" required>

                    <label for="description">Description:</label>
                    <textarea name="description" required><?php echo $row['description']; ?></textarea>

                    <label for="price">Price (NRS):</label>
                    <input type="number" step="0.01" name="price" value="<?php echo $row['price']; ?>" required>

                    <label for="image">Image:</label>
                    <input type="file" name="image" id="image">

                    <button type="submit">Update Product</button>
                </form>

                <form action="delete_product.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit">Delete Product</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<footer>
    &copy; <?php echo date('Y'); ?> Cakeria. All rights reserved.
</footer>
</body>
</html>

<?php

mysqli_close($conn);
?>