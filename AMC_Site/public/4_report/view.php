<?php
session_start();
require 'C:\xampp\htdocs\SWAP_Assignment\AMC_Site\config\database_connection.php'; 

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../login_form.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

define("ENCRYPTION_KEY", "your_secret_encryption_key_here");
function decryptData($encryptedData) {
    $key = hash('sha256', ENCRYPTION_KEY, true);
    $decoded = base64_decode($encryptedData);
    $iv = substr($decoded, 0, 16);
    $encryptedText = substr($decoded, 16);
    return openssl_decrypt($encryptedText, 'AES-256-CBC', $key, 0, $iv);
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

$message = "";

if (isset($_GET['id'])) {
    $report_id = sanitizeInput($_GET['id']);

    try {
        $stmt = $pdo->prepare("SELECT * FROM reports WHERE report_id = :report_id");
        $stmt->bindParam(':report_id', $report_id, PDO::PARAM_INT);
        $stmt->execute();
        $report = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($report) {
            $report['report_data'] = decryptData($report['report_data']);
        } else {
            $message = "Report not found.";
        }
    } catch (Exception $e) {
        error_log("Database Fetch Error: " . $e->getMessage());
        $message = "Error retrieving report data.";
    }
} else {
    header("Location: reports.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Report</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Product+Sans&display=swap">
    <style>
        body {
            background: #F9F9F9;
            color: #333;
            font-family: 'Product Sans';
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
        h1, h2, h3 {
            color: #0056b3; 
            margin-bottom: 15px;
        }
        .button {
            padding: 10px 20px;
            background-color: #0056b3; 
            color: #FFFFFF;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            display: inline-block;
            margin-top: 20px;
            border: none;
            transition: background 0.3s ease-in-out;
        }
        .button:hover {
            background-color: #003d7a;
        }
        p {
            text-align: left;
            background: #F1F1F1;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #DDD;
            color: black;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>View Report</h1>
        <div id="output">
            <?php if (!empty($message)): ?>
                <p><?php echo $message; ?></p>
            <?php elseif ($report): ?>
                <h2><?php echo sanitizeInput($report['report_name']); ?></h2>
                <p><strong>Report Type:</strong> <?php echo sanitizeInput($report['report_type']); ?></p>
                <p><strong>Generated By:</strong> <?php echo sanitizeInput($report['generated_by']); ?></p>
                <h3>Report Data:</h3>
                <p><?php echo nl2br(sanitizeInput($report['report_data'])); ?></p>
            <?php else: ?>
                <p>Report not found.</p>
            <?php endif; ?>
        </div>

        <a href="../4_report/reports.php" class="button">Back to Reports</a>
    </div>
</body>
</html>