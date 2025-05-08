<?php
// modules/users/index.php - إدارة المستخدمين
$page_title = 'المستخدمون';
$db = Database::getInstance();

// معالجة إضافة مستخدم
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_user') {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        setAlert('خطأ في التحقق من النموذج.', 'danger');
        redirect('index.php?page=users');
    }

    $data = [
        'username' => sanitize($_POST['username']),
        'email' => sanitize($_POST['email']),
        'first_name' => sanitize($_POST['first_name']),
        'last_name' => sanitize($_POST['last_name']),
        'role' => sanitize($_POST['role']),
        'password' => password_hash($_POST['password'], PASSWORD_BCRYPT)
    ];

    try {
        $query = "INSERT INTO users (username, email, first_name, last_name, role, password) VALUES (:username, :email, :first_name, :last_name, :role, :password)";
        $stmt = $db->prepare($query);
        $stmt->execute($data);
        setAlert('تم إضافة المستخدم بنجاح.', 'success');
        redirect('index.php?page=users');
    } catch (Exception $e) {
        setAlert('خطأ أثناء إضافة المستخدم: ' . $e->getMessage(), 'danger');
    }
}

// معالجة حذف مستخدم
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_user') {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        setAlert('خطأ في التحقق من النموذج.', 'danger');
        redirect('index.php?page=users');
    }

    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    if ($user_id > 0) {
        try {
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            setAlert('تم حذف المستخدم بنجاح.', 'success');
        } catch (Exception $e) {
            setAlert('خطأ أثناء حذف المستخدم: ' . $e->getMessage(), 'danger');
        }
    } else {
        setAlert('معرف المستخدم غير صالح.', 'danger');
    }
    redirect('index.php?page=users');
}

$stmt = $db->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container-fluid">
    <h1 class="mt-4"><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></h1>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">إضافة مستخدم</button>
    <?php echo displayAlerts(); ?>
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>اسم المستخدم</th>
                        <th>الاسم الأول</th>
                        <th>الاسم الأخير</th>
                        <th>البريد الإلكتروني</th>
                        <th>الدور</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($user['first_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($user['last_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a href="?page=edit_user&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">تعديل</a>
                            <form method="POST" action="index.php?page=users" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">
                                <input type="hidden" name="action" value="delete_user">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal لإضافة مستخدم -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">إضافة مستخدم جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=users">
                    <input type="hidden" name="action" value="add_user">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="username" class="form-label">اسم المستخدم</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="first_name" class="form-label">الاسم الأول</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">الاسم الأخير</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">الدور</label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="admin">مدير</option>
                                <option value="manager">مدير</option>
                                <option value="marketing">تسويق</option>
                                <option value="sales">مبيعات</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                        <button type="submit" class="btn btn-primary">إضافة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>