<?php
// Start the session
session_start();

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to the login page
header('Location: http://localhost:8080/paypointway/login.html');
exit;
?>
