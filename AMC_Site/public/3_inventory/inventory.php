<?php
session_start();
require 'C:\xampp\htdocs\SWAP_Assignment\AMC_Site\config\database_connection.php'; // Ensure this matches your setup

// Initialize variables for feedback messages
$_SESSION['success'] = '';
$_SESSION['error'] = '';

// Role-based access control
if (!isset($_SESSION['role'])) {
    header("Location: /SWAP_Assignment/AMC_Site/public/login.php");
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

    // Handle Add Equipment (Admin and Research Assistant)
    if ($action === 'add' && ($user_role === 'Admin' || $user_role === 'Research Assistant')) {
        $product_name = trim($_POST['product_name']);
        $quantity = intval($_POST['quantity']);
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
    }

    // Handle Update Equipment (Admin and Research Assistant)
    elseif ($action === 'update' && ($user_role === 'Admin' || $user_role === 'Research Assistant')) {
        $equipment_id = intval($_POST['equipment_id']);
        $quantity = intval($_POST['quantity']);
        $restock_level = intval($_POST['restock_level']);

        if ($equipment_id > 0 && $quantity >= 0 && $restock_level >= 0) {
            $stmt = $pdo->prepare("UPDATE equipment_inventory SET quantity = ?, restock_level = ? WHERE equipment_id = ?");
            $stmt->execute([$quantity, $restock_level, $equipment_id]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = "Equipment updated successfully.";
            } else {
                $_SESSION['error'] = "No equipment found with the provided ID or access denied.";
            }
        } else {
            $_SESSION['error'] = "Please provide valid inputs for updating equipment.";
        }
    }

    // Handle Delete Equipment (Admin only)
    elseif ($action === 'delete' && $user_role === 'Admin') {
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

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch equipment inventory without filtering by assigned user
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
    <link rel="stylesheet" href="../../assets/styles/inventory.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Product+Sans&display=swap">
</head>
<body>
    <div class="container">
        <div class="back-to-dashboard">
            <a href='/SWAP_Assignment/AMC_Site/public/dashboard.php' class='btn'>Back to Dashboard</a>
        </div>
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

        <!-- Add Equipment Section -->
        <?php if ($user_role === 'Admin' || $user_role === 'Research Assistant'): ?>
            <div class="crud-box">
                <h2>Add Equipment</h2>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="textbox">
                        <input type="text" name="product_name" placeholder="Enter product name" required>
                    </div>
                    <div class="textbox">
                        <input type="number" name="quantity" placeholder="Enter quantity" min="0" required>
                    </div>
                    <div class="textbox">
                        <input type="number" name="restock_level" placeholder="Enter restock level" min="0" required>
                    </div>
                    <input type="submit" value="Add Equipment" class="btn">
                </form>
            </div>
        <?php endif; ?>

        <!-- Equipment Inventory Section -->
        <div class="crud-box">
            <h2>Equipment Inventory</h2>
            <?php if (!empty($equipment)): ?>
                <table class='equipment-table'>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Restock Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($equipment as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['equipment_id']); ?></td>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($item['restock_level']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No equipment found in the inventory.</p>
            <?php endif; ?>
        </div>

        <!-- Update Equipment Section -->
        <?php if ($user_role === 'Admin' || $user_role === 'Research Assistant'): ?>
            <div class="crud-box">
                <h2>Update Equipment</h2>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="textbox">
                        <input type="number" name="equipment_id" placeholder="Enter equipment ID" min="1" required>
                    </div>
                    <div class="textbox">
                        <input type="number" name="quantity" placeholder="Enter new quantity" min="0" required>
                    </div>
                    <div class="textbox">
                        <input type="number" name="restock_level" placeholder="Enter new restock level" min="0" required>
                    </div>
                    <input type="submit" value="Update Equipment" class="btn">
                </form>
            </div>
        <?php endif; ?>

        <!-- Delete Equipment Section -->
        <?php if ($user_role === 'Admin'): ?>
            <div class="crud-box">
                <h2>Delete Equipment</h2>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="textbox">
                        <input type="number" name="equipment_id" placeholder="Enter equipment ID to delete" min="1" required>
                    </div>
                    <input type="submit" value="Delete Equipment" class="btn">
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
