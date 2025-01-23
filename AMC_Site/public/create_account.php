<?php
// Include your database connection file
require 'C:\xampp\htdocs\SWAP_Assignment\AMC_Site\config\database_connection.php'; 

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone_number = intval($_POST['phone_number']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Hash the password
    $department_id = intval($_POST['department']); // Get department ID as an integer
    $permission_id = intval($_POST['permission']);

    if (empty($name) || empty($email) || empty($password) || empty($phone_number) || empty($department_id) || empty($permission_id)) {
        $error = "All fields are required.";
    }

    try {
        // Insert new user into the database
        $stmt = $pdo->prepare("
            INSERT INTO researcher (name, email, phone_number, password, department_id, permission_id) 
            VALUES (:name, :email, :phone_number, :password, :department_id, :permission_id)
        ");
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'phone_number' => $phone_number,
            'password' => $password,
            'department_id' => $department_id,
            'permission_id' => $permission_id
        ]);

        echo "User created successfully!";
    } catch (PDOException $e) {
        $error = "Error creating user: " . $e->getMessage();
    }
}

include 'create_account_form.php';
?>
