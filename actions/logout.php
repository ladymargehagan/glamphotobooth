<?php
/**
 * Logout Action
 * actions/logout.php
 */
require_once __DIR__ . '/../settings/core.php';

session_destroy();
header('Location: ' . SITE_URL . '/index.php');
exit;
?>
