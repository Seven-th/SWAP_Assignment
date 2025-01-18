<?php
session_start();
require 'C:\xampp\htdocs\SWAP_Assignment\AMC_Site\config\database_connection.php'; // Include database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
    SELECT researcher.researcher_id, researcher.email, department.name AS department_name, permission.role AS permission_name
    FROM researcher
    JOIN department ON researcher.department_id = department.department_id
    JOIN permission ON researcher.permission_id = permission.permission_id
    WHERE researcher.researcher_id = :user_id
    ");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "$user";
        echo "User not found";
        exit;
    }

    $name = $user['email'];
    $department= $user['department_name'];
    $permission = $user['permission_name'];

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

try {
    $stmt = $pdo->prepare("
    SELECT * FROM project
    JOIN researcher on project.generated_by = researcher.id
    JOIN researcher on project.assigned_to = researcher.id
    JOIN department on project.department_id = department.id
    ");
    $projects = $stmt->fetchAll();

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

?>

<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/styles/dashboard.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Product+Sans&display=swap">
    <title>Dashboard</title>
</head>

<body>
    <div class = "container">
        <aside class="sidebar">
            <div>
                <h1>AMC COMPANY</h1>
            </div>    
            <nav class="menu">
                <a href="dashboard.php" class="active"><i class="icon">🏠</i> Dashboard</a>
                <!-- To add or not to add. That is the question.-->
                <!-- <a href="profile.php"><i class="icon">👤</i> Profile</a> -->
                <a href="user_management.php"><i class="icon">👤</i> User Management</a>
                <a href="equipment.php"><i class="icon">📦</i> Equipment</a>
                <a href="reports.php"><i class="icon">📄</i> Reports</a>
                <a href="logout.php"><i class="icon">🚪</i> Logout</a>
            </nav>
        </aside>

        <!-- main-content -->
        <main class="main-content">
            <header>
                <h1>Dashboard</h1>
                <div class="profile">
                    Department: <strong><?=htmlspecialchars($department);?></strong><br>
                    Role: <strong><?=htmlspecialchars($permission);?></strong>
                </div>
            </header>

            <section class="projects">
                <table>
                    <thead>
                        <tr>
                            <?php
                            // Display table headers dynamically
                            foreach (array_keys($projects[0]) as $column) {
                                echo "<th>" . htmlspecialchars($column) . "</th>";
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Display table rows dynamically
                        foreach ($projects as $row) {
                            echo "<tr>";
                            foreach ($row as $cell) {
                                echo "<td>" . htmlspecialchars($cell) . "</td>";
                            }
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>

        </main>

    </div>
    
</body>