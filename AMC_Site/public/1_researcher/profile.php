<?php
require 'C:\xampp\htdocs\SWAP_Assignment\AMC_Site\config\database_connection.php'; // Include database connection file

// Check if an ID is provided in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID.");
}

$user_id = intval($_GET['id']);
$error = "";
$success = "";

// Fetch user data from the user table
try {
    $stmt = $pdo->prepare("
        SELECT * FROM user WHERE user_id = :id
    ");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found.");
    }
} catch (PDOException $e) {
    die("Error fetching user details: " . $e->getMessage());
}

// Handle form submission for updating user details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];
    $role = trim($_POST['role']); // Now using ENUM

    try {
        $update_stmt = $pdo->prepare("
            UPDATE user 
            SET name = :name, email = :email, phone_number = :phone, password = :password, role = :role
            WHERE user_id = :id
        ");
        $update_stmt->execute([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'role' => $role,
            'id' => $user_id
        ]);

        $success = "User details updated successfully!";
        // Refresh user data
        header("Refresh:2; url=create_account_form.php");
    } catch (PDOException $e) {
        $error = "Error updating user: " . $e->getMessage();
    }
}

include 'profile_form.php';
?>