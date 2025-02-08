<?php
// Start the session to access session variables
session_start();

// Delete the session variable for user ID
unset($_SESSION['user_id']);
unset($_SESSION['user_type']);

// Destroy the entire session
session_destroy();

// Delete the cookie by setting it with a past expiration time
setcookie('remember_token', '', time() - 3600, '/', '', false, true);


// Redirect to the main page (or login page)
$host  = $_SERVER['HTTP_HOST'];
$extra = "index.php"; // Or change this to your login page if needed
header("Location: http://$host/$extra");
exit;
?>
