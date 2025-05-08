<?php
// templates/sidebar.php - القائمة الجانبية
$current_page = isset($_GET['page']) ? sanitize($_GET['page']) : 'dashboard';
$user_role = $_SESSION['user_role'] ?? 'user';
?>

<div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4">
            <img src="assets/images/logo.png" alt="<?php echo SITE_NAME; ?>" class="img-fluid" style="max-width: 100px;">
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page === 'dashboard') ? 'active' : ''; ?>" href="index.php?page=dashboard">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    لوحة التحكم
                </a>
            </li>
            
            <?php if (checkPermissions(['manage_leads', 'view_leads'])): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page === 'leads') ? 'active' : ''; ?>" href="index.php?page=leads">
                    <i class="fas fa-users me-2"></i>
                    العملاء المحتملين
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (checkPermissions(['manage_campaigns', 'view_campaigns'])): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page === 'campaigns') ? 'active' : ''; ?>" href="index.php?page=campaigns">
                    <i class="fas fa-bullhorn me-2"></i>
                    الحملات
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (checkPermissions(['view_analytics'])): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page === 'analytics') ? 'active' : ''; ?>" href="index.php?page=analytics">
                    <i class="fas fa-chart-bar me-2"></i>
                    التحليلات
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($user_role === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page === 'users') ? 'active' : ''; ?>" href="index.php?page=users">
                    <i class="fas fa-user-cog me-2"></i>
                    المستخدمين
                </a>
            </li>
            <?php endif; ?>
        </ul>
        
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>إعدادات</span>
        </h6>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page === 'profile') ? 'active' : ''; ?>" href="index.php?page=profile">
                    <i class="fas fa-user-circle me-2"></i>
                    الملف الشخصي
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page === 'settings') ? 'active' : ''; ?>" href="index.php?page=settings">
                    <i class="fas fa-cog me-2"></i>
                    الإعدادات
                </a>
            </li>
        </ul>
    </div>
</div>