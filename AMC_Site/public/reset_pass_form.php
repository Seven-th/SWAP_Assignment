<?php
session_start();
require "..\config\database_connection.php";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $token = trim($_POST['token']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    $error = "";

    // Validate new password
    if (empty($new_password) || empty($confirm_password)) {
        $error = "Please enter and confirm your new password.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if the reset token is valid and not expired
        $stmt = $pdo->prepare("
            SELECT password_reset_requests.user_id FROM password_reset_requests 
            JOIN user ON password_reset_requests.user_id = user.user_id
            WHERE password_reset_requests.token = :token AND user.email = :email AND password_reset_requests.expires_at > NOW()
        ");
        $stmt->execute(['token' => $token, 'email' => $email]);
        $request = $stmt->fetch();

        if ($request) {
            // Update the user's password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                UPDATE user SET password = :password, password_set = TRUE WHERE user_id = :user_id
            ");
            $stmt->execute(['password' => $hashed_password, 'user_id' => $request['user_id']]);

            // Delete the used reset token
            $stmt = $pdo->prepare("DELETE FROM password_reset_requests WHERE token = :token");
            $stmt->execute(['token' => $token]);

            $success = "Password reset successfully! You can now log in.";
            header("Refresh:2; url=login_form.php");
        } else {
            $error = "Invalid or expired reset token.";
        }
    }
} elseif (isset($_GET['token'], $_GET['email'])) {
    // Pre-fill the token and email fields from the URL
    $token = trim($_GET['token']);
    $email = trim($_GET['email']);
} else {
    die("Invalid request.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../assets/styles/reset_password.css">
</head>
<body>
    <div class="reset-password-container">
        <h1>Reset Password</h1>
        <?php if (!empty($error)): ?>
            <div class="error-message" style="color: red;"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success-message" style="color: green;"><?= htmlspecialchars($success); ?></div>
        <?php else: ?>
            <form method="POST" class="reset_pass_form">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token); ?>">
                <input type="hidden" name="email" value="<?= htmlspecialchars($email); ?>">

                <div class="form-group">
                    <label for="new_password">Enter New Password</label>
                    <input type="password" name="new_password" placeholder="Enter new password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" name="confirm_password" placeholder="Confirm new password" required>
                </div>

                <button type="submit" class="submit-button">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
