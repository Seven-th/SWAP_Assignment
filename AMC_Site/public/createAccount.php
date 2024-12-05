<!-- Copyright Info
Ng Yong Kiat Shawn
Created 28 Nov 2024 -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="../assets/styles/createAccount.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Product+Sans&display=swap">
</head>
<body>

    <div class="create-account-container">
        <div class="create-account-form">
            <h1>Create Account</h1>
            <form action="signup.php" method="POST">
                <!-- Hidden Input to Track Role -->
                
                
                <!-- Email input -->
                <div class="form-group">
                    <label for="email">Enter your email address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <!-- Password input -->
                <div class="form-group">
                    <label for="password">Choose a Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <!-- Confirm Password input -->
                <div class="form-group">
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" required>
                </div>
                <!-- Submit button -->
                <button type="submit" class="create-account-button">Create Account</button>
            </form>
            <!-- Footer Link -->
            <div class="form-footer">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>

    <script src="../assets/scripts/tabBar.js"></script>
</body>
</html>
