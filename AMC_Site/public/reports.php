<?php
session_start();
require 'C:\xampp\htdocs\SWAP_Assignment\AMC_Site\config\database_connection.php'; // Include the PDO connection file

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login_form.php");
    exit();
}

$user_id = $_SESSION['user_id'];  // Assuming user_id is stored in the session
$department_id = $_SESSION['department_id'];  // Assuming department_id is stored in the session

// Initialize feedback message
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reportType = $_POST['reportType'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];

    // Validate inputs
    if (!empty($reportType) && !empty($startDate) && !empty($endDate)) {
        // Query for report generation (adjust based on reportType)
        if ($reportType == 'research_progress') {
            // Example query for research progress report (from project_activity_log)
            $query = "SELECT * FROM project_activity_log 
                      WHERE project_status = 'Ongoing' 
                      AND created_at BETWEEN :start_date AND :end_date";
        } elseif ($reportType == 'project_funding') {
            // Example query for project funding report (from project)
            $query = "SELECT * FROM project 
                      WHERE funding IS NOT NULL 
                      AND created_at BETWEEN :start_date AND :end_date";
        } elseif ($reportType == 'equipment_usage') {
            // Example query for equipment usage report (from equipment_usage_log)
            $query = "SELECT * FROM equipment_usage_log 
                      WHERE last_used_by = :user_id 
                      AND department_id = :department_id 
                      AND quantity > 0 
                      AND created_at BETWEEN :start_date AND :end_date";
        } else {
            $message = "<p style='color: red;'>Invalid report type selected.</p>";
        }

        // Prepare the statement for the report query
        if (isset($query)) {
            $stmt = $pdo->prepare($query);

            // Bind parameters depending on the report type
            if ($reportType == 'research_progress' || $reportType == 'project_funding') {
                $stmt->bindParam(':start_date', $startDate);
                $stmt->bindParam(':end_date', $endDate);
            } elseif ($reportType == 'equipment_usage') {
                // Bind parameters for equipment usage report
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':department_id', $department_id, PDO::PARAM_INT);
                $stmt->bindParam(':start_date', $startDate);
                $stmt->bindParam(':end_date', $endDate);
            }

            // Execute the query
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result) {
                // Generate report (basic example: display results)
                $message = "<h3>Generated Report:</h3><ul>";
                foreach ($result as $row) {
                    // Adjust this line based on the expected data in your report
                    $message .= "<li>" . htmlspecialchars($row['data_points'] ?? $row['funding'] ?? $row['product_name']) . "</li>";
                }
                $message .= "</ul>";
            } else {
                $message = "<p>No data found for the selected criteria.</p>";
            }
        }
    } else {
        $message = "<p style='color: red;'>Please fill out all fields.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Reports</title>
    <link rel="stylesheet" href="../assets/styles/dashboard.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #28a745;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Generate Report</h1>
        <form method="POST" action="">
            <label for="reportType">Select Report Type:</label>
            <select id="reportType" name="reportType" required>
                <option value="">-- Select --</option>
                <option value="research_progress">Research Progress</option>
                <option value="project_funding">Project Funding</option>
                <option value="equipment_usage">Equipment Usage</option>
            </select>

            <label for="startDate">Start Date:</label>
            <input type="date" id="startDate" name="startDate" required>

            <label for="endDate">End Date:</label>
            <input type="date" id="endDate" name="endDate" required>

            <button type="submit">Generate Report</button>
        </form>

        <div id="output">
            <?php
            if (!empty($message)) {
                echo $message;
            }
            ?>
        </div>
    </div>
</body>
</html>
