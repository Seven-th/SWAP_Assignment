<?php
session_start();
require 'C:\xampp\htdocs\SWAP_Assignment\AMC_Site\config\database_connection.php'; // Database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.php");
    exit;
}

// CSRF Token Generation and Validation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Getting user info from session
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$name = $_SESSION['name'];

try {
    // Fetch all projects from the project table securely
    $stmt = $pdo->prepare("SELECT * FROM project");
    $stmt->execute();
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage()); // Log error securely
    die("An error occurred. Please try again later.");
}
?>

<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Arial&display=swap">
    <title>AMC Procurement Portal - Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: #F9F9F9; color: #333; height: 100vh; width: 100vw; display: flex; flex-direction: column; align-items: center; }
        .header { background: #D71920; color: white; padding: 20px; font-size: 24px; font-weight: bold; text-align: center; width: 100%; }
        h1, h2 { color: black; }
        .button { padding: 10px 20px; background-color: #D71920; color: #FFFFFF; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block; margin: 10px 5px; }
        .button:hover { background-color: #B6161A; }
        .container { display: flex; flex-direction: column; align-items: center; width: 100%; padding: 20px; }
        .card { background: white; padding: 20px; margin: 10px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 90%; max-width: 400px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #DDD; text-align: left; }
        th { background: #F1F1F1; color: black; }
        td { background: #FFFFFF; color: black; }
        button { background: #FF5555; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; }
        button:hover { background: #CC4444; }
        a.view-link, a.update-link { color: #0066CC; text-decoration: none; margin-left: 10px; }
        a.view-link:hover, a.update-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="header">AMC Research Management</div>
    <h1>Welcome, <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?> (<?= htmlspecialchars($role, ENT_QUOTES, 'UTF-8'); ?>)</h1>
    <div class="container">
        <div class="card">
            <h2>User Management</h2>
            <p>Manage Users</p>
            <a href="../public/1_researcher/create_account.php" class="button">View</a>
        </div>
        <div class="card">
            <h2>Equipments</h2>
            <p>Manage Equipments</p>
            <a href="../public/3_inventory/inventory.php" class="button">View</a>
        </div>
        <div class="card">
            <h2>Reports</h2>
            <p>Manage Reports</p>
            <a href="../public/4_report/reports.php" class="button">View</a>
        </div>
    </div>
    <?php if ($role !== 'Research Assistant'): ?>
        <form method="POST" action="logout.php" onsubmit="return confirm_logout();">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
            <button type="submit" class="button">Logout</button>
        </form>
    <?php endif; ?>

    <script>
    function confirm_logout() {
        return confirm("Are you sure you want to logout?");
    }
    </script>
</body>
</html>
