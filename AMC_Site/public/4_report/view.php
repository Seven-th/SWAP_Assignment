<?php
session_start();
require 'C:\xampp\htdocs\SWAP_Assignment\AMC_Site\config\database_connection.php'; // Include the PDO connection file

// Check if user is logged in and has the appropriate role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login_form.php");
    exit();
}

// Fetch report data for the given ID
if (isset($_GET['id'])) {
    $report_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM reports WHERE report_id = :report_id");
    $stmt->bindParam(':report_id', $report_id);
    $stmt->execute();
    $report = $stmt->fetch(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="../assets/styles/dashboard.css">
</head>
<body>
    <div class="container">
        <h1>View Report</h1>
        <div id="output">
            <?php if ($report): ?>
                <h2><?php echo htmlspecialchars($report['report_name']); ?></h2>
                <p><strong>Report Type:</strong> <?php echo htmlspecialchars($report['report_type']); ?></p>
                <p><strong>Generated By:</strong> <?php echo htmlspecialchars($report['generated_by']); ?></p>
                <h3>Report Data:</h3>
                <p><?php echo nl2br(htmlspecialchars($report['report_data'])); ?></p>
            <?php else: ?>
                <p>Report not found.</p>
            <?php endif; ?>
        </div>
        <a href="../4_report/reports.php" class="button">Back to Reports</a> <!-- Back button -->
    </div>
</body>
</html>