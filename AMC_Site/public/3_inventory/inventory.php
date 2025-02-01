<?php
session_start();
require 'C:\xampp\htdocs\SWAP_Assignment\AMC_Site\config\database_connection.php';

// Initialize feedback messages
$_SESSION['success'] = '';
$_SESSION['error']   = '';

// Ensure the user is logged in
if (!isset($_SESSION['role'])) {
    header("Location: /SWAP_Assignment/AMC_Site/public/login.php");
    exit();
}

$user_role = $_SESSION['role'];
$user_id   = $_SESSION['user_id'];

// Generate CSRF token if not already available
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Process POST requests for Add, Update, and Delete operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }

    $action = $_POST['action'];

    if ($action === 'Add' && ($user_role === 'Admin' || $user_role === 'Research Assistant')) {
        // Adding new equipment
        $product_name  = trim($_POST['product_name']);
        $quantity      = intval($_POST['quantity']);
        $restock_level = intval($_POST['restock_level']);

        if (!empty($product_name) && $quantity >= 0 && $restock_level >= 0) {
            $stmt = $pdo->prepare("INSERT INTO equipment_inventory (product_name, quantity, restock_level) VALUES (?, ?, ?)");
            $stmt->execute([$product_name, $quantity, $restock_level]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = "New equipment added successfully.";
            } else {
                $_SESSION['error'] = "Error adding equipment.";
            }
        } else {
            $_SESSION['error'] = "Please provide valid inputs for adding equipment.";
        }
    } elseif ($action === 'Update' && ($user_role === 'Admin' || $user_role === 'Research Assistant')) {
        // Updating equipment quantity and restock level
        $equipment_id  = intval($_POST['equipment_id']);
        $quantity      = intval($_POST['quantity']);
        $restock_level = intval($_POST['restock_level']);

        if ($equipment_id > 0 && $quantity >= 0 && $restock_level >= 0) {
            $stmt = $pdo->prepare("UPDATE equipment_inventory SET quantity = ?, restock_level = ? WHERE equipment_id = ?");
            $stmt->execute([$quantity, $restock_level, $equipment_id]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = "Equipment updated successfully.";
            } else {
                $_SESSION['error'] = "No equipment found with the provided ID or no changes made.";
            }
        } else {
            $_SESSION['error'] = "Please provide valid inputs for updating equipment.";
        }
    } elseif ($action === 'Delete' && $user_role === 'Admin') {
        // Deleting equipment (Admin only)
        $equipment_id = intval($_POST['equipment_id']);

        if ($equipment_id > 0) {
            $stmt = $pdo->prepare("DELETE FROM equipment_inventory WHERE equipment_id = ?");
            $stmt->execute([$equipment_id]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = "Equipment deleted successfully.";
            } else {
                $_SESSION['error'] = "No equipment found with the provided ID.";
            }
        } else {
            $_SESSION['error'] = "Please provide a valid equipment ID to delete.";
        }
    }

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Retrieve all equipment records
$stmt = $pdo->prepare("SELECT * FROM equipment_inventory");
$stmt->execute();
$equipment = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Management</title>
    <!-- Using the same CSS scheme as projects.php -->
    <link rel="stylesheet" href="../../assets/styles/projects.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Product+Sans&display=swap">
</head>
<body>
    <!-- Fixed navbar -->
    <div style="position: fixed; top: 0; left: 0; width: 100%; z-index: 1000;">
        <?php require "../../includes/navbar.php"; ?>
    </div>
    <div class="container">
        <!-- Feedback Messages -->
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="success-message">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="error-message">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Equipment Inventory Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Restock Level</th>
                    <th>Actions</th>
                </tr>
                <!-- Add Equipment Row (visible to Admin and Research Assistant) -->
                <?php if ($user_role === 'Admin' || $user_role === 'Research Assistant'): ?>
                <tr>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <td></td>
                        <td>
                            <input type="text" name="product_name" placeholder="Enter product name" required>
                        </td>
                        <td>
                            <input type="number" name="quantity" placeholder="Enter quantity" min="0" required>
                        </td>
                        <td>
                            <input type="number" name="restock_level" placeholder="Enter restock level" min="0" required>
                        </td>
                        <td>
                            <input type="submit" name="action" value="Add" class="btn">
                        </td>
                    </form>
                </tr>
                <?php endif; ?>
            </thead>
            <tbody>
                <?php if (!empty($equipment)): ?>
                    <?php foreach ($equipment as $item): ?>
                        <tr>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="equipment_id" value="<?php echo $item['equipment_id']; ?>">
                                <td><?php echo htmlspecialchars($item['equipment_id']); ?></td>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td>
                                    <input type="number" name="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="0" required>
                                </td>
                                <td>
                                    <input type="number" name="restock_level" value="<?php echo htmlspecialchars($item['restock_level']); ?>" min="0" required>
                                </td>
                                <td>
                                    <div class="button-container">
                                        <?php if ($user_role === 'Admin' || $user_role === 'Research Assistant'): ?>
                                            <input type="submit" name="action" value="Update" class="btn">
                                        <?php endif; ?>
                                        <?php if ($user_role === 'Admin'): ?>
                                            <input type="submit" name="action" value="Delete" class="btn">
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No equipment found in the inventory.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>