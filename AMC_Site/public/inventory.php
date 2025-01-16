<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'usermanagement');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add Equipment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_name'])) {
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $restock_level = $_POST['restock_level'];

    $sql = "INSERT INTO equipment_inventory (product_name, quantity, restock_level) VALUES ('$product_name', $quantity, $restock_level)";
    if ($conn->query($sql) === TRUE) {
        echo "New equipment added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle Update Equipment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['equipment_id'])) {
    $equipment_id = $_POST['equipment_id'];
    $quantity = $_POST['quantity'];
    $restock_level = $_POST['restock_level'];

    $sql = "UPDATE equipment_inventory SET quantity=$quantity, restock_level=$restock_level WHERE equipment_id=$equipment_id";
    if ($conn->query($sql) === TRUE) {
        echo "Equipment updated successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle Delete Equipment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['equipment_id'])) {
    $equipment_id = $_POST['equipment_id'];

    $sql = "DELETE FROM equipment_inventory WHERE equipment_id=$equipment_id";
    if ($conn->query($sql) === TRUE) {
        echo "Equipment deleted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Management</title>
    <link rel="stylesheet" href="C:\xampp\htdocs\SWAP_Assignment\AMC_Site\assets\styles\inventory.css"> <!-- Ensure this path is correct -->
</head>
<body>
    <div class="container">
        <!-- Add Equipment Section -->
        <div class="crud-box">
            <h2>Add Equipment</h2>
            <form method="post">
                <div class="textbox">
                    <input type="text" name="product_name" placeholder="Enter product name" required>
                </div>
                <div class="textbox">
                    <input type="number" name="quantity" placeholder="Enter quantity" required>
                </div>
                <div class="textbox">
                    <input type="number" name="restock_level" placeholder="Enter restock level" required>
                </div>
                <input type="submit" value="Add Equipment" class="btn">
            </form>
        </div>

        <!-- Equipment Inventory Section -->
        <div class="crud-box">
            <h2>Equipment Inventory</h2>
            <?php
                $conn = new mysqli('localhost', 'root', '', 'usermanagement');
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $sql = "SELECT * FROM equipment_inventory";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    echo "<table class='equipment-table'>
                            <tr><th>ID</th><th>Product Name</th><th>Quantity</th><th>Restock Level</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr><td>" . $row["equipment_id"] . "</td><td>" . $row["product_name"] . "</td><td>" . $row["quantity"] . "</td><td>" . $row["restock_level"] . "</td></tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No results found.</p>";
                }

                $conn->close();
            ?>
        </div>

        <!-- Update Equipment Section -->
        <div class="crud-box">
            <h2>Update Equipment</h2>
            <form method="post">
                <div class="textbox">
                    <input type="number" name="equipment_id" placeholder="Enter equipment ID" required>
                </div>
                <div class="textbox">
                    <input type="number" name="quantity" placeholder="Enter new quantity" required>
                </div>
                <div class="textbox">
                    <input type="number" name="restock_level" placeholder="Enter new restock level" required>
                </div>
                <input type="submit" value="Update Equipment" class="btn">
            </form>
        </div>

        <!-- Delete Equipment Section -->
        <div class="crud-box">
            <h2>Delete Equipment</h2>
            <form method="post">
                <div class="textbox">
                    <input type="number" name="equipment_id" placeholder="Enter equipment ID to delete" required>
                </div>
                <input type="submit" value="Delete Equipment" class="btn">
            </form>
        </div>
    </div>
</body>
</html>