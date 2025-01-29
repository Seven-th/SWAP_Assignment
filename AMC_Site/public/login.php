<?php

session_start();
require 'C:\xampp\htdocs\SWAP_Assignment\AMC_Site\config\database_connection.php'; 

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
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();


    //verifying password
    if ($user && password_verify($password, $user['password'])) {
        // password is correct, start a session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        header("Location: dashboard.php");
        exit;
        
        // DELETE THE ELSEIF ONCE DONE THIS SHI UNSAFE
    } elseif ($user && $password == $user['password']){
        // password is correct, start a session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
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