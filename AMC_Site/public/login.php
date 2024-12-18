<?php

session_start();

$host = 'localhost';           
$db = 'usermanagement';   
$user = 'root';                
$pass = '';
$charset = 'utf8mb4';          

$dsn = "mysql:host=$host;dbname=$db;charset=$charset"; 
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // Connection successful
    echo "Database connection successful!";
} catch (PDOException $e) {
    // Handle connection error
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submission of login page

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    //check if inputs are empty
    if (empty($email) || empty($password)) {
        header("Location: login.php");
        exit;
    }

    // Fetches data from database
    $stmt = $pdo->prepare("SELECT * FROM usermanagement.researcher WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    //verifying password
    if ($user && password_verify($password, $user['password'])) {
        // password is correct, start a session
        $_SESSION['user_id'] = $user['researcher_id'];
        $_SESSION['email'] = $user['email'];
        header("Location: dashboard.php");
        exit;
    } else {
        // Invalid credentials
        header("Location: login.php");
        exit;
    }
} else {
    // Invalid request method
    header("Location: login.php");
    exit;
}

include 'login_form.php';
?>
