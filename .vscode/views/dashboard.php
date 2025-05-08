<?php
require_once '../config/database.php';

$db = Database::getInstance();
$stmt = $db->prepare("SELECT id, username, email, first_name, last_name, role FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container mt-5">
    <h1 class="text-center">لوحة التحكم</h1>
    <p>مرحبًا <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'المستخدم'; ?>!</p>
    <h2>قائمة المستخدمين</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>اسم المستخدم</th>
                <th>البريد الإلكتروني</th>
                <th>الاسم الأول</th>
                <th>الاسم الأخير</th>
                <th>الدور</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <ul>
        <li><a href="?page=clients">إدارة العملاء</a></li>
        <li><a href="?page=appointments">إدارة المواعيد</a></li>
        <li><a href="?page=logout">تسجيل الخروج</a></li>
    </ul>
</div>