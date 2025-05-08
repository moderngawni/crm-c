<?php
$hashed_password = 'admin123'; // استبدل هذا بالقيمة المنسوخة من حقل password في جدول users لـ admin
$password = 'admin123';

if (password_verify($password, $hashed_password)) {
    echo "كلمة المرور متطابقة!";
} else {
    echo "كلمة المرور غير متطابقة.";
}
?>