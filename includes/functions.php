<?php
// includes/functions.php - الدوال المساعدة العامة

/**
 * تنظيف المدخلات لمنع هجمات الحقن
 */
function sanitize($input) {
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            $input[$key] = sanitize($value);
        }
        return $input;
    }
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

/**
 * إعادة توجيه المستخدم إلى صفحة أخرى
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * إضافة تنبيه للمستخدم
 */
function setAlert($message, $type = 'info') {
    if (!isset($_SESSION['alerts'])) {
        $_SESSION['alerts'] = [];
    }
    $_SESSION['alerts'][] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * عرض التنبيهات المخزنة وحذفها
 */
function displayAlerts() {
    $output = '';
    if (isset($_SESSION['alerts']) && count($_SESSION['alerts']) > 0) {
        foreach ($_SESSION['alerts'] as $alert) {
            $output .= '<div class="alert alert-' . $alert['type'] . ' alert-dismissible fade show" role="alert">';
            $output .= $alert['message'];
            $output .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>';
            $output .= '</div>';
        }
        unset($_SESSION['alerts']);
    }
    return $output;
}

/**
 * التحقق من تاريخ معين وتنسيقه
 */
function formatDate($date, $format = 'Y-m-d H:i:s') {
    if (empty($date)) {
        return '';
    }
    $dateTime = new DateTime($date);
    return $dateTime->format($format);
}

/**
 * الحصول على الوقت الحالي
 */
function getCurrentTime() {
    return date('Y-m-d H:i:s');
}

/**
 * توليد رقم تعريفي فريد
 */
function generateUniqueId($prefix = '') {
    return uniqid($prefix) . bin2hex(random_bytes(8));
}

/**
 * الحصول على عنوان IP للمستخدم
 */
function getUserIp() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

/**
 * تسجيل النشاط في ملف السجل
 */
function logActivity($userId, $action, $details = '') {
    try {
        $db = Database::getInstance();
        $db->execute(
            "INSERT INTO activity_logs (user_id, action, details, ip_address, created_at) VALUES (?, ?, ?, ?, ?)",
            [$userId, $action, $details, getUserIp(), getCurrentTime()]
        );
    } catch (Exception $e) {
        error_log("خطأ في تسجيل النشاط: " . $e->getMessage());
    }
}

/**
 * التحقق من وجود صلاحيات للمستخدم
 */
function checkPermissions($required_permissions) {
    if (!isset($_SESSION['user_role'])) {
        return false;
    }
    
    $role = $_SESSION['user_role'];
    
    // المدير لديه جميع الصلاحيات
    if ($role === 'admin') {
        return true;
    }
    
    // يمكن إضافة منطق آخر للتحقق من الصلاحيات حسب الدور
    try {
        $db = Database::getInstance();
        
        if (is_array($required_permissions)) {
            foreach ($required_permissions as $permission) {
                $has_permission = $db->getRow(
                    "SELECT * FROM role_permissions WHERE role = ? AND permission = ? LIMIT 1",
                    [$role, $permission]
                );
                
                if (!$has_permission) {
                    return false;
                }
            }
            return true;
        } else {
            $has_permission = $db->getRow(
                "SELECT * FROM role_permissions WHERE role = ? AND permission = ? LIMIT 1",
                [$role, $required_permissions]
            );
            
            return (bool) $has_permission;
        }
    } catch (Exception $e) {
        error_log("خطأ في التحقق من الصلاحيات: " . $e->getMessage());
        return false;
    }
}