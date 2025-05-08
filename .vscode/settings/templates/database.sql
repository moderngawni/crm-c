-- database.sql - إنشاء قاعدة بيانات CRM
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- إنشاء قاعدة البيانات (يتم إنشاؤها في phpMyAdmin)
-- CREATE DATABASE IF NOT EXISTS u917915270_m_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE u917915270_m_crm;

-- جدول القطاعات
CREATE TABLE IF NOT EXISTS sectors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول المناطق
CREATE TABLE IF NOT EXISTS regions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    country VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول الصلاحيات
CREATE TABLE IF NOT EXISTS permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    permission_name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول المستخدمين
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role ENUM('admin', 'manager', 'marketing', 'sales') NOT NULL DEFAULT 'marketing',
    profile_image VARCHAR(255),
    phone VARCHAR(20),
    sector_id INT,
    region_id INT,
    last_login TIMESTAMP NULL,
    status ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sector_id) REFERENCES sectors(id) ON DELETE SET NULL,
    FOREIGN KEY (region_id) REFERENCES regions(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول ربط المستخدمين بالصلاحيات
CREATE TABLE IF NOT EXISTS user_permissions (
    user_id INT NOT NULL,
    permission_id INT NOT NULL,
    granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, permission_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول الحملات
CREATE TABLE IF NOT EXISTS campaigns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    sector_id INT,
    status ENUM('planning', 'active', 'completed', 'on_hold') NOT NULL DEFAULT 'planning',
    start_date DATE,
    end_date DATE,
    budget DECIMAL(15,2),
    owner_id INT,
    description TEXT,
    kpi_goals TEXT,
    campaign_results TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sector_id) REFERENCES sectors(id) ON DELETE SET NULL,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول العملاء المحتملين
CREATE TABLE IF NOT EXISTS leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(100),
    contact_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    sector_id INT,
    city VARCHAR(100),
    country VARCHAR(100),
    lead_score INT DEFAULT 0,
    stage ENUM('new', 'contacted', 'qualified', 'proposal', 'negotiation', 'won', 'lost') NOT NULL DEFAULT 'new',
    source ENUM('website', 'referral', 'event', 'cold_call', 'social_media', 'other') NOT NULL,
    source_campaign_id INT,
    notes TEXT,
    last_contact_date DATE,
    next_followup_date DATE,
    assigned_to INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sector_id) REFERENCES sectors(id) ON DELETE SET NULL,
    FOREIGN KEY (source_campaign_id) REFERENCES campaigns(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إدخال بيانات اختبار
INSERT IGNORE INTO sectors (name, description) VALUES
('الصحة والجمال', 'قطاع يركز على منتجات وخدمات العناية الشخصية'),
('التكنولوجيا', 'قطاع البرمجيات والأجهزة الإلكترونية');

INSERT IGNORE INTO regions (name, country) VALUES
('الرياض', 'السعودية'),
('دبي', 'الإمارات');

INSERT IGNORE INTO permissions (permission_name, description) VALUES
('view_dashboard', 'عرض لوحة التحكم'),
('manage_users', 'إدارة المستخدمين'),
('manage_campaigns', 'إدارة الحملات'),
('manage_leads', 'إدارة العملاء المحتملين'),
('view_analytics', 'عرض التحليلات');

INSERT IGNORE INTO users (username, password, email, first_name, last_name, role, status) VALUES
('admin', '$2y$10$M.yJq5pOUBnV3qKAjD6nXOxyUWevU9yHwAf7jJekHu7bJgvaZnRTO', 'admin@candelspa.online', 'مدير', 'النظام', 'admin', 'active'),
('marketing1', '$2y$10$M.yJq5pOUBnV3qKAjD6nXOxyUWevU9yHwAf7jJekHu7bJgvaZnRTO', 'marketing@candelspa.online', 'أحمد', 'محمد', 'marketing', 'active');

INSERT IGNORE INTO user_permissions (user_id, permission_id)
SELECT u.id, p.id FROM users u, permissions p 
WHERE u.username = 'admin' AND p.permission_name IN (
    'view_dashboard', 'manage_users', 'manage_campaigns', 'manage_leads', 'view_analytics'
);

INSERT IGNORE INTO user_permissions (user_id, permission_id)
SELECT u.id, p.id FROM users u, permissions p 
WHERE u.username = 'marketing1' AND p.permission_name IN ('view_dashboard', 'manage_campaigns', 'manage_leads');

INSERT IGNORE INTO campaigns (name, sector_id, status, start_date, end_date, budget, owner_id, description) VALUES
('حملة الصيف 2025', 1, 'planning', '2025-06-01', '2025-08-31', 50000.00, 1, 'حملة ترويجية لمنتجات العناية بالبشرة'),
('حملة الشتاء 2025', 1, 'planning', '2025-12-01', '2026-02-28', 30000.00, 2, 'حملة لمنتجات الترطيب'),
('حملة تكنولوجيا', 2, 'active', '2025-05-01', '2025-07-31', 100000.00, 1, 'ترويج لأجهزة جديدة');

INSERT IGNORE INTO leads (contact_name, email, sector_id, city, country, stage, source, source_campaign_id, assigned_to) VALUES
('سارة علي', 'sarah@example.com', 1, 'الرياض', 'السعودية', 'new', 'website', 1, 2),
('محمد حسن', 'mohammed@example.com', 1, 'دبي', 'الإمارات', 'contacted', 'referral', 1, 2),
('ليلى أحمد', 'layla@example.com', 1, 'الرياض', 'السعودية', 'qualified', 'event', 2, 1),
('خالد عمر', 'khalid@example.com', 2, 'دبي', 'الإمارات', 'proposal', 'social_media', 3, 1),
('نورا سعيد', 'noura@example.com', 1, 'الرياض', 'السعودية', 'new', 'cold_call', NULL, 2);

SET FOREIGN_KEY_CHECKS = 1;