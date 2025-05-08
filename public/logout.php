<?php
// logout.php - تسجيل الخروج
session_start();
require_once 'includes/auth.php';
logoutUser();
redirect('login.php');
?>