<?php
// modules/analytics/index.php - التحليلات
$page_title = 'التحليلات';
$db = Database::getInstance();

$leads_by_stage = $db->getRows("SELECT stage, COUNT(*) as count FROM leads GROUP BY stage");
$campaigns_by_status = $db->getRows("SELECT status, COUNT(*) as count FROM campaigns GROUP BY status");
?>
<div class="container-fluid">
    <h1 class="mt-4"><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></h1>
    <?php echo displayAlerts(); ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">العملاء المحتملون حسب المرحلة</h5>
                    <canvas id="leadsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">الحملات حسب الحالة</h5>
                    <canvas id="campaignsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const leadsData = <?php echo json_encode($leads_by_stage); ?>;
    const campaignsData = <?php echo json_encode($campaigns_by_status); ?>;

    new Chart(document.getElementById('leadsChart'), {
        type: 'pie',
        data: {
            labels: leadsData.map(item => item.stage),
            datasets: [{
                data: leadsData.map(item => item.count),
                backgroundColor: ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6f42c1', '#fd7e14']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } }
        }
    });

    new Chart(document.getElementById('campaignsChart'), {
        type: 'bar',
        data: {
            labels: campaignsData.map(item => item.status),
            datasets: [{
                label: 'عدد الحملات',
                data: campaignsData.map(item => item.count),
                backgroundColor: '#007bff'
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });
});
</script>