<?php
session_start();
require 'C:\xampp\htdocs\SWAP_Assignment\AMC_Site\config\database_connection.php'; // Include database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.php");
    exit;
}



$role= $_SESSION['role'];
$name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/styles/login.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Product+Sans&display=swap">
</head>

<body>
    <div class="section">
        <p>Your role: <strong><?=$role?></strong></p>
    </div>