<!-- Copyright 2025 Ng Yong Kiat Shawn (Login form) -->

<?php

session_start();
require 'C:\xampp\htdocs\SWAP_Assignment\AMC_Site\config\database_connection.php'; 

$error = "";

// CSRF Token Generation and Validation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission of login page
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    //check if inputs are empty
    if (empty($email) || empty($password)) {
        $error = "Both fields are required!";
    }

    // Fetches data from database
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();


    //verifying password
    if ($user) {
        if (!$user['password_set']) {
            header("Location: forget_pass_form.php?msg=Please set your password first.");
            exit;
        }
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            // password is correct, start a session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            header("Location: dashboard.php");
            exit;
        } else {
            // Invalid credentials
            $error = "Invalid credentials";
        }
    } else {
        // Invalid credentials
        $error = "Invalid credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/styles/login.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Product+Sans&display=swap">
</head>
<body>
    <div class="login-container">
        <form method="POST" action="login_form.php" class="login-form">
        <?php if (!empty($error)): ?>
            <div class="error-message" style="color: red;"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
            <h1>Login</h1>
            <!-- Hidden Input to Track CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="login-button">Login</button>
            <div class="form-footer">
                <a href="forget_pass_form.php">Forgot Password?</a>
            </div>
        </form>
    </div>
</body>
</html>