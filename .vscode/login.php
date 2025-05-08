<?php
// login.php - صفحة تسجيل الدخول
session_start();

$required_files = ['config/config.php', 'config/database.php', 'includes/functions.php', 'includes/auth.php'];
foreach ($required_files as $file) {
    if (!file_exists($file)) {
        die("خطأ: ملف الإعدادات '$file' غير موجود.");
    }
    require_once $file;
}

if (isLoggedIn()) {
    redirect('index.php?page=dashboard');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? sanitize($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($password)) {
        setAlert('يرجى ملء جميع الحقول.', 'danger');
    } else {
        try {
            $db = Database::getInstance();
            $user = $db->getRow("SELECT * FROM users WHERE username = ? LIMIT 1", [$username]);

            if ($user && password_verify($password, $user['password'])) {
                loginUser($user['id'], $user['role']);
                setAlert('تم تسجيل الدخول بنجاح!', 'success');
                redirect('index.php?page=dashboard');
            } else {
                setAlert('اسم المستخدم أو كلمة المرور غير صحيحة.', 'danger');
            }
        } catch (Exception $e) {
            error_log("خطأ في تسجيل الدخول: " . $e->getMessage());
            setAlert('حدث خطأ أثناء تسجيل الدخول. يرجى المحاولة لاحقًا.', 'danger');
        }
    }
}

$page_title = 'تسجيل الدخول';
include 'templates/header.php';
echo displayAlerts();
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="card-title text-center">تسجيل الدخول</h3>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">اسم المستخدم</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">تسجيل الدخول</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>