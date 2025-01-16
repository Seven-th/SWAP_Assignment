<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="../assets/styles/create_account.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Product+Sans&display=swap">
</head>
<body>
    <div class="tab-bar">
        <div class="tab active" data-role="Admin">Admin</div>
        <div class="tab" data-role="Researcher">Researcher</div>
        <div class="tab" data-role="Assistant_Researcher">Assistant Researcher</div>
        <div class="tab-indicator"></div>
    </div>

    <div class="create-account-container">
        <div class="create-account-form">
            <h1>Create Account</h1>
            <form action="create_account_form.php" method="POST">
                <!-- Hidden Input to Track Role -->
                <input type="hidden" id="role" name="role" value="Admin">
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
</body>
</html>
