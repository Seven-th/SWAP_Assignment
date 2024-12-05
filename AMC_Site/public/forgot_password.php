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
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../assets/styles/forgot_password.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Product+Sans&display=swap">
</head>
<body>

<div class="forgot-password-container">
    <div class="forgot-password-form">
        <h1>Forgot Password</h1>
        
        <form action="reset-password.php" method="POST">
            
            <!-- Email input to send OTP to -->
            <div class="form-group">
                <label for="email">Enter your email address</label>
                <input type="email" id="email" name="email" placeholder="Enter your registered email" required>
            </div>
            
            <!-- OTP input -->
            <div class="form-group">
                <label for="otp">Enter OTP</label>
                <input type="text" id="otp" name="otp" placeholder="Enter OTP sent to your email" required>
            </div>
            
            <!-- Submit button -->
            <button type="submit" class="forgot-password-button">Submit</button>
        </form>

        <!-- Footer Link to go back to Login -->
        <div class="form-footer">
            <p>Remembered your password? <a href="login.php">Back to Login</a></p>
        </div>
    </div>
</div>

</body>
</html>
