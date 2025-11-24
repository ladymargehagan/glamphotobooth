<?php
/**
 * Logout Handler
 * login/logout.php
 */

session_start();

// Destroy session
session_destroy();

// Delete remember me cookie if exists
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Redirect to home
header('Location: ../index.php');
exit();
?>
