<!-- Copyright 2025 Ng Yong Kiat Shawn -->

<?php
session_start();
require 'C:\xampp\htdocs\SWAP_Assignment\AMC_Site\config\database_connection.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.php");
    exit;
}

// CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get user details
$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'];
$name    = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Product+Sans&display=swap">
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Product Sans', sans-serif;
        }
        body {
            background: #F9F9F9;
            color: #333;
            margin: 0;
        }
        
        /* Navbar Styles */
        .navbar { 
            background: #0066CC; 
            color: white; 
            padding: 15px 20px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            width: 100%; 
        }
        .navbar .logo { font-size: 24px; font-weight: bold; }
        .navbar .links a { 
            color: white; 
            text-decoration: none; 
            margin: 0 15px; 
            font-size: 18px; 
        }
        .navbar .links a:hover { text-decoration: underline; }
        .navbar .user { font-size: 16px; }
        .logout { 
            background: #004488; 
            padding: 8px 12px; 
            border-radius: 5px; 
            text-decoration: none; 
            color: white; 
            font-weight: bold; 
            margin-left: 10px;
        }
        .logout:hover { background: #003366; }
        
        /* Centered Welcome Message Styles */
        .welcome-message-container {
            /* Adjusting height to fill the viewport below the navbar */
            min-height: calc(100vh - 60px);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
        }
        .welcome-message {
            background-color: #FFFFFF;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .welcome-message h1 {
            margin-bottom: 10px;
        }
        .welcome-message p {
            margin-bottom: 20px;
            font-size: 18px;
            color: #666;
        }
        .welcome-message a.logout {
            display: inline-block;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">AMC Research Management</div>
        <div class="links">
            <?php if ($_SESSION['role'] !== "Research Assistant"): ?>
                <a href="../public/1_researcher/create_account_form.php">User Management</a>
            <?php endif; ?>
            <a href="../public/2_projects/projects.php">Research Projects</a>
            <a href="../public/3_inventory/inventory.php">Equipments</a>
            <a href="../public/4_report/reports.php">Reports</a>
        </div>
        <div class="user">
            Welcome, <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?> (<?= htmlspecialchars($role, ENT_QUOTES, 'UTF-8'); ?>)
            <a href="logout.php" class="logout" onclick="return confirm_logout();">Logout</a>
        </div>
    </div>
    
    <!-- Separate Centered Welcome Message -->
    <div class="welcome-message-container">
        <div class="welcome-message">
            <h1>Welcome to your dashboard,</h1>
            <h1><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>!</h1>
            <p>Your role: <?= htmlspecialchars($role, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
    </div>

    <script>
        function confirm_logout() {
            return confirm("Are you sure you want to logout?");
        }
    </script>
</body>
</html>
