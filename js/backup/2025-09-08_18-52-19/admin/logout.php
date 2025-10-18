<?php
/**
 * Admin Panel - Logout
 * NextCode Group - Secure Session Management
 */

define('ADMIN_ACCESS', true);

// Include configurations
require_once 'config/database.php';
require_once 'config/security.php';

// Logout user
$adminSecurity->logout();

// Redirect to login page
header('Location: login.php?logged_out=1');
exit();
?>
