<?php
session_start();

// Check if logout is confirmed
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    // Unset all session variables
    $_SESSION = [];

    // Destroy the session
    session_destroy();

    // Redirect to login page with a success message
    header("Location: login_form.php?msg=You have been logged out successfully");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/styles/logout.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Product+Sans&display=swap">
    <title>Logout</title>

</head>
<body>

<div class="logout-box">
    <h2>Are you sure you want to logout?</h2>
    <p>This will end your session.</p>
    <button class="confirm-btn" onclick="confirmLogout()">Yes, Logout</button>
    <button class="cancel-btn" onclick="cancelLogout()">Cancel</button>
</div>

<script>
    function confirmLogout() {
        window.location.href = "logout.php?confirm=yes"; // Proceed with logout
    }

    function cancelLogout() {
        window.history.back(); // Go back to the previous page
    }
</script>

</body>
</html>
