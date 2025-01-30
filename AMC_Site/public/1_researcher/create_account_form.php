<?php
session_start();
require 'C:\xampp\htdocs\SWAP_Assignment\AMC_Site\config\database_connection.php'; // Include database connection file

if (isset($_SESSION['user_id'])) {
    if ((isset($_SESSION['role']) && ($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Researcher'))){
    } else {
        die('Invalid Permissions: Required Admin or Researcher.');
    }
} else {
    die("Invalid user ID.");
}

$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';

try {
    // Fetch all user data
    $stmt = $pdo->prepare("SELECT * FROM user");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching researcher profiles: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="\SWAP_Assignment\AMC_Site\assets\styles\create_account.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Product+Sans&display=swap">
</head>
<body>
    <div class="create-account-container">
        <a href="..\dashboard.php" class="active"><i class="icon">🏠</i> Dashboard</a>
        <h1>Create Account</h1>
        <div class="tab-bar">
            <div class="tab active" data-role="Admin">Admin</div>
            <div class="tab" data-role="Researcher">Researcher</div>
            <div class="tab" data-role="Assistant_Researcher">Assistant Researcher</div>
            <div class="tab-indicator"></div>
        </div>

        <div class="create-account-form">
            <?php if (!empty($error)): ?>
                <div class="error-message" style="color: red;"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="create_account.php" method="POST">
                <!-- Hidden input to help differentiate the INSERT POST and UPDATE POST -->
                <input type="hidden" name="action" value="insert">
                <!-- Hidden Input to Track permission -->
                <input type="hidden" id="role" name="role" value="1" required>
                <noscript>
                    <input type="hidden" name="role" value="3" />
                </noscript>
                    <!-- Name input -->
                    <div class="form-group">
                    <label for="name">Enter your name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your name" required>
                </div>
                <!-- Email input -->
                <div class="form-group">
                    <label for="email">Enter your email address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <!-- Phone Number input -->
                <div class="form-group">
                    <label for="phone">Enter your Phone Number</label>
                    <input id="phone_number" type="tel" name="phone_number" placeholder="Enter your phone number" 
                        pattern="\+?[0-9]{10,15}" title="Please enter a valid phone number (with country code)" required />
                </div>
                <!-- Password input -->
                <div class="form-group">
                    <label for="password">Choose a Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <!-- Confirm Password input -->
                <div class="form-group">
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" required>
                </div>
                <!-- Submit button -->
                <button type="submit" class="create-account-button">Create Account</button>
            </form>
        </div>
    </div>

    <div class="user_profiles_container"> 
        <header class="profile_header">
            <strong>User Profiles</strong>
        </header>

        <section class="user_profiles">
            <table>
                <thead>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Password</th>
                    <th>Role</th>
                    <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['user_id']) ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['phone_number']) ?></td>
                            <td><strong>********</strong></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td class="action_buttons">
                                <!-- Update Button -->
                                <button href="profile_form.php?id=<?= $user['user_id'] ?>" class="update_button">Update</button>
                                <!-- Delete Button -->
                                <?php if ($is_admin): ?>
                                    <button class="delete_button" onclick="confirmDelete(<?= $user['user_id'] ?>)">Delete</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No researchers found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

    </div>


    <script>
        // JavaScript for Tab Bar
        document.addEventListener("DOMContentLoaded", () => {
            const tabs = document.querySelectorAll(".tab");
            const tabIndicator = document.querySelector(".tab-indicator");
            const roleInput = document.getElementById("role");

            tabs.forEach((tab, index) => {
                tab.addEventListener("click", () => {
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove("active"));

                    // Add active class to the clicked tab
                    tab.classList.add("active");

                    // Update tab indicator position
                    tabIndicator.style.transform = `translateX(${index * 100}%)`;

                    // Update hidden input value
                    roleInput.value = tab.dataset.role;
                });
            });
        });
    </script>

    <script>
        // JavaScript to ensure both passwords entered are the same
        document.querySelector("form").addEventListener("submit", function (e) {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm-password").value;

            if (password !== confirmPassword) {
                e.preventDefault(); // Prevent form submission
                alert("Passwords do not match. Please try again.");
            }
        });
    </script>

    <script>
        // JAvaScript to ensure no accidental deletion
        function confirmDelete(user_id) {
            if (confirm("Are you sure you want to delete this researcher?")) {
                window.location.href = "create_account.php?id=" + user_id;
            }
        }
    </script>
</body>
</html>

