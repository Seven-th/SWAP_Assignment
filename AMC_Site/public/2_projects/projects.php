<?php
session_start();
require 'C:\xampp\htdocs\SWAP_Assignment\AMC_Site\config\database_connection.php';

// Initialize variables for feedback messages
$_SESSION['success'] = '';
$_SESSION['error'] = '';

// Role-based access control
if (!isset($_SESSION['role'])) {
    header("Location: /SWAP_Assignment/AMC_Site/public/login.php");
    exit();
}

// Restrict Research Assistants from accessing this page
if ($_SESSION['role'] === 'Research Assistant') {
    header("Location: /SWAP_Assignment/AMC_Site/public/unauthorized.php");
    exit();
}

$user_role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Check CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }

    $action = $_POST['action'];

    // Add Project
    if ($action === 'Add' && ($user_role === 'Admin' || $user_role === 'Research Assistant')) {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $funding = floatval($_POST['funding']);
        $status = $_POST['status'];
        $priority = $_POST['project_priority_level'];
        $assigned_to = trim($_POST['assigned_to']);
        
        if (!empty($title) && !empty($description) && in_array($status, ['Ongoing', 'Completed']) && in_array($priority, ['Low', 'Medium', 'High'])) {
            $stmt = $pdo->prepare("INSERT INTO project (title, description, funding, status, project_priority_level, assigned_to) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $funding, $status, $priority, $assigned_to]);
            
            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = "Project added successfully.";
            } else {
                $_SESSION['error'] = "Error adding project.";
            }
        } else {
            $_SESSION['error'] = "Please provide valid inputs for adding a project.";
        }
    }

    // Update Project
    elseif ($action === 'Update' && ($user_role === 'Admin' || $user_role === 'Research Assistant')) {
        $project_id = intval($_POST['project_id']);
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $funding = floatval($_POST['funding']);
        $status = $_POST['status'];
        $priority = $_POST['project_priority_level'];
        $assigned_to = trim($_POST['assigned_to']);
        
        if ($project_id > 0 && !empty($title) && !empty($description) && in_array($status, ['Ongoing', 'Completed']) && in_array($priority, ['Low', 'Medium', 'High'])) {
            $stmt = $pdo->prepare("UPDATE project SET title = ?, description = ?, funding = ?, status = ?, project_priority_level = ?, assigned_to = ? WHERE project_id = ?");
            $stmt->execute([$title, $description, $funding, $status, $priority, $assigned_to, $project_id]);

            
            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = "Project updated successfully.";
            } else {
                $_SESSION['error'] = "No project found with the provided ID.";
            }
        } else {
            $_SESSION['error'] = "Please provide valid inputs for updating the project.";
        }
    }

    // Delete Project
    elseif ($action === 'Delete' && $user_role === 'Admin') {
        $project_id = intval($_POST['project_id']);
        
        if ($project_id > 0) {
            $stmt = $pdo->prepare("DELETE FROM project WHERE project_id = ?");
            $stmt->execute([$project_id]);
            
            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = "Project deleted successfully.";
            } else {
                $_SESSION['error'] = "No project found with the provided ID.";
            }
        } else {
            $_SESSION['error'] = "Please provide a valid project ID to delete.";
        }
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM project");
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Management</title>
    <link rel="stylesheet" href="../../assets/styles/projects.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Product+Sans&display=swap">
</head>
<body>
    <div style="position: fixed; top: 0; left: 0; width: 100%; z-index: 1000;">
        <?php require "../../includes/navbar.php"; ?>
    </div>
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Funding</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Assigned To</th>
                    <th>Actions</th>
                </tr>
                <tr>
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <td><input type="text" name="title" placeholder="Title" required></td>
                        <td><input type="text" name="description" placeholder="Description" required></td>
                        <td><input type="number" name="funding" placeholder="Funding" step="0.01" required></td>
                        <td><select name="status">
                            <option value="Ongoing">Ongoing</option>
                            <option value="Completed">Completed</option>
                        </select></td>
                        <td><select name="project_priority_level">
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select></td>
                        <td><input type="text" name="assigned_to" placeholder="Assigned To" required></td>
                        <td><input type="submit" name="action" value="Add"></td>
                    </form>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($projects)): ?>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <form method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="project_id" value="<?php echo $project['project_id']; ?>">
                                <td><input type="text" name="title" value="<?php echo htmlspecialchars($project['title']); ?>"></td>
                                <td><input type="text" name="description" value="<?php echo htmlspecialchars($project['description']); ?>"></td>
                                <td><input type="number" name="funding" step="0.01" value="<?php echo $project['funding']; ?>"></td>
                                <td><select name="status">
                                    <option value="Ongoing" <?php echo $project['status'] == 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                                    <option value="Completed" <?php echo $project['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                </select></td>
                                <td><select name="project_priority_level">
                                    <option value="Low" <?php echo $project['project_priority_level'] == 'Low' ? 'selected' : ''; ?>>Low</option>
                                    <option value="Medium" <?php echo $project['project_priority_level'] == 'Medium' ? 'selected' : ''; ?>>Medium</option>
                                    <option value="High" <?php echo $project['project_priority_level'] == 'High' ? 'selected' : ''; ?>>High</option>
                                </select></td>
                                <td><input type="text" name="assigned_to" value="<?php echo htmlspecialchars($project['assigned_to']); ?>"></td>
                                <td>
                                    <input type="submit" name="action" value="Update"><input type="submit" name="action" value="Delete">
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No equipment found in the inventory.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
