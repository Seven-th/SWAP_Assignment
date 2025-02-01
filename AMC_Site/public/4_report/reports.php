<?php
session_start();
require 'C:\xampp\htdocs\SWAP_Assignment\AMC_Site\config\database_connection.php'; // Database connection file

// Check if user is logged in and has the appropriate role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login_form.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// CSRF Protection: Generate a CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Encryption Function for Sensitive Data (AES-256)
define("ENCRYPTION_KEY", "your_secret_encryption_key_here"); // Store securely
function encryptData($data) {
    $key = hash('sha256', ENCRYPTION_KEY, true);
    $iv = random_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

// Decryption Function
function decryptData($encryptedData) {
    $key = hash('sha256', ENCRYPTION_KEY, true);
    $decoded = base64_decode($encryptedData);
    $iv = substr($decoded, 0, 16);
    $encryptedText = substr($decoded, 16);
    return openssl_decrypt($encryptedText, 'AES-256-CBC', $key, 0, $iv);
}

// Input Validation
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Initialize feedback message
$message = "";

// Handle delete report
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_report'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token. Possible attack detected.");
    }

    $report_id = sanitizeInput($_POST['report_id']);

    if ($user_role === 'Admin') {
        try {
            $deleteStmt = $pdo->prepare("DELETE FROM reports WHERE report_id = :report_id");
            $deleteStmt->bindParam(':report_id', $report_id, PDO::PARAM_INT);
            $deleteStmt->execute();
            $message = "Report deleted successfully.";
        } catch (Exception $e) {
            $message = "Error deleting report. Please try again later.";
            error_log("Report Deletion Error: " . $e->getMessage()); // Secure error logging
        }
    } else {
        // Set an error message if the user is not an Admin
        $message = "You do not have permission to delete reports.";
    }

    // Regenerate CSRF token after form submission
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch all reports securely
try {
    $stmt = $pdo->query("SELECT report_id, report_name, report_type, generated_by, report_data FROM reports");
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Database Fetch Error: " . $e->getMessage());
    $reports = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Management</title>
    <link rel="stylesheet" href="../assets/styles/dashboard.css">
    <style>
        body {
            background: #F9F9F9;
            color: #333;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #FFFFFF;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
            text-align: center;
            border: 1px solid #DDD;
        }
        h1, h2 {
            color: black;
        }
        .button {
            padding: 10px 20px;
            background-color: #D71920;
            color: #FFFFFF;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            display: inline-block;
            margin: 10px 5px;
        }
        .button:hover {
            background-color: #B6161A;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #DDD;
            text-align: left;
        }
        th {
            background: #F1F1F1;
            color: black;
        }
        td {
            background: #FFFFFF;
            color: black;
        }
        button {
            background: #FF5555;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #CC4444;
        }
        a.view-link, a.update-link {
            color: #0066CC;
            text-decoration: none;
            margin-left: 10px;
        }
        a.view-link:hover, a.update-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div style="position: fixed; top: 0; left: 0; width: 100%; z-index: 1000;">
        <?php require "../../includes/navbar.php"; ?>
    </div>
    <div class="container">
        <h1>Reports Management</h1>
        <a href="create.php" class="button">Create New Report</a>
        <a href="../dashboard.php" class="button">Back to Dashboard</a>
        <div id="output">
            <?php if (!empty($message)) echo "<p>$message</p>"; ?>
        </div>
        <h2>Existing Reports</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Report Name</th>
                    <th>Report Type</th>
                    <th>Generated By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($report['report_id']); ?></td>
                        <td><?php echo htmlspecialchars($report['report_name']); ?></td>
                        <td><?php echo htmlspecialchars($report['report_type']); ?></td>
                        <td><?php echo htmlspecialchars($report['generated_by']); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="report_id" value="<?php echo htmlspecialchars($report['report_id']); ?>">
                                <button type="submit" name="delete_report" <?php echo ($user_role !== 'Admin') ? 'disabled' : ''; ?>>Delete</button>
                            </form>
                            <?php if ($user_role === 'Admin'): ?>
                                <a href="update.php?id=<?php echo htmlspecialchars($report['report_id']); ?>" class="update-link">Update</a>
                            <?php endif; ?>
                            <a href="view.php?id=<?php echo htmlspecialchars($report['report_id']); ?>" class="view-link">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>