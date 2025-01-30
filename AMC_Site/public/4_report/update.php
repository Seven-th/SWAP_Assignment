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

// Fetch report data for the given ID
if (isset($_GET['id'])) {
    $report_id = sanitizeInput($_GET['id']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM reports WHERE report_id = :report_id");
        $stmt->bindParam(':report_id', $report_id, PDO::PARAM_INT);
        $stmt->execute();
        $report = $stmt->fetch(PDO::FETCH_ASSOC);

        // Decrypt report data
        if ($report) {
            $report['report_data'] = decryptData($report['report_data']);
        } else {
            $message = "Report not found.";
        }
    } catch (Exception $e) {
        error_log("Database Fetch Error: " . $e->getMessage());
        $message = "Error retrieving report data.";
    }
}

// Handle report update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token. Possible attack detected.");
    }

    if ($user_role === 'Admin') {
        $reportName = sanitizeInput($_POST['report_name']);
        $reportType = sanitizeInput($_POST['report_type']);
        $reportData = encryptData(sanitizeInput($_POST['report_data']));

        try {
            $updateStmt = $pdo->prepare("UPDATE reports SET report_name = ?, report_type = ?, report_data = ? WHERE report_id = ?");
            $updateStmt->execute([$reportName, $reportType, $reportData, $report_id]);
            $message = "Report updated successfully.";
        } catch (Exception $e) {
            error_log("Report Update Error: " . $e->getMessage());
            $message = "Error updating report. Please try again later.";
        }
    } else {
        $message = "You do not have permission to update reports.";
    }

    // Regenerate CSRF token after form submission
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Report</title>
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
            max-width: 600px;
            width: 100%;
            text-align: center;
            border: 1px solid #DDD;
        }
        h1 {
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
            margin-top: 20px;
        }
        .button:hover {
            background-color: #B6161A;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            text-align: left;
            font-weight: bold;
            color: black;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #DDD;
            background: #FFFFFF;
            color: black;
            box-sizing: border-box;
        }
        textarea {
            resize: none;
            height: 100px;
        }
        button {
            background: #D71920;
            color: #FFFFFF;
            border: none;
            padding: 10px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background: #B6161A;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Update Report</h1>
        <div id="output">
            <?php if (!empty($message)) echo "<p>$message</p>"; ?>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <label for="report_name">Report Name:</label>
            <input type="text" id="report_name" name="report_name" value="<?php echo htmlspecialchars($report['report_name']); ?>" required>

            <label for="report_type">Report Type:</label>
            <select id="report_type" name="report_type" required>
                <option value="Research Progress" <?php if ($report['report_type'] == 'Research Progress') echo 'selected'; ?>>Research Progress</option>
                <option value="Project Funding" <?php if ($report['report_type'] == 'Project Funding') echo 'selected'; ?>>Project Funding</option>
                <option value="Equipment Usage" <?php if ($report['report_type'] == 'Equipment Usage') echo 'selected'; ?>>Equipment Usage</option>
            </select>

            <label for="report_data">Report Data:</label>
            <textarea id="report_data" name="report_data" required><?php echo htmlspecialchars($report['report_data']); ?></textarea>

            <button type="submit">Update Report</button>
        </form>
        <a href="reports.php" class="button">Back to Reports</a>
    </div>
</body>
</html>