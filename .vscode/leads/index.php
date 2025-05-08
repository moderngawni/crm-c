<?php
// modules/leads/index.php - إدارة العملاء المحتملين
$page_title = 'العملاء المحتملين';
$db = Database::getInstance();

/**
 * Verifies the CSRF token to prevent cross-site request forgery attacks.
 *
 * @param string $token The CSRF token to verify.
 * @return bool True if the token is valid, false otherwise.
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_lead') {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        setAlert('خطأ في التحقق من النموذج.', 'danger');
        redirect('index.php?page=leads');
    }

    $data = [
        'contact_name' => sanitize($_POST['contact_name']),
        'email' => sanitize($_POST['email']),
        'phone' => sanitize($_POST['phone']),
        'sector_id' => !empty($_POST['sector_id']) ? (int)$_POST['sector_id'] : null,
        'city' => sanitize($_POST['city']),
        'country' => sanitize($_POST['country']),
        'stage' => sanitize($_POST['stage']),
        'source' => sanitize($_POST['source']),
        'source_campaign_id' => !empty($_POST['source_campaign_id']) ? (int)$_POST['source_campaign_id'] : null,
        'assigned_to' => (int)$_SESSION['user_id']
    ];

    try {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO leads ($columns) VALUES ($placeholders)";
        $stmt = $db->getConnection()->prepare($sql);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();
        setAlert('تم إضافة العميل المحتمل بنجاح.', 'success');
        redirect('index.php?page=leads');
    } catch (Exception $e) {
        setAlert('خطأ أثناء إضافة العميل: ' . $e->getMessage(), 'danger');
    }
}

$leads = $db->getRows("SELECT l.*, s.name as sector_name, c.name as campaign_name, u.username as assigned_to_name 
                       FROM leads l 
                       LEFT JOIN sectors s ON l.sector_id = s.id 
                       LEFT JOIN campaigns c ON l.source_campaign_id = c.id 
                       LEFT JOIN users u ON l.assigned_to = u.id");
$sectors = $db->getRows("SELECT * FROM sectors");
$campaigns = $db->getRows("SELECT * FROM campaigns");
?>
<div class="container-fluid">
    <h1 class="mt-4"><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></h1>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addLeadModal">إضافة عميل محتمل</button>
    <?php echo displayAlerts(); ?>
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>اسم الاتصال</th>
                        <th>البريد الإلكتروني</th>
                        <th>الهاتف</th>
                        <th>القطاع</th>
                        <th>المدينة</th>
                        <th>الدولة</th>
                        <th>المرحلة</th>
                        <th>المصدر</th>
                        <th>الحملة</th>
                        <th>تم التكليف إلى</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leads as $lead): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($lead['contact_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($lead['email'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($lead['phone'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($lead['sector_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($lead['city'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($lead['country'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($lead['stage'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($lead['source'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($lead['campaign_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($lead['assigned_to_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal لإضافة عميل محتمل -->
    <div class="modal fade" id="addLeadModal" tabindex="-1" aria-labelledby="addLeadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLeadModalLabel">إضافة عميل محتمل</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?page=leads">
                    <input type="hidden" name="action" value="add_lead">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="contact_name" class="form-label">اسم الاتصال</label>
                            <input type="text" class="form-control" id="contact_name" name="contact_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">الهاتف</label>
                            <input type="text" class="form-control" id="phone" name="phone">
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
                            <label for="city" class="form-label">المدينة</label>
                            <input type="text" class="form-control" id="city" name="city">
                        </div>
                        <div class="mb-3">
                            <label for="country" class="form-label">الدولة</label>
                            <input type="text" class="form-control" id="country" name="country">
                        </div>
                        <div class="mb-3">
                            <label for="stage" class="form-label">المرحلة</label>
                            <select class="form-control" id="stage" name="stage" required>
                                <option value="new">جديد</option>
                                <option value="contacted">تم التواصل</option>
                                <option value="qualified">مؤهل</option>
                                <option value="proposal">عرض</option>
                                <option value="negotiation">تفاوض</option>
                                <option value="won">فاز</option>
                                <option value="lost">خسر</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="source" class="form-label">المصدر</label>
                            <select class="form-control" id="source" name="source" required>
                                <option value="website">موقع إلكتروني</option>
                                <option value="referral">إحالة</option>
                                <option value="event">حدث</option>
                                <option value="cold_call">مكالمة باردة</option>
                                <option value="social_media">وسائل التواصل</option>
                                <option value="other">أخرى</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="source_campaign_id" class="form-label">الحملة المصدر</label>
                            <select class="form-control select2" id="source_campaign_id" name="source_campaign_id">
                                <option value="">اختر حملة</option>
                                <?php foreach ($campaigns as $campaign): ?>
                                <option value="<?php echo $campaign['id']; ?>"><?php echo htmlspecialchars($campaign['name'], ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
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