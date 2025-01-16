<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    // Redirect to login page or show an error
    header("Location: login.php");
    exit();
}

// Retrieve user information from session
$current_user_id = $_SESSION['user_id'];
$current_user_role = $_SESSION['role']; // Expected values: 'Admin', 'Research_Assistant'

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
        $usage_status = trim($_POST['usage_status']);
        $availability = trim($_POST['availability']);

        // For Admins, allow assigning equipment to a Research Assistant
        if ($current_user_role === 'Admin') {
            $assigned_to = intval($_POST['assigned_to']);
            // Optional: Validate if the assigned_to user is a Research Assistant
            // You can add additional checks here
        } else {
            // For Research Assistants, assign equipment to themselves
            $assigned_to = $current_user_id;
        }

        if (!empty($product_name) && !empty($usage_status) && !empty($availability)) {
            // Prepare and bind
            $stmt = $conn->prepare("INSERT INTO equipment_inventory (product_name, usage_status, availability, assigned_to) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $product_name, $usage_status, $availability, $assigned_to);

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
        $usage_status = trim($_POST['usage_status']);
        $availability = trim($_POST['availability']);

        if ($equipment_id > 0 && !empty($usage_status) && !empty($availability)) {
            // Check if the user has permission to update this equipment
            if ($current_user_role === 'Admin') {
                // Admin can update any equipment
                $stmt_check = $conn->prepare("SELECT * FROM equipment_inventory WHERE equipment_id = ?");
                $stmt_check->bind_param("i", $equipment_id);
            } else {
                // Research Assistant can only update their own equipment
                $stmt_check = $conn->prepare("SELECT * FROM equipment_inventory WHERE equipment_id = ? AND assigned_to = ?");
                $stmt_check->bind_param("ii", $equipment_id, $current_user_id);
            }

            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                // Proceed with update
                if ($current_user_role === 'Admin') {
                    $stmt = $conn->prepare("UPDATE equipment_inventory SET usage_status = ?, availability = ? WHERE equipment_id = ?");
                    $stmt->bind_param("ssi", $usage_status, $availability, $equipment_id);
                } else {
                    $stmt = $conn->prepare("UPDATE equipment_inventory SET usage_status = ?, availability = ? WHERE equipment_id = ? AND assigned_to = ?");
                    $stmt->bind_param("ssii", $usage_status, $availability, $equipment_id, $current_user_id);
                }

                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        $_SESSION['success'] = "Equipment updated successfully.";
                    } else {
                        $_SESSION['error'] = "No changes made or equipment not found.";
                    }
                } else {
                    $_SESSION['error'] = "Error updating equipment: " . $stmt->error;
                }

                $stmt->close();
            } else {
                $_SESSION['error'] = "You do not have permission to update this equipment or it does not exist.";
            }

            $stmt_check->close();
        } else {
            $_SESSION['error'] = "Please provide valid inputs for updating equipment.";
        }
    }

    // Handle Delete Equipment
    elseif ($action === 'delete') {
        // Only Admin can delete equipment
        if ($current_user_role !== 'Admin') {
            $_SESSION['error'] = "You do not have permission to delete equipment.";
        } else {
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
    <link rel="stylesheet" href="../assets/styles/inventory.css"> <!-- Ensure this path is correct -->
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

        <!-- Add Equipment Section (Visible to Admin and Research Assistants) -->
        <?php if ($current_user_role === 'Admin' || $current_user_role === 'Research_Assistant'): ?>
            <div class="crud-box">
                <h2>Add Equipment</h2>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="action" value="add">
                    <div class="textbox">
                        <input type="text" name="product_name" placeholder="Enter product name" required>
                    </div>
                    <div class="textbox">
                        <input type="text" name="usage_status" placeholder="Enter usage status" required>
                    </div>
                    <div class="textbox">
                        <input type="text" name="availability" placeholder="Enter availability" required>
                    </div>
                    <?php if ($current_user_role === 'Admin'): ?>
                        <div class="textbox">
                            <label for="assigned_to">Assign to Research Assistant:</label>
                            <select name="assigned_to" required>
                                <option value="">Select Research Assistant</option>
                                <?php
                                    // Fetch all Research Assistants from the users table
                                    $conn = new mysqli('localhost', 'root', '', 'usermanagement');
                                    if ($conn->connect_error) {
                                        die("Connection failed: " . $conn->connect_error);
                                    }

                                    $ra_sql = "SELECT user_id, username FROM users WHERE role = 'Research_Assistant'";
                                    $ra_result = $conn->query($ra_sql);

                                    if ($ra_result->num_rows > 0) {
                                        while ($ra = $ra_result->fetch_assoc()) {
                                            echo "<option value='" . htmlspecialchars($ra['user_id']) . "'>" . htmlspecialchars($ra['username']) . "</option>";
                                        }
                                    } else {
                                        echo "<option value=''>No Research Assistants Available</option>";
                                    }

                                    $conn->close();
                                ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <input type="submit" value="Add Equipment" class="btn">
                </form>
            </div>
        <?php endif; ?>

        <!-- Equipment Inventory Section -->
        <div class="crud-box">
            <h2>Equipment Inventory</h2>
            <?php
                // Re-establish database connection
                $conn = new mysqli('localhost', 'root', '', 'usermanagement');
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Determine what equipment to display based on user role
                if ($current_user_role === 'Admin') {
                    // Admin can view all equipment or filter by Research Assistant
                    if (isset($_GET['ra_id'])) {
                        $ra_id = intval($_GET['ra_id']);
                        $sql = "SELECT e.*, u.username FROM equipment_inventory e LEFT JOIN users u ON e.assigned_to = u.user_id WHERE e.assigned_to = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $ra_id);
                    } else {
                        // Fetch all equipment with assigned user
                        $sql = "SELECT e.*, u.username FROM equipment_inventory e LEFT JOIN users u ON e.assigned_to = u.user_id";
                        $stmt = $conn->prepare($sql);
                    }
                } else {
                    // Research Assistants can only view their own equipment
                    $sql = "SELECT e.*, u.username FROM equipment_inventory e LEFT JOIN users u ON e.assigned_to = u.user_id WHERE e.assigned_to = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $current_user_id);
                }

                // Execute and get results
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    echo "<table class='equipment-table'>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product Name</th>
                                    <th>Usage Status</th>
                                    <th>Availability</th>
                                    <th>Assigned To</th>";
                    if ($current_user_role === 'Admin') {
                        echo "<th>Actions</th>";
                    }
                    echo "</tr>
                            </thead>
                            <tbody>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row["equipment_id"]) . "</td>
                                <td>" . htmlspecialchars($row["product_name"]) . "</td>
                                <td>" . htmlspecialchars($row["usage_status"]) . "</td>
                                <td>" . htmlspecialchars($row["availability"]) . "</td>
                                <td>" . htmlspecialchars($row["username"] ?? 'Unassigned') . "</td>";
                        if ($current_user_role === 'Admin') {
                            echo "<td>
                                    <!-- Update Button (Optional: Implement a modal or separate form for updating) -->
                                    <form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' style='display:inline;'>
                                        <input type='hidden' name='action' value='update'>
                                        <input type='hidden' name='equipment_id' value='" . htmlspecialchars($row["equipment_id"]) . "'>
                                        <input type='text' name='usage_status' placeholder='New Status' required>
                                        <input type='text' name='availability' placeholder='New Availability' required>
                                        <input type='submit' value='Update' class='btn'>
                                    </form>
                                    <!-- Delete Button -->
                                    <form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' style='display:inline;'>
                                        <input type='hidden' name='action' value='delete'>
                                        <input type='hidden' name='equipment_id' value='" . htmlspecialchars($row["equipment_id"]) . "'>
                                        <input type='submit' value='Delete' class='btn' onclick=\"return confirm('Are you sure you want to delete this equipment?');\">
                                    </form>
                                  </td>";
                        }
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>No equipment found in the inventory.</p>";
                }

                $stmt->close();
                $conn->close();
            ?>
        </div>

        <!-- Optional: Admin Filter to View Equipment by Research Assistant -->
        <?php if ($current_user_role === 'Admin'): ?>
            <div class="crud-box">
                <h2>Filter Equipment by Research Assistant</h2>
                <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="textbox">
                        <label for="ra_id">Select Research Assistant:</label>
                        <select name="ra_id" required>
                            <option value="">Select Research Assistant</option>
                            <?php
                                // Fetch all Research Assistants from the users table
                                $conn = new mysqli('localhost', 'root', '', 'usermanagement');
                                if ($conn->connect_error) {
                                    die("Connection failed: " . $conn->connect_error);
                                }

                                $ra_sql = "SELECT user_id, username FROM users WHERE role = 'Research_Assistant'";
                                $ra_result = $conn->query($ra_sql);

                                if ($ra_result->num_rows > 0) {
                                    while ($ra = $ra_result->fetch_assoc()) {
                                        // Preserve the selected RA after submission
                                        $selected = (isset($_GET['ra_id']) && $_GET['ra_id'] == $ra['user_id']) ? 'selected' : '';
                                        echo "<option value='" . htmlspecialchars($ra['user_id']) . "' $selected>" . htmlspecialchars($ra['username']) . "</option>";
                                    }
                                } else {
                                    echo "<option value=''>No Research Assistants Available</option>";
                                }

                                $conn->close();
                            ?>
                        </select>
                    </div>
                    <input type="submit" value="Filter" class="btn">
                    <?php if (isset($_GET['ra_id']) && !empty($_GET['ra_id'])): ?>
                        <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="btn">Clear Filter</a>
                    <?php endif; ?>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
