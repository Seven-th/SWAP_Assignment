<?php
session_start();
require "..\config\database_connection.php"; 
require "..\config\PHPMailer\src\PHPMailer.php";
require "..\config\PHPMailer\src\SMTP.php";
require "..\config\PHPMailer\src\Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Singapore'); // Set server's time zone
$error = "";
$success = ""; // Initialize success variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $stmt = $pdo->prepare("SELECT user_id FROM user WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            // Generates a reset Token
            $reset_token = bin2hex(random_bytes(32));
            $stmt = $pdo->prepare("
                INSERT INTO password_reset_requests (user_id, token, expires_at) 
                VALUES (:user_id, :token, DATE_ADD(NOW(), INTERVAL 1 HOUR))
            ");
            $stmt->execute(['user_id' => $user['user_id'], 'token' => $reset_token]);

            // Send a password reset link to the user's email using SMTP
            $mail = new PHPMailer(true);
            try {
                // SMTP server configuration
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'youwanttoprayletspray@gmail.com'; // Your email address
                $mail->Password = 'dubh ifsa isxs beed';   // Your email's app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
                $mail->Port = 587;

                // Email content
                $mail->setFrom('youwanttoprayletspray@gmail.com', 'AMC Website');
                $mail->addAddress($email); // Add recipient
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $reset_link = 'http://localhost/SWAP_Assignment/AMC_Site/public/reset_pass_form.php?email=' . urlencode($email) . '&token=' . urlencode($reset_token);
                $mail->Body = "Hello,<br><br>Click the link below to reset your password:<br>
                            <a href='$reset_link'>Reset Link</a><br><br>This link will expire in 1 hour.";
                $mail->send();

                $success = "A password reset link has been sent to your email.";
            } catch (Exception $e) {
                $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $error = "Email address not found.";
        }
    }
} else {
    $error = "Please enter your email address.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/styles/login.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Product+Sans&display=swap">
    <title>Forgot Password</title>
</head>
<body>
    <div class="forget-password-container">
        <h1>Forgot Password</h1>
        <form method="POST" class="forget_pass_form">
            <?php if (!empty($error)): ?>
                <div class="error-message" style="color: red;"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="success-message" style="color: green;"><?= htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <div class="form-group">
                <label for="email">Enter your email address</label>
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit" class="submit-button">Send Reset Link</button>
            </div>
        </form>
    </div>
</body>
</html>