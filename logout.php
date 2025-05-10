<?php
// logout.php
session_start();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session.
if (session_destroy()) {
    // Redirect to login page
    header("location: index.php");
    exit;
} else {
    // Handle error if session couldn't be destroyed (unlikely)
    echo "Error destroying session. Please try closing your browser.";
    // You might want to log this error as well
    error_log("Failed to destroy session for user.");
}
?>