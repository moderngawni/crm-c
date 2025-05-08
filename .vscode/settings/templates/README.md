Candel Spa CRM
نظام إدارة علاقات العملاء (CRM) لتتبع المستخدمين، الحملات، والعملاء المحتملين.
هيكلية المشروع
candelspa_crm/
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── scripts.js
├── config/
│   ├── config.php
│   └── database.php
├── includes/
│   ├── functions.php
│   └── auth.php
├── modules/
│   ├── dashboard/
│   │   └── index.php
│   ├── users/
│   │   └── index.php
│   ├── campaigns/
│   │   └── index.php
│   ├── leads/
│   │   └── index.php
│   └── analytics/
│       └── index.php
├── templates/
│   ├── header.php
│   ├── sidebar.php
│   └── footer.php
├── index.php
├── login.php
├── logout.php
├── .htaccess
└── database.sql

خطوات التثبيت على هوستنجر

إنشاء قاعدة البيانات:

سجّل الدخول إلى hPanel -> Databases -> MySQL Databases.
أنشئ قاعدة بيانات (مثل u917915270_m_crm) ومستخدم (مثل u917915270_crm) مع كلمة مرور.
اربط المستخدم بالقاعدة مع ALL PRIVILEGES.
افتح phpMyAdmin، اختر القاعدة، واستورد database.sql من علامة التبويب Import.


تعديل إعدادات قاعدة البيانات:

افتح config/database.php.
استبدل YOUR_PASSWORD بكلمة المرور الفعلية.
تأكد من أن db_name, username, وhost صحيحة.


رفع الملفات:

أنشئ مجلدًا محليًا (مثل candelspa_crm) وضع الملفات وفق الهيكلية أعلاه.
ضغط المجلد إلى candelspa_crm.zip.
في hPanel، انتقل إلى File Manager -> public_html.
ارفع candelspa_crm.zip، فك الضغط، وانقل الملفات إلى public_html.


التحقق من الأذونات:

اضبط أذونات الملفات على 644 والمجلدات على 755 في File Manager.


اختبار الموقع:

زر https://candelspa.online/login.php.
سجّل الدخول باستخدام:
اسم المستخدم: admin
كلمة المرور: admin123


تحقق من الصفحات: لوحة التحكم، المستخدمون، الحملات، العملاء المحتملون، التحليلات.



معالجة الأخطاء

خطأ الاتصال بقاعدة البيانات: تحقق من إعدادات

