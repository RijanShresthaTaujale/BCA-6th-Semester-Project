<?php
include '../../backend/db_config.php';

$identifier = '';
$password = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identifier = mysqli_real_escape_string($conn, $_POST['identifier']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
        $check_query = "SELECT * FROM users WHERE email = '$identifier'";
    } else {
        $check_query = "SELECT * FROM users WHERE username = '$identifier'";
    }
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: ../cakes/cake.php");
            exit();
        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "Username or email not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<header>
    <h1>Login</h1>
</header>

<main>
    <form method="POST" action="">
        <label for="identifier">Username or Email:</label>
        <input type="text" name="identifier" id="identifier" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Login</button>
    </form>
    <p><?php echo $message; ?></p>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
    <p><a href="../cakes/cake.php">View Available Cakes</a></p>
</main>

<footer>
    &copy; <?php echo date('Y'); ?> Cakeria. All rights reserved.
</footer>
</body>
</html>