<?php

session_start();
require 'C:\xampp\htdocs\SWAP_Assignment\AMC_Site\config\database_connection.php'; // Include database connection file

$error = "";

// Handle form submission of login page
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    //check if inputs are empty
    if (empty($email) || empty($password)) {
        $error = "Both fields are required!";
    }

    // Fetches data from database
    $stmt = $pdo->prepare("SELECT * FROM researcher WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();


    //verifying password
    if ($user && $password == $user['password']) {
        // password is correct, start a session
        $_SESSION['user_id'] = $user['researcher_id'];
        $_SESSION['role'] = $user['department_id'];
        $_SESSION['name'] = $user['name'];
        header("Location: dashboard.php");
        exit;
    } else {
        // Invalid credentials
        $error = "Invalid credentials";
    } 
}

include 'login_form.php';
?>