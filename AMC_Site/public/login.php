<!--
Copyright Info
Ng Yong Kiat Shawn
Created 28 Nov 2024
-->

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
        <form method="POST" action="login.php" class="login-form">
            <h1>Login</h1>
            <?php if (!empty($error)): ?>
                <div class="error-message"><?= $error ?></div>
            <?php endif; ?>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="login-button">Login</button>
            <div class="form-footer">
                <a href="forgotPassword.php">Forgot Password?</a>
                <span> | </span>
                <a href="createAccount.php">Create Account</a>
            </div>
        </form>
    </div>
</body>
</html>
