<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'usermanagement');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize feedback message
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reportType = $_POST['reportType'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];

    // Validate inputs
    if (!empty($reportType) && !empty($startDate) && !empty($endDate)) {
        // Query for report generation (example query, adjust as needed)
        $query = "SELECT * FROM reports WHERE type = ? AND date BETWEEN ? AND ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sss', $reportType, $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Generate report (basic example: display results)
            $message = "<h3>Generated Report:</h3><ul>";
            while ($row = $result->fetch_assoc()) {
                $message .= "<li>" . $row['data'] . "</li>";
            }
            $message .= "</ul>";
        } else {
            $message = "<p>No data found for the selected criteria.</p>";
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