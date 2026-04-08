<?php
session_start();


include '../../backend/db_config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../auth/admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = mysqli_real_escape_string($conn, $_POST['new_username']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    if (empty($new_username) || empty($new_password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $update_query = "UPDATE users SET username = '$new_username', password = '$hashed_password' WHERE role = 'admin'";
        if (mysqli_query($conn, $update_query)) {
            if (mysqli_affected_rows($conn) > 0) {
                $success_message = "Credentials updated successfully.";
            } else {
                $error_message = "No changes were made to the credentials.";
            }
        } else {
            $error_message = "Error updating credentials: " . mysqli_error($conn);
        }
    }
}


mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Admin Credentials</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<header>
    <h1>Change Admin Credentials</h1>
    <nav>
        <a href="admin_dashboard.php">Go to Dashboard</a>
        <a href="admin_logout.php">Logout</a>
    </nav>
</header>

<main>
    <form action="change_admin_credentials.php" method="POST">
        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <?php if (isset($success_message)): ?>
            <p style="color: green;"><?php echo $success_message; ?></p>
        <?php endif; ?>
        
        <label for="new_username">New Username:</label>
        <input type="text" name="new_username" id="new_username" required>

        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" id="new_password" autocomplete="new-password" required>

        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" autocomplete="new-password" required>

        <button type="submit">Update Credentials</button>
    </form>
</main>

<footer>
    &copy; <?php echo date('Y'); ?> Cakeria. All rights reserved.
</footer>
</body>
</html>