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
    SELECT user.user_id, user.email, user.role
    FROM user
    WHERE user.user_id = :user_id
    ");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "$user";
        echo "User not found";
        exit;
    }

    $name = $user['email'];
    $role = $user['role'];

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

try {
    // Fetch all projects from the project table
    $stmt = $pdo->prepare("SELECT * FROM project");
    $stmt->execute();
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching projects: " . $e->getMessage());
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
                <a href="..\public\1_researcher\create_account_form.php"><i class="icon">👤</i> User Management</a>
                <a href="inventory.php"><i class="icon">📦</i> Equipment</a>
                <a href="../public/4_report/reports.php"><i class="icon">📄</i> Reports</a>
                <a href="logout.php" onclick="confirm_logout()"><i class="icon">🚪</i> Logout</a>
            </nav>
        </aside>

        <!-- main-content -->
        <main class="main-content">
            <header>
                <h1>Dashboard</h1>
                <div class="profile">
                    Name: <strong><?=htmlspecialchars($name);?></strong><br>
                    Role: <strong><?=htmlspecialchars($role);?></strong>
                </div>
            </header>

            <section class="projects">
                <table>
                    <thead>
                        <tr>
                            <th>Project ID</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Funding</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Generated By</th>
                            <th>Assigned To</th>
                            <th>Department ID</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($projects)): ?>
                            <?php foreach ($projects as $project): ?>
                                <tr>
                                    <td><?= htmlspecialchars($project['project_id']) ?></td>
                                    <td><?= htmlspecialchars($project['title']) ?></td>
                                    <td><?= htmlspecialchars($project['description']) ?></td>
                                    <td><?= htmlspecialchars($project['funding']) ?></td>
                                    <td><?= htmlspecialchars($project['status']) ?></td>
                                    <td><?= htmlspecialchars($project['project_priority_level']) ?></td>
                                    <td><?= htmlspecialchars($project['generated_by']) ?></td>
                                    <td><?= htmlspecialchars($project['assigned_to']) ?></td>
                                    <td><?= htmlspecialchars($project['department_id']) ?></td>
                                    <td><?= htmlspecialchars($project['created_at']) ?></td>
                                    <td><?= htmlspecialchars($project['updated_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" style="text-align: center;">No projects found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>

        </main>

    </div>

    <script>
    // Confirm logout script
    function confirm_logout() {
        if (confirm("Are you sure you want to logout?")) {
            fetch("logout.php")
                .then(response => window.location.href = "login_form.php?msg=You have been logged out successfully")
                .catch(error => console.error("Logout failed:", error));
        }
    }
    </script>
    
</body>
</html>