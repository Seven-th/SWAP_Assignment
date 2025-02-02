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
function encryptData($data) {
    $key = hash('sha256', ENCRYPTION_KEY, true);
    $iv = random_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token. Possible attack detected.");
    }

    $reportName = sanitizeInput($_POST['report_name']);
    $reportType = sanitizeInput($_POST['report_type']);
    $reportData = sanitizeInput($_POST['report_data']);

    if ($user_role === 'Admin' || $user_role === 'Researcher') {
        try {
            $encryptedData = encryptData($reportData);
            $stmt = $pdo->prepare("INSERT INTO reports (report_name, report_type, generated_by, report_data) VALUES (?, ?, ?, ?)");
            $stmt->execute([$reportName, $reportType, $user_id, $encryptedData]);
            $message = "Report created successfully.";
        } catch (Exception $e) {
            $message = "Error creating report. Please try again later.";
            error_log("Report Creation Error: " . $e->getMessage());
        }
    } else {
        $message = "You do not have permission to create reports.";
    }

    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Report</title>
    <link rel="stylesheet" href="../assets/styles/dashboard.css">
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
        h1 {
            color: #0056b3; 
            margin-bottom: 20px;
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
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            text-align: left;
        }
        label {
            font-weight: bold;
            color: black;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #CCC;
            background: #FFFFFF;
            color: black;
            box-sizing: border-box;
        }
        textarea {
            resize: none;
            height: 80px;
        }
        button {
            background: #0056b3; 
            color: #FFFFFF;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease-in-out;
        }
        button:hover {
            background: #003d7a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Report</h1>
        <div id="output">
            <?php if (!empty($message)) echo "<p>$message</p>"; ?>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <label for="report_name">Report Name:</label>
            <input type="text" id="report_name" name="report_name" required>

            <label for="report_type">Report Type:</label>
            <select id="report_type" name="report_type" required>
                <option value="Research Progress">Research Progress</option>
                <option value="Project Funding">Project Funding</option>
                <option value="Equipment Usage">Equipment Usage</option>
            </select>

            <label for="report_data">Report Data:</label>
            <textarea id="report_data" name="report_data" required></textarea>

            <button type="submit">Create Report</button>
        </form>

        <a href="../4_report/reports.php" class="button">Back to Reports</a>
    </div>
</body>
</html>
