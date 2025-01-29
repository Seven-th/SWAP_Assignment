<?php
session_start();
session_unset();  // Unset all session variables
session_destroy(); // Destroy session
header("Location: login_form.php?msg=You have been logged out successfully");
exit;
?>
