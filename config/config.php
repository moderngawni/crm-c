<?php
// config/config.php - الإعدادات العامة
define('SITE_NAME', 'Candel Spa CRM');
define('SESSION_TIMEOUT', 3600); // 1 ساعة بالثواني
define('BASE_URL', 'http://localhost/candelspa');
define('DEBUG', true);

if (DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}
?>