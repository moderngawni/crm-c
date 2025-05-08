<?php
// modules/campaigns/index.php - إدارة الحملات
$page_title = 'الحملات';
$db = Database::getInstance();

// Define the generateCsrfToken function if not already defined
if (!function_exists('generateCsrfToken')) {
    function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_campaign') {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        setAlert('خطأ في التحقق من النموذج.', 'danger');
        redirect('index.php?page=campaigns');
    }

    $data = [
        'name' => sanitize($_POST['name']),
        'sector_id' => !empty($_POST['sector_id']) ? (int)$_POST['sector_id'] : null,
        'status' => sanitize($_POST['status']),
        'start_date' => !empty($_POST['start_date']) ? $_POST['start_date'] : null,
        'end_date' => !empty($_POST['end_date']) ? $_POST['end_date'] : null,
        'budget' => !empty($_POST['budget']) ? (float)$_POST['budget'] : null,
        'owner_id' => (int)$_SESSION['user_id'],
        'description' => sanitize($_POST['description'])
    ];

    try {
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $columns = implode(', ', array_keys($data));
        $values = array_values($data);
        $sql = "INSERT INTO campaigns ($columns) VALUES ($placeholders)";
        $stmt = $db->getConnection()->prepare($sql);
        $stmt->execute($values);
        setAlert('تم إضافة الحملة بنجاح.', 'success');
        redirect('index.php?page=campaigns');
    } catch (Exception $e) {
        setAlert('خطأ أثناء إضافة الحملة: ' . $e->getMessage(), 'danger');
    }
}

$campaigns = $db->getRows("SELECT c.*, s.name as sector_name, u.username as owner_name 
                           FROM campaigns c 
                           LEFT JOIN sectors s ON c.sector_id = s.id 
                           LEFT JOIN users u ON c.owner_id = u.id");
$sectors = $db->getRows("SELECT * FROM sectors");
?>
<div class="container-fluid">
    <h1 class="mt-4"><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></h1>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addCampaignModal">إضافة حملة</button>
    <?php echo displayAlerts(); ?>
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>القطاع</th>
                        <th>الحالة</th>
                        <th>تاريخ البدء</th>
                        <th>تاريخ الانتهاء</th>
                        <th>الميزانية</th>
                        <th>المالك</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($campaigns as $campaign): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($campaign['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($campaign['sector_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($campaign['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($campaign['start_date'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($campaign['end_date'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($campaign['budget'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($campaign['owner_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal لإضافة حملة -->
    <div class="modal fade" id="addCampaignModal" tabindex="-1" aria-labelledby="addCampaignModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCampaignModalLabel">إضافة حملة جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=campaigns">
                    <input type="hidden" name="action" value="add_campaign">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">اسم الحملة</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="sector_id" class="form-label">القطاع</label>
                            <select class="form-control select2" id="sector_id" name="sector_id">
                                <option value="">اختر قطاع</option>
                                <?php foreach ($sectors as $sector): ?>
                                <option value="<?php echo $sector['id']; ?>"><?php echo htmlspecialchars($sector['name'], ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">الحالة</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="planning">تخطيط</option>
                                <option value="active">نشط</option>
                                <option value="completed">مكتمل</option>
                                <option value="on_hold">معلق</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="start_date" class="form-label">تاريخ البدء</label>
                            <input type="text" class="form-control datepicker" id="start_date" name="start_date">
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">تاريخ الانتهاء</label>
                            <input type="text" class="form-control datepicker" id="end_date" name="end_date">
                        </div>
                        <div class="mb-3">
                            <label for="budget" class="form-label">الميزانية</label>
                            <input type="number" class="form-control" id="budget" name="budget" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف</label>
                            <textarea class="form-control" id="description" name="description" rows="4"></textarea>
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