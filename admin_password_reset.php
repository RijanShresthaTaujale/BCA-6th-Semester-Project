<?php
include 'backend/db_config.php';

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($new_password) || empty($confirm_password)) {
        $message = "Both password fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $message = "Passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $message = "Password must be at least 6 characters long.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $update_query = "UPDATE users SET password = '$hashed_password' WHERE role = 'admin'";
        
        if (mysqli_query($conn, $update_query)) {
            if (mysqli_affected_rows($conn) > 0) {
                $message = "✓ Admin password has been reset successfully! You can now login with username 'Abhi' and your new password.";
                $success = true;
            } else {
                $message = "Error: No admin account found to update.";
            }
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Password Reset</title>
    <link rel="stylesheet" href="frontend/css/styles.css">
    <style>
        body {
            background-color: #f5f5f5;
        }
        main {
            max-width: 400px;
            margin: 3rem auto;
            padding: 2rem;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #333;
        }
        input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 1rem;
        }
        input[type="password"]:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0,123,255,0.5);
        }
        button {
            width: 100%;
            padding: 0.75rem;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-bottom: 1.5rem;
            padding: 1rem;
            border-radius: 4px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .login-link {
            text-align: center;
            margin-top: 1rem;
        }
        .login-link a {
            color: #007bff;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<header>
    <h1>Admin Password Reset</h1>
</header>

<main>
    <?php if (!empty($message)): ?>
        <div class="message <?php echo $success ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if (!$success): ?>
        <form method="POST">
            <label for="new_password">New Admin Password:</label>
            <input type="password" name="new_password" id="new_password" required placeholder="Enter new password (min 6 chars)">

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" required placeholder="Confirm your password">

            <button type="submit">Reset Password</button>
        </form>
    <?php else: ?>
        <div class="login-link">
            <p><a href="frontend/auth/admin_login.php">Go to Admin Login</a></p>
        </div>
    <?php endif; ?>
</main>

<footer>
    &copy; <?php echo date('Y'); ?> Cakeria. All rights reserved.
</footer>
</body>
</html>
