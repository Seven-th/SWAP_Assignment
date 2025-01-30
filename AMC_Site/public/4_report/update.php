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

// Fetch report data for the given ID
if (isset($_GET['id'])) {
    $report_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM reports WHERE report_id = :report_id");
    $stmt->bindParam(':report_id', $report_id);
    $stmt->execute();
    $report = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle report update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($user_role === 'Admin') {
        $reportName = $_POST['report_name'];
        $reportType = $_POST['report_type'];
        $reportData = $_POST['report_data'];

        $updateStmt = $pdo->prepare("UPDATE reports SET report_name = ?, report_type = ?, report_data = ? WHERE report_id = ?");
        $updateStmt->execute([$reportName, $reportType, $reportData, $report_id]);
        $message = "Report updated successfully.";
    } else {
        $message = "You do not have permission to update reports.";
    }
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
        .button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            display: inline-block;
        }
        .button:hover {
            background-color: #0056b3;
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
        <a href="reports.php" class="button">Back to Reports</a> <!-- Back button -->
    </div>
</body>
</html>