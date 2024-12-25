<?php
// Include your database connection file
require 'C:\xampp\htdocs\SWAP_Assignment\AMC_Site\config\database_connection.php'; // Update the path as needed

// Define the table name
$table = 'researcher';

try {
    // Fetch all data from the table
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE password = 'password'");
    $stmt->execute();
    $results = $stmt->fetchAll();

    // Check if the table is empty
    if (!$results) {
        echo "No data found in the table.";
        exit;
    }
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Data</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 18px;
            text-align: left;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        table th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <h1>Data in Table: <?= htmlspecialchars($table) ?></h1>
    <table>
        <thead>
            <tr>
                <?php
                // Display table headers dynamically
                foreach (array_keys($results[0]) as $column) {
                    echo "<th>" . htmlspecialchars($column) . "</th>";
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
            // Display table rows dynamically
            foreach ($results as $row) {
                echo "<tr>";
                foreach ($row as $cell) {
                    echo "<td>" . htmlspecialchars($cell) . "</td>";
                }
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>