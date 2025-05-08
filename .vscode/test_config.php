<?php
if (file_exists('config/config.php')) {
    echo "تم العثور على config.php بنجاح!";
    require_once 'config/config.php';
    echo "<br>اسم الموقع: " . SITE_NAME;
} else {
    echo "لم يتم العثور على config.php.";
}
?>