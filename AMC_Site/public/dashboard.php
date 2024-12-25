<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.php");
    exit;
}

echo "<h1>Welcome, " . htmlspecialchars($_SESSION['email']) . "!</h1>";
echo "<p>You are successfully logged in.</p>";

?>