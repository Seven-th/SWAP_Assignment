<?php
// Include your database connection file
session_start();
require 'C:\xampp\htdocs\SWAP_Assignment\AMC_Site\config\database_connection.php'; 

$error = "";

// CREATE operation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensures only Admins and Researcher can create new user accounts
    if ($_SESSION['role'] === "Admin" || $_SESSION["role"] === "Researcher") {
        // Sanitize and validate input
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone_number = intval($_POST['phone_number']);
        $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Hash the password
        $role = $_POST['role'];

        if (empty($name) || empty($email) || empty($password) || empty($phone_number) || empty($role)) {
            $error = "All fields are required.";
        }

        try {
            // Insert new user into the database
            $stmt = $pdo->prepare("
                INSERT INTO user (name, email, phone_number, password, role) 
                VALUES (:name, :email, :phone_number, :password, :role)
            ");
                    $stmt->execute([
                        'name' => $name,
                        'email' => $email,
                        'phone_number' => $phone_number,
                        'password' => $password,
                        'role' => $role
            ]);
            echo "User created successfully!";
                } catch (PDOException $e) {
            $error = "Error creating user: " . $e->getMessage();
        }
    }
}

// DELETE operation
if (isset($_GET['id'])) {
    // Ensure only Admins can delete users
    if (isset($_SESSION['role']) || $_SESSION['role'] === 'Admin') {
        $user_id = intval($_GET['id']);

        try {
            // Delete researcher from database
            $stmt = $pdo->prepare("DELETE FROM user WHERE user_id = :id");
            $stmt->execute(['id' => $user_id]);

            // Redirect back to researcher list after deletion
            header("Location: create_account_form.php?msg=Researcher deleted successfully");
            exit;
        } catch (PDOException $e) {
            die("Error deleting researcher: " . $e->getMessage());
        }
    } else {
        die("Access denied. Only admins can perform this action.");
    }
} else {
    // If no ID provided, redirect back
    header("Location: create_account_form.php?error=Invalid request");
    exit;
}

include 'create_account_form.php';
?>
