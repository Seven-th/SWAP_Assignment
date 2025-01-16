<?php
session_start();
require 'C:\xampp\htdocs\SWAP_Assignment\AMC_Site\config\database_connection.php'; // Include database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
    SELECT researcher.researcher_id, researcher.email, department.name AS department_name, permission.role AS permission_name
    FROM researcher
    JOIN department ON researcher.department_id = department.department_id
    JOIN permission ON researcher.permission_id = permission.permission_id
    WHERE researcher.researcher_id = :user_id
    ");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "$user";
        echo "User not found";
        exit;
    }

    $name = $user['email'];
    $department= $user['department_name'];
    $permission = $user['permission_name'];

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

?>

<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/styles/login.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Product+Sans&display=swap">
    <title>Dashboard</title>
</head>

<body>
    <header class="Header">
        <nav class="nav container">
            Department: <strong><?=htmlspecialchars($department);?></strong><br>
            Role: <strong><?=htmlspecialchars($permission);?></strong>
        </nav>
    </header>

    <div>
        <br>
        <a href="create_account.php">Create New Account</a>
        <br>
        <a href="inventory.php">Inventory Page</a>
    </div>
    
</body>