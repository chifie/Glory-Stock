<?php
// Start the session so we can find the one we want to destroy
session_start();

// Remove all session variables (username, role, user_id)
session_unset();

// Destroy the session entirely
session_destroy();

// Send the user back to the login page
header("Location: login.php");
exit();
?>