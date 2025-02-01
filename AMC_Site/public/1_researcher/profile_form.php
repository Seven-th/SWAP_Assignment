<?php
require 'C:\xampp\htdocs\SWAP_Assignment\AMC_Site\config\database_connection.php'; // Include database connection file

// Check if an ID is provided in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID.");
}

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user_id = intval($_GET['id']);
$error = "";
$success = "";

// Fetch user data from the user table
try {
    $stmt = $pdo->prepare("
        SELECT * FROM user WHERE user_id = :id
    ");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found.");
    }
} catch (PDOException $e) {
    die("Error fetching user details: " . $e->getMessage());
}

// Handle form submission for updating user details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role = trim($_POST['role']); // Now using ENUM

    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // Validate the current password if new password is being set
    if (!empty($new_password) || !empty($confirm_new_password)) {
        if (!password_verify($current_password, $user['password']) || $current_password === $user['password']) {
            $error = "Current password is incorrect.";
        } elseif ($new_password !== $confirm_new_password) {
            $error = "New passwords do not match.";
        } else {
            // Hash the new password
            $password = password_hash($new_password, PASSWORD_DEFAULT);
        }
    } else {
        // No new password provided, keep the existing password
        $password = $user['password'];
    }

    try {
            $update_stmt = $pdo->prepare("
                UPDATE user 
                SET name = :name, email = :email, phone_number = :phone, password = :password, role = :role
                WHERE user_id = :id
            ");
            $update_stmt->execute([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'password' => $password,
                'role' => $role,
                'id' => $user_id
            ]);

            $success = "User details updated successfully!";
            // Refresh user data
            header("Refresh:2; url=create_account_form.php");
       
    } catch (PDOException $e) {
        $error = "Error updating user: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Create Account</title>
        <link rel="stylesheet" href="\SWAP_Assignment\AMC_Site\assets\styles\profile.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Product+Sans&display=swap">
    </head>
    <body>
        <div class="profile-container">
            <a href="..\1_researcher\create_account_form.php" class="active"><i class="icon">🏠</i> Dashboard</a>
            <h1>User Profile</h1>

            <?php if (!empty($error)): ?>
                <p class="error"><?= htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <p class="success"><?= htmlspecialchars($success); ?></p>
            <?php endif; ?>

            <form class="update-form" action="profile_form.php?id=<?= htmlspecialchars($user['user_id']) ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="user_id" value="1">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone_number']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="current_password">Enter your current password (Leave blank to keep current password)</label>
                    <input type="password" id="current_password" name="current_password">
                </div>
                <div class="form-group">
                    <label for="new_password">Password (Leave blank to keep current password)</label>
                    <input type="password" id="new_password" name="new_password">
                </div>
                <div class="form-group">
                    <label for="confirm_new_password">Confirm New Password (Leave blank to keep current password)</label>
                    <input type="password" id="confirm_new_password" name="confirm_new_password" placeholder="Confirm your new password">
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="Admin" <?= $user['role'] == 'Admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="Researcher" <?= $user['role'] == 'Researcher' ? 'selected' : '' ?>>Researcher</option>
                        <option value="Research Assistant" <?= $user['role'] == 'Research Assistant' ? 'selected' : '' ?>>Research Assistant</option>
                    </select>
                </div>

                <button type="submit" class="update-btn">Update Profile</button>
            </form>
        </div>
    </body>
</html>