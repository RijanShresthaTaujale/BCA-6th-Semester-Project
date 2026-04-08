<?php
session_start();
include '../../backend/db_config.php';
include '../../backend/algorithms.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';

// Fetch all cakes from database
$query = "SELECT * FROM cakes";
$result = mysqli_query($conn, $query);

// Convert result to array
$cakes = resultToArray($result);

// ========== KMP PATTERN MATCHING SEARCH ==========
if (!empty($search)) {
    $filtered_cakes = [];
    foreach ($cakes as $cake) {
        $matches_name = kmpSearch(strtolower($cake['name']), strtolower($search), false);
        $matches_desc = kmpSearch(strtolower($cake['description']), strtolower($search), false);
        
        if (count($matches_name) > 0 || count($matches_desc) > 0) {
            $filtered_cakes[] = $cake;
        }
    }
    $cakes = $filtered_cakes;
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
    default:
        $sort_field = 'name';
        $sort_order = 'asc';
        break;
}

// Sort cakes using Merge Sort
if (count($cakes) > 0) {
    mergeSort($cakes, 0, count($cakes) - 1, $sort_field, $sort_order);
}

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cake Catalog</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".add-to-cart-form").submit(function(event) {
                event.preventDefault();
                var isLoggedIn = <?php echo json_encode($is_logged_in); ?>;
                
                if (!isLoggedIn) {
                    alert("Please log in to order items.");
                    window.location.href = "../auth/login.php";
                    return;
                }
                
                var form = $(this);
                var formData = form.serialize();
                console.log("Form Data: ", formData); 

                $.ajax({
                    type: "POST",
                    url: "cart.php?action=add",
                    data: formData,
                    success: function(response) {
                        console.log("Response: ", response);
                        alert("Item added to cart successfully!");
                    },
                    error: function(xhr, status, error) {
                        console.error("Error: ", error); 
                        alert("An error occurred while adding the item to the cart.");
                    }
                });
            });
        });
    </script>
</head>
<body>
<header>
    <h1>Welcome to Cakeria</h1>
    <nav>
        <a href="../dashboard.php">Go to Dashboard</a>
        <a href="cart.php">View Cart</a>
        <a href="view_orders.php">My Orders</a>
    </nav>
</header>

<main>
    <form action="cake.php" method="GET">
        <label for="search">Search Cakes:</label>
        <input type="text" name="search" id="search" placeholder="Enter cake name or description" value="<?php echo htmlspecialchars($search); ?>">
        
        <label for="sort">Sort By:</label>
        <select name="sort" id="sort">
            <option value="name_asc" <?php if ($sort == 'name_asc') echo 'selected'; ?>>Name (A-Z)</option>
            <option value="name_desc" <?php if ($sort == 'name_desc') echo 'selected'; ?>>Name (Z-A)</option>
            <option value="price_asc" <?php if ($sort == 'price_asc') echo 'selected'; ?>>Price (Low to High)</option>
            <option value="price_desc" <?php if ($sort == 'price_desc') echo 'selected'; ?>>Price (High to Low)</option>
        </select>

        
        <button type="submit">Search & Sort</button>
    </form>

    <div class="cake-container">
        <?php foreach ($cakes as $row): ?>
            <div class="cake-item">
                <img src="../<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                <h3><?php echo $row['name']; ?></h3>
                <p><?php echo $row['description']; ?></p>
                <p>Price: NRS <?php echo $row['price']; ?></p>
                <form class="add-to-cart-form" method="POST">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="name" value="<?php echo $row['name']; ?>">
                    <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" value="1" min="1" max="100" required>
                    <button type="submit">Add to Cart</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</main>
</body>
</html>