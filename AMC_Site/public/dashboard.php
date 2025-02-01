<?php
session_start();
require 'C:\xampp\htdocs\SWAP_Assignment\AMC_Site\config\database_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.php");
    exit;
}

// CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Getting user info from session
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Arial&display=swap">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: #F9F9F9; color: #333; }
        .navbar { background: #0066CC; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; width: 100%; }
        .navbar .logo { font-size: 24px; font-weight: bold; }
        .navbar .links a { color: white; text-decoration: none; margin: 0 15px; font-size: 18px; }
        .navbar .links a:hover { text-decoration: underline; }
        .navbar .user { font-size: 16px; }
        .navbar .logout { background: #FF5555; padding: 8px 12px; border-radius: 5px; text-decoration: none; color: white; font-weight: bold; }
        .navbar .logout:hover { background: #CC4444; }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">AMC Research Management</div>
        <div class="links">
            <a href="../public/1_researcher/create_account_form.php">User Management</a>
            <a href="../public/2_projects/projects.php">Research Projects</a>
            <a href="../public/3_inventory/inventory.php">Equipments</a>
            <a href="../public/4_report/reports.php">Reports</a>
        </div>
        <div class="user">
            Welcome, <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?> (<?= htmlspecialchars($role, ENT_QUOTES, 'UTF-8'); ?>)
            <a href="logout.php" class="logout" onclick="return confirm_logout();">Logout</a>
        </div>
    </div>

    <script>
    function confirm_logout() {
        return confirm("Are you sure you want to logout?");
    }
    </script>
</body>
</html>
