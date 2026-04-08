<?php
include '../../backend/db_config.php';
$username = '';
$email = '';
$password = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    if (!preg_match('/^(?=.*[a-zA-Z])[a-zA-Z0-9]+$/', $username)) {
        $message = "Username must contain at least one letter and no special characters.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } elseif (!preg_match('/^[a-zA-Z0-9._]+@gmail\.com$/', $email)) {
        $message = "Email must be a complete address ending with @gmail.com.";
    } else {
        $check_query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($result) > 0) {
            $message = "Email is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
            if (mysqli_query($conn, $insert_query)) {
                $message = "Registration successful! You can now login.";
            } else {
                error_log("MySQL error: " . mysqli_error($conn));
                $message = "Error: Could not register user. Please try again later.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<header>
    <h1>Register</h1>
</header>

<main>
    <form method="POST" action="">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required value="<?php echo htmlspecialchars($username); ?>">

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($email); ?>">

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Register</button>
    </form>
    <p><?php echo $message; ?></p>
    <p>Already have an account? <a href="login.php">Login here</a></p>
    <p><a href="../cakes/cake.php">View Available Cakes</a></p>
</main>

<footer>
    &copy; <?php echo date('Y'); ?> Cakeria. All rights reserved.
</footer>
</body>
</html>