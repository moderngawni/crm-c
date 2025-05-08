<?php
// index.php - الملف الرئيسي لنظام CRM
session_start();

$required_files = ['config/config.php', 'config/database.php', 'includes/functions.php', 'includes/auth.php'];
foreach ($required_files as $file) {
    if (!file_exists($file)) {
        die("خطأ: ملف الإعدادات '$file' غير موجود.");
    }
    require_once $file;
}

try {
    $db = Database::getInstance();
} catch (Exception $e) {
    error_log("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
    die("خطأ في الاتصال بقاعدة البيانات. يرجى المحاولة لاحقًا.");
}

if (isLoggedIn() && isSessionExpired()) {
    logoutUser();
    setAlert('انتهت مهلة الجلسة. يرجى تسجيل الدخول مرة أخرى.', 'warning');
    redirect('login.php');
}

if (isLoggedIn()) {
    updateLastActivity();
}

$page = isset($_GET['page']) ? sanitize($_GET['page']) : 'dashboard';

if (!isLoggedIn() && $page !== 'login') {
    redirect('login.php');
}

$restricted_pages = [
    'users' => ['manage_users'],
    'campaigns' => ['manage_campaigns'],
    'leads' => ['manage_leads'],
    'analytics' => ['view_analytics']
];

if (isLoggedIn() && isset($restricted_pages[$page])) {
    if (!checkPermissions($restricted_pages[$page])) {
        setAlert('ليس لديك صلاحية للوصول إلى هذه الصفحة.', 'danger');
        redirect('index.php?page=dashboard');
    }
}

if (file_exists('templates/header.php')) {
    include 'templates/header.php';
} else {
    die("خطأ: ملف القالب 'header.php' غير موجود.");
}

if (isLoggedIn() && file_exists('templates/sidebar.php')) {
    include 'templates/sidebar.php';
}

if (function_exists('displayAlerts')) {
    echo displayAlerts();
}

$valid_pages = ['dashboard', 'users', 'campaigns', 'leads', 'analytics', 'profile', 'settings'];
if (in_array($page, $valid_pages)) {
    $page_file = "modules/{$page}/index.php";
    if (file_exists($page_file)) {
        include $page_file;
    } else {
        echo '<div class="alert alert-danger">الصفحة المطلوبة غير موجودة.</div>';
    }
} else {
    echo '<div class="alert alert-danger">الصفحة المطلوبة غير صالحة.</div>';
}

if (file_exists('templates/footer.php')) {
    include 'templates/footer.php';
} else {
    die("خطأ: ملف القالب 'footer.php' غير موجود.");
}
?>