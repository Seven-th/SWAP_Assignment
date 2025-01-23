<?php
session_start();

// Database connection using mysqli with error handling
$conn = new mysqli('localhost', 'root', '', 'usermanagement');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables for feedback messages
$_SESSION['success'] = '';
$_SESSION['error'] = '';

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    // Determine the action
    $action = $_POST['action'];

    // Handle Add Equipment
    if ($action === 'add') {
        // Validate and sanitize inputs
        $product_name = trim($_POST['product_name']);
        $quantity = intval($_POST['quantity']);
        $restock_level = intval($_POST['restock_level']);

        if (!empty($product_name) && $quantity >= 0 && $restock_level >= 0) {
            // Prepare and bind
            $stmt = $conn->prepare("INSERT INTO equipment_inventory (product_name, quantity, restock_level) VALUES (?, ?, ?)");
            $stmt->bind_param("sii", $product_name, $quantity, $restock_level);

            if ($stmt->execute()) {
                $_SESSION['success'] = "New equipment added successfully.";
            } else {
                $_SESSION['error'] = "Error adding equipment: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $_SESSION['error'] = "Please provide valid inputs for adding equipment.";
        }
    }

    // Handle Update Equipment
    elseif ($action === 'update') {
        // Validate and sanitize inputs
        $equipment_id = intval($_POST['equipment_id']);
        $quantity = intval($_POST['quantity']);
        $restock_level = intval($_POST['restock_level']);

        if ($equipment_id > 0 && $quantity >= 0 && $restock_level >= 0) {
            // Prepare and bind
            $stmt = $conn->prepare("UPDATE equipment_inventory SET quantity = ?, restock_level = ? WHERE equipment_id = ?");
            $stmt->bind_param("iii", $quantity, $restock_level, $equipment_id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $_SESSION['success'] = "Equipment updated successfully.";
                } else {
                    $_SESSION['error'] = "No equipment found with the provided ID.";
                }
            } else {
                $_SESSION['error'] = "Error updating equipment: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $_SESSION['error'] = "Please provide valid inputs for updating equipment.";
        }
    }

    // Handle Delete Equipment
    elseif ($action === 'delete') {
        // Validate and sanitize inputs
        $equipment_id = intval($_POST['equipment_id']);

        if ($equipment_id > 0) {
            // Prepare and bind
            $stmt = $conn->prepare("DELETE FROM equipment_inventory WHERE equipment_id = ?");
            $stmt->bind_param("i", $equipment_id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $_SESSION['success'] = "Equipment deleted successfully.";
                } else {
                    $_SESSION['error'] = "No equipment found with the provided ID.";
                }
            } else {
                $_SESSION['error'] = "Error deleting equipment: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $_SESSION['error'] = "Please provide a valid equipment ID to delete.";
        }
    }

    // Redirect to the same page to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Management</title>
    <link rel="stylesheet" href="../../assets/styles/inventory.css"> <!-- Ensure this path is correct -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Product+Sans&display=swap">
</head>
<body>
    <div class="container">
        <!-- Display Success and Error Messages -->
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="success-message">
                <?php 
                    echo $_SESSION['success']; 
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="error-message">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Add Equipment Section -->
        <div class="crud-box">
            <h2>Add Equipment</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="action" value="add">
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

        <!-- Equipment Inventory Section -->
        <div class="crud-box">
            <h2>Equipment Inventory</h2>
            <?php
                // Re-establish database connection
                $conn = new mysqli('localhost', 'root', '', 'usermanagement');
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $sql = "SELECT * FROM equipment_inventory";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    echo "<table class='equipment-table'>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Restock Level</th>
                                </tr>
                            </thead>
                            <tbody>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row["equipment_id"]) . "</td>
                                <td>" . htmlspecialchars($row["product_name"]) . "</td>
                                <td>" . htmlspecialchars($row["quantity"]) . "</td>
                                <td>" . htmlspecialchars($row["restock_level"]) . "</td>
                              </tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>No equipment found in the inventory.</p>";
                }

                $conn->close();
            ?>
        </div>

        <!-- Update Equipment Section -->
        <div class="crud-box">
            <h2>Update Equipment</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="action" value="update">
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

        <!-- Delete Equipment Section -->
        <div class="crud-box">
            <h2>Delete Equipment</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="action" value="delete">
                <div class="textbox">
                    <input type="number" name="equipment_id" placeholder="Enter equipment ID to delete" min="1" required>
                </div>
                <input type="submit" value="Delete Equipment" class="btn">
            </form>
        </div>
    </div>
</body>
</html>