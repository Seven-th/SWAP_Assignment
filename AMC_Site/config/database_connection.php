<?php
// Database configuration
$host = 'localhost';            // Hostname (default for XAMPP is 'localhost')
$db = 'usermanagement';         // Database name
$user = 'root';                 // Database username (default for XAMPP is 'root')
$pass = '';                     // Database password (default for XAMPP is an empty string)
$charset = 'utf8mb4';           // Character set for supporting Unicode

// Data Source Name (DSN) for PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// PDO options for error handling and fetch mode
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,   // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch data as associative arrays
    PDO::ATTR_EMULATE_PREPARES => false,           // Use native prepared statements
];

try {
    // Create a PDO instance (database connection)
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Handle connection errors
    die("Database connection failed: " . $e->getMessage());
}
?>