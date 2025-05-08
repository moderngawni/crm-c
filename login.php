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
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            loginUser($user['id'], $user['username'], $user['role']);
            setAlert('تم تسجيل الدخول بنجاح!', 'success');
            redirect('index.php?page=dashboard');
        } else {
            setAlert('اسم المستخدم أو كلمة المرور غير صحيحة.', 'danger');
        }
    }
}

include 'templates/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center">تسجيل الدخول</h2>
            <?php echo displayAlerts(); ?>
            <form method="POST" action="">
                <div class="form-group mb-3">
                    <label for="username">اسم المستخدم:</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="form-group mb-3">
                    <label for="password">كلمة المرور:</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">تسجيل الدخول</button>
            </form>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>