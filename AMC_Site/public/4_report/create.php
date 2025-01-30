<?php
session_start();
require 'C:\xampp\htdocs\SWAP_Assignment\AMC_Site\config\database_connection.php'; // Include the PDO connection file

// Check if user is logged in and has the appropriate role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login_form.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Initialize feedback message
$message = "";

// Handle report creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reportName = $_POST['report_name'];
    $reportType = $_POST['report_type'];
    $reportData = $_POST['report_data'];

    if ($user_role === 'Admin' || $user_role === 'Researcher') {
        $stmt = $pdo->prepare("INSERT INTO reports (report_name, report_type, generated_by, report_data) VALUES (?, ?, ?, ?)");
        $stmt->execute([$reportName, $reportType, $user_id, $reportData]);
        $message = "Report created successfully.";
    } else {
        $message = "You do not have permission to create reports.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Report</title>
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
            height: 40px;
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
        <h1>Create Report</h1>
        <div id="output">
            <?php if (!empty($message)) echo "<p>$message</p>"; ?>
        </div>
        <form method="POST" action="">
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

