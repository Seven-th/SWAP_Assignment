<!-- Copyright Info
Ng Yong Kiat Shawn
Created 28 Nov 2024 -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Product+Sans&display=swap">
    <style>
        /* General Reset */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Product Sans', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f4f4f9;
        }

        /* Tab Bar Styles */
        .tab-bar {
            display: flex;
            position: relative;
            width: 100%;
            max-width: 400px;
            margin-bottom: 20px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .tab {
            flex: 1;
            text-align: center;
            padding: 10px 15px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            color: #555;
            transition: color 0.3s;
        }

        .tab:hover {
            color: #007bff;
        }

        .tab-indicator {
            position: absolute;
            bottom: 0;
            height: 4px;
            width: 33.33%;
            background-color: #007bff;
            transition: transform 0.3s ease-in-out;
        }

        /* Active Tab */
        .tab.active {
            color: #007bff;
        }

        /* Form Styles */
        .create-account-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .create-account-form h1 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .create-account-button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .create-account-button:hover {
            background-color: #0056b3;
        }

        .form-footer {
            text-align: center;
            margin-top: 15px;
        }

        .form-footer a {
            color: #007bff;
            text-decoration: none;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Tab Bar -->
    <div class="tab-bar">
        <div class="tab active" data-role="Admin">Admin</div>
        <div class="tab" data-role="Researcher">Researcher</div>
        <div class="tab" data-role="Assistant_Researcher">Assistant Researcher</div>
        <div class="tab-indicator"></div>
    </div>

    <div class="create-account-container">
        <div class="create-account-form">
            <h1>Create Account</h1>
            <form action="signup.php" method="POST">
                <!-- Hidden Input to Track Role -->
                <input type="hidden" id="role" name="role" value="Admin">
                
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
            <!-- Footer Link -->
            <div class="form-footer">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
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
