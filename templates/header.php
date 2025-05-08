<?php
// templates/header.php - رأس الصفحة المشترك
$page_title = $page_title ?? SITE_NAME;
$current_user = isLoggedIn() ? getCurrentUser() : null;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- نمط مخصص -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- رمز الأيقونة -->
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
</head>
<body>
    <?php if (isLoggedIn()): ?>
    <!-- شريط التنقل العلوي -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <!-- ... باقي كود الشريط ... -->
    </nav>
    <?php endif; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php if (isLoggedIn()): ?>
            <!-- هنا سيتم تضمين الشريط الجانبي -->
            <?php endif; ?>
            
            <main class="<?php echo isLoggedIn() ? 'col-md-9 ms-sm-auto col-lg-10 px-md-4' : 'col-12'; ?>">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?php echo $page_title; ?></h1>
                </div>