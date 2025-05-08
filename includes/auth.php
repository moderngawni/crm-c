<?php
// includes/auth.php - دوال المصادقة وإدارة الجلسات

/**
 * التحقق من تسجيل دخول المستخدم
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * تسجيل دخول المستخدم
 */
function loginUser($user_id, $username, $role = 'user') {
    // إنشاء جلسة المستخدم
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_role'] = $role;
    $_SESSION['last_activity'] = time();
    $_SESSION['session_token'] = bin2hex(random_bytes(32));
    
    // تسجيل معلومات الاتصال
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $ip_address = getUserIp();
    
    try {
        $db = Database::getInstance();
        $db->execute(
            "INSERT INTO login_history (user_id, ip_address, user_agent, login_time) VALUES (?, ?, ?, ?)",
            [$user_id, $ip_address, $user_agent, getCurrentTime()]
        );
        
        // تحديث آخر تسجيل دخول في جدول المستخدمين
        $db->execute(
            "UPDATE users SET last_login = ? WHERE id = ?",
            [getCurrentTime(), $user_id]
        );
        
        // تسجيل النشاط
        logActivity($user_id, 'login', 'تسجيل الدخول بنجاح');
        
    } catch (Exception $e) {
        error_log("خطأ في تسجيل معلومات الدخول: " . $e->getMessage());
    }
    
    return true;
}

/**
 * تسجيل خروج المستخدم
 */
function logoutUser() {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        try {
            logActivity($user_id, 'logout', 'تسجيل الخروج');
        } catch (Exception $e) {
            error_log("خطأ في تسجيل معلومات الخروج: " . $e->getMessage());
        }
    }
    
    // حذف متغيرات الجلسة
    $_SESSION = [];
    
    // حذف ملف الكوكيز الخاص بمعرف الجلسة
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    
    // تدمير الجلسة
    session_destroy();
    
    return true;
}

/**
 * التحقق مما إذا كانت الجلسة منتهية
 */
function isSessionExpired() {
    $timeout = defined('SESSION_TIMEOUT') ? SESSION_TIMEOUT : 3600; // افتراضي 1 ساعة
    
    if (!isset($_SESSION['last_activity'])) {
        return true; // تعتبر منتهية إذا لم تكن موجودة
    }
    
    $inactive_time = time() - $_SESSION['last_activity'];
    
    return $inactive_time > $timeout;
}

/**
 * تحديث وقت آخر نشاط للمستخدم
 */
function updateLastActivity() {
    $_SESSION['last_activity'] = time();
}

/**
 * إنشاء كلمة مرور مشفرة
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * التحقق من كلمة المرور
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * الحصول على معلومات المستخدم الحالي
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT id, username, email, first_name, last_name, role, avatar, created_at, last_login 
            FROM users 
            WHERE id = ? 
            LIMIT 1"
        );
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $user;
    } catch (Exception $e) {
        error_log("خطأ في الحصول على معلومات المستخدم: " . $e->getMessage());
        return null;
    }
}
?>