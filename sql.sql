-- الحذف إذا كانت الجداول موجودة مسبقًا
SET FOREIGN_KEY_CHECKS = 0;

-- ==========================================
-- Generic Lookup Tables
-- ==========================================

-- الجدول الرئيسي للقيم الثابتة (Categories)
CREATE TABLE lookup_masters (
    lookup_master_id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) NOT NULL UNIQUE, -- e.g., 'ACCOUNT_STATUS', 'GENDER'
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- جدول القيم التفصيلية (Values)
CREATE TABLE lookup_values (
    lookup_value_id INT PRIMARY KEY AUTO_INCREMENT,
    lookup_master_id INT NOT NULL,
    code VARCHAR(50) NOT NULL, -- e.g., 'ACTIVE', 'PENDING'
    name VARCHAR(100) NOT NULL, -- Display Name
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE INDEX unique_lookup_code (lookup_master_id, code),
    FOREIGN KEY (lookup_master_id) REFERENCES lookup_masters(lookup_master_id) ON DELETE CASCADE
);

-- ==========================================
-- Main Tables
-- ==========================================

-- جدول الحسابات
CREATE TABLE accounts (
    account_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE,
    email_verified BOOLEAN DEFAULT FALSE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL UNIQUE,
    profile_image VARCHAR(255) DEFAULT NULL,
    account_status_id INT DEFAULT NULL, -- FK to lookup_values
    phone_verified BOOLEAN DEFAULT FALSE,
    verification_code VARCHAR(10) DEFAULT NULL,
    verification_expiry TIMESTAMP NULL DEFAULT NULL,
    last_login TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,

    FOREIGN KEY (account_status_id) REFERENCES lookup_values(lookup_value_id),
    INDEX idx_account_status (account_status_id),
    INDEX idx_account_phone (phone),
    INDEX idx_account_email (email)
);

-- جدول الاستوديوهات
CREATE TABLE studios (
    studio_id INT PRIMARY KEY AUTO_INCREMENT,
    account_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    logo VARCHAR(255) DEFAULT NULL,
    studio_status_id INT DEFAULT NULL, -- FK to lookup_values
    
    email VARCHAR(100),
    phone VARCHAR(20),
    website VARCHAR(255),
    address TEXT,
    city VARCHAR(100),
    
    settings JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,

    FOREIGN KEY (account_id) REFERENCES accounts(account_id) ON DELETE CASCADE,
    FOREIGN KEY (studio_status_id) REFERENCES lookup_values(lookup_value_id),
    INDEX idx_studio_account (account_id)
);

-- جدول المدارس
CREATE TABLE schools (
    school_id INT PRIMARY KEY AUTO_INCREMENT,
    account_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    logo VARCHAR(255) DEFAULT NULL,
    school_type_id INT DEFAULT NULL,   -- FK to lookup_values
    school_level_id INT DEFAULT NULL,  -- FK to lookup_values
    school_status_id INT DEFAULT NULL, -- FK to lookup_values

    email VARCHAR(100),
    phone VARCHAR(20),
    website VARCHAR(255),
    address TEXT,
    city VARCHAR(100),
    country VARCHAR(100),

    settings JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,

    FOREIGN KEY (account_id) REFERENCES accounts(account_id) ON DELETE CASCADE,
    FOREIGN KEY (school_type_id) REFERENCES lookup_values(lookup_value_id),
    FOREIGN KEY (school_level_id) REFERENCES lookup_values(lookup_value_id),
    FOREIGN KEY (school_status_id) REFERENCES lookup_values(lookup_value_id),
    INDEX idx_school_account (account_id)
);

-- جدول المشتركين
CREATE TABLE subscribers (
    subscriber_id INT PRIMARY KEY AUTO_INCREMENT,
    account_id INT NOT NULL,
    subscriber_status_id INT DEFAULT NULL, -- FK to lookup_values
    settings JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,

    FOREIGN KEY (account_id) REFERENCES accounts(account_id) ON DELETE CASCADE,
    FOREIGN KEY (subscriber_status_id) REFERENCES lookup_values(lookup_value_id)
);

-- جدول العملاء
CREATE TABLE customers (
    customer_id INT PRIMARY KEY AUTO_INCREMENT,
    account_id INT NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    date_of_birth DATE DEFAULT NULL,
    gender_id INT DEFAULT NULL, -- FK to lookup_values
    
    email VARCHAR(100),
    phone VARCHAR(20),
    
    settings JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,

    FOREIGN KEY (account_id) REFERENCES accounts(account_id) ON DELETE CASCADE,
    FOREIGN KEY (gender_id) REFERENCES lookup_values(lookup_value_id),
    INDEX idx_customer_account (account_id)
);

-- جدول الأدوار
CREATE TABLE roles (
    role_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    is_system BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_role_name (name)
);

-- جدول الصلاحيات
CREATE TABLE permissions (
    permission_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    resource_type VARCHAR(50) NOT NULL,
    action VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE INDEX unique_permission_resource_action (resource_type, action),
    INDEX idx_permission_name (name)
);

-- جدول صلاحيات الأدوار
CREATE TABLE role_permissions (
    role_permission_id INT PRIMARY KEY AUTO_INCREMENT,
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE INDEX unique_role_permission (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(permission_id) ON DELETE CASCADE
);

-- جدول أدوار الحسابات
CREATE TABLE account_roles (
    account_role_id INT PRIMARY KEY AUTO_INCREMENT,
    account_id INT NOT NULL,
    role_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE INDEX unique_account_role_scope (account_id, role_id),
    INDEX idx_account_role (account_id, role_id),
    FOREIGN KEY (account_id) REFERENCES accounts(account_id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE
);

-- جدول حسابات التخزين
CREATE TABLE storage_accounts (
    storage_account_id INT PRIMARY KEY AUTO_INCREMENT,
    owner_type VARCHAR(50) NOT NULL, -- Polymorphic (no FK to lookup per se, usually strict string or mapped ID)
    owner_id INT NOT NULL,
    total_space INT NOT NULL,
    used_space INT DEFAULT 0,
    status VARCHAR(20) DEFAULT 'active', -- Simple string is often fine, or could be FK
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE INDEX unique_storage_owner (owner_type, owner_id),
    INDEX idx_storage_owner (owner_type, owner_id)
);

-- جدول المكاتب
CREATE TABLE offices (
    office_id INT PRIMARY KEY AUTO_INCREMENT,
    studio_id INT NOT NULL,
    subscriber_id INT DEFAULT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_office_subscriber (subscriber_id),
    FOREIGN KEY (studio_id) REFERENCES studios(studio_id) ON DELETE CASCADE,
    FOREIGN KEY (subscriber_id) REFERENCES subscribers(subscriber_id) ON DELETE SET NULL
);

-- جدول الخطط
CREATE TABLE plans (
    plan_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    storage_limit INT NOT NULL,
    price_monthly DECIMAL(10,2) NOT NULL,
    price_yearly DECIMAL(10,2) NOT NULL,
    max_albums INT DEFAULT 0,
    max_cards INT DEFAULT 0,
    max_users INT DEFAULT 0,
    max_offices INT DEFAULT 0,
    features JSON,
    billing_cycle_id INT DEFAULT NULL, -- FK to lookup_values
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,

    FOREIGN KEY (billing_cycle_id) REFERENCES lookup_values(lookup_value_id),
    INDEX idx_plan_active (is_active)
);

-- جدول الاشتراكات
CREATE TABLE subscriptions (
    subscription_id INT PRIMARY KEY AUTO_INCREMENT,
    subscriber_type_id INT NOT NULL, -- FK to lookup_values (Subscriber Type)
    subscriber_id INT NOT NULL,
    plan_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    renewal_date DATE NOT NULL,
    auto_renew BOOLEAN DEFAULT TRUE,
    subscription_status_id INT DEFAULT NULL, -- FK to lookup_values
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,

    INDEX idx_subscription_subscriber (subscriber_type_id, subscriber_id, subscription_status_id),
    INDEX idx_subscription_renewal (renewal_date, subscription_status_id),
    FOREIGN KEY (plan_id) REFERENCES plans(plan_id),
    FOREIGN KEY (subscriber_type_id) REFERENCES lookup_values(lookup_value_id),
    FOREIGN KEY (subscription_status_id) REFERENCES lookup_values(lookup_value_id)
);

-- جدول الفواتير
CREATE TABLE invoices (
    invoice_id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_number VARCHAR(50) NOT NULL UNIQUE,
    subscriber_type_id INT NOT NULL, -- FK to lookup_values
    subscriber_id INT NOT NULL,
    issue_date DATE NOT NULL,
    due_date DATE NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    paid_amount DECIMAL(10,2) DEFAULT 0,
    currency CHAR(3) DEFAULT 'SAR',
    invoice_status_id INT DEFAULT NULL, -- FK to lookup_values
    payment_method_id INT DEFAULT NULL, -- FK to lookup_values
    payment_date DATE DEFAULT NULL,
    transaction_id VARCHAR(100) DEFAULT NULL,
    notes TEXT,
    pdf_path VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_invoice_subscriber (subscriber_type_id, subscriber_id, invoice_status_id),
    INDEX idx_invoice_due_date (due_date),
    FOREIGN KEY (invoice_status_id) REFERENCES lookup_values(lookup_value_id),
    FOREIGN KEY (payment_method_id) REFERENCES lookup_values(lookup_value_id),
    FOREIGN KEY (subscriber_type_id) REFERENCES lookup_values(lookup_value_id)
);

-- جدول عناصر الفواتير
CREATE TABLE invoice_items (
    item_id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT NOT NULL,
    description VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    item_type_id INT NOT NULL, -- FK to lookup_values
    related_id INT DEFAULT NULL,
    taxable BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_invoice_items_invoice (invoice_id),
    FOREIGN KEY (invoice_id) REFERENCES invoices(invoice_id) ON DELETE CASCADE,
    FOREIGN KEY (item_type_id) REFERENCES lookup_values(lookup_value_id)
);

-- جدول المدفوعات
CREATE TABLE payments (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method_id INT NOT NULL, -- FK to lookup_values
    gateway_transaction_id VARCHAR(100) DEFAULT NULL,
    gateway_response JSON,
    payment_status_id INT DEFAULT NULL, -- FK to lookup_values
    paid_at TIMESTAMP NULL DEFAULT NULL,
    refunded_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_payments_invoice (invoice_id),
    FOREIGN KEY (invoice_id) REFERENCES invoices(invoice_id) ON DELETE CASCADE,
    FOREIGN KEY (payment_method_id) REFERENCES lookup_values(lookup_value_id),
    FOREIGN KEY (payment_status_id) REFERENCES lookup_values(lookup_value_id)
);

-- جدول العمولات
CREATE TABLE commissions (
    commission_id INT PRIMARY KEY AUTO_INCREMENT,
    studio_id INT NOT NULL,
    office_id INT DEFAULT NULL,
    invoice_id INT NOT NULL,
    transaction_type_id INT NOT NULL, -- FK to lookup_values
    amount DECIMAL(10,2) NOT NULL,
    commission_rate DECIMAL(5,2) NOT NULL,
    studio_share DECIMAL(10,2) NOT NULL,
    platform_share DECIMAL(10,2) NOT NULL,
    settlement_date DATE DEFAULT NULL,
    commission_status_id INT DEFAULT NULL, -- FK to lookup_values
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_commissions_studio (studio_id, commission_status_id),
    FOREIGN KEY (studio_id) REFERENCES studios(studio_id) ON DELETE CASCADE,
    FOREIGN KEY (office_id) REFERENCES offices(office_id) ON DELETE SET NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(invoice_id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_type_id) REFERENCES lookup_values(lookup_value_id),
    FOREIGN KEY (commission_status_id) REFERENCES lookup_values(lookup_value_id)
);

-- جدول الألبومات
CREATE TABLE albums (
    album_id INT PRIMARY KEY AUTO_INCREMENT,
    owner_type VARCHAR(50) NOT NULL, -- Polymorphic
    owner_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_default BOOLEAN DEFAULT FALSE,
    is_visible BOOLEAN DEFAULT TRUE,
    view_count INT DEFAULT 0,
    settings JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,

    INDEX idx_album_owner (owner_type, owner_id),
    INDEX idx_album_visible (is_visible)
);

-- جدول الصور
CREATE TABLE photos (
    photo_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    album_id INT NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL UNIQUE,
    file_path VARCHAR(500) NOT NULL,
    file_size BIGINT NOT NULL,
    mime_type VARCHAR(100),
    width INT,
    height INT,
    caption TEXT,
    tags JSON,
    is_hidden BOOLEAN DEFAULT FALSE,
    is_archived BOOLEAN DEFAULT FALSE,
    view_count INT DEFAULT 0,
    download_count INT DEFAULT 0,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,

    INDEX idx_photos_album (album_id),
    INDEX idx_photos_uploaded (uploaded_at),
    FOREIGN KEY (album_id) REFERENCES albums(album_id) ON DELETE CASCADE
);

-- جدول مجموعات البطاقات
CREATE TABLE card_groups (
    group_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- جدول البطاقات
CREATE TABLE cards (
    card_id INT PRIMARY KEY AUTO_INCREMENT,
    card_uuid CHAR(36) NOT NULL UNIQUE DEFAULT (UUID()),
    card_number VARCHAR(50) NOT NULL,
    card_group_id INT DEFAULT NULL,
    owner_type VARCHAR(50) NOT NULL, -- Polymorphic
    owner_id INT NOT NULL,
    holder_id INT DEFAULT NULL,
    card_type_id INT NOT NULL,       -- FK to lookup_values
    card_status_id INT DEFAULT NULL, -- FK to lookup_values
    activation_date TIMESTAMP NULL DEFAULT NULL,
    expiry_date DATE DEFAULT NULL,
    usage_count INT DEFAULT 0,
    last_used TIMESTAMP NULL DEFAULT NULL,
    metadata JSON,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_cards_number (card_number),
    INDEX idx_cards_owner (owner_type, owner_id),
    INDEX idx_cards_holder (holder_id),
    INDEX idx_cards_status_expiry (card_status_id, expiry_date),
    FOREIGN KEY (card_group_id) REFERENCES card_groups(group_id) ON DELETE SET NULL,
    FOREIGN KEY (holder_id) REFERENCES accounts(account_id) ON DELETE SET NULL,
    FOREIGN KEY (card_type_id) REFERENCES lookup_values(lookup_value_id),
    FOREIGN KEY (card_status_id) REFERENCES lookup_values(lookup_value_id)
);

-- جدول بطاقات الألبومات
CREATE TABLE card_albums (
    card_album_id INT PRIMARY KEY AUTO_INCREMENT,
    card_id INT NOT NULL,
    album_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE INDEX unique_card_album (card_id, album_id),
    FOREIGN KEY (card_id) REFERENCES cards(card_id) ON DELETE CASCADE,
    FOREIGN KEY (album_id) REFERENCES albums(album_id) ON DELETE CASCADE
);

-- جدول الإشعارات
CREATE TABLE notifications (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    account_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    notification_type_id INT NOT NULL, -- FK to lookup_values
    is_read BOOLEAN DEFAULT FALSE,
    metadata JSON,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL DEFAULT NULL,

    INDEX idx_notifications_account (account_id, is_read),
    FOREIGN KEY (account_id) REFERENCES accounts(account_id) ON DELETE CASCADE,
    FOREIGN KEY (notification_type_id) REFERENCES lookup_values(lookup_value_id)
);

-- جدول سجل النشاطات
CREATE TABLE activity_logs (
    log_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    account_id INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    resource_type VARCHAR(50) DEFAULT NULL,
    resource_id INT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT,
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_activity_account (account_id, created_at),
    INDEX idx_activity_action (action, created_at),
    FOREIGN KEY (account_id) REFERENCES accounts(account_id) ON DELETE SET NULL
);

-- جدول الإعدادات
CREATE TABLE settings (
    setting_id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value JSON NOT NULL,
    setting_type_id INT NOT NULL, -- FK to lookup_values
    description TEXT,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_settings_key (setting_key),
    FOREIGN KEY (setting_type_id) REFERENCES lookup_values(lookup_value_id)
);

-- جدول الإحصائيات اليومية
CREATE TABLE daily_stats (
    stat_id INT PRIMARY KEY AUTO_INCREMENT,
    stat_date DATE NOT NULL,
    account_id INT DEFAULT NULL,
    new_accounts INT DEFAULT 0,
    new_photos INT DEFAULT 0,
    photo_views INT DEFAULT 0,
    card_activations INT DEFAULT 0,
    revenue DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE INDEX unique_daily_stat (stat_date, account_id),
    FOREIGN KEY (account_id) REFERENCES accounts(account_id) ON DELETE SET NULL
);

SET FOREIGN_KEY_CHECKS = 1;

-- ==========================================
-- Seed Data for Lookups
-- ==========================================
/* ===============================
   LOOKUP MASTERS
================================ */

INSERT INTO lookup_masters (code, name, description) VALUES
('ACCOUNT_STATUS', 'حالة الحساب', 'Status of user accounts'),
('STUDIO_STATUS', 'حالة الاستوديو', 'Status of studio profiles'),
('SCHOOL_TYPE', 'نوع المدرسة', 'Type of educational institution'),
('SCHOOL_LEVEL', 'المرحلة التعليمية', 'Educational level of the school'),
('SCHOOL_STATUS', 'حالة المدرسة', 'Operational status of the school'),
('SUBSCRIBER_TYPE', 'نوع المشترك', 'Type of entity subscribing'),
('SUBSCRIBER_STATUS', 'حالة المشترك', 'Status of the subscription profile'),
('GENDER', 'الجنس', 'Gender options'),
('BILLING_CYCLE', 'دورة الفوترة', 'Billing frequency'),
('SUBSCRIPTION_STATUS', 'حالة الاشتراك', 'Current state of subscription'),
('INVOICE_STATUS', 'حالة الفاتورة', 'Payment status of invoices'),
('PAYMENT_METHOD', 'طريقة الدفع', 'Methods of payment'),
('PAYMENT_STATUS', 'حالة الدفع', 'Status of payment transactions'),
('ITEM_TYPE', 'نوع البند', 'Type of invoice item'),
('TRANSACTION_TYPE', 'نوع العملية', 'Type of financial transaction'),
('COMMISSION_STATUS', 'حالة العمولة', 'Status of commission payout'),
('CARD_TYPE', 'نوع البطاقة', 'Type of access/photo card'),
('CARD_STATUS', 'حالة البطاقة', 'Status of the card'),
('NOTIFICATION_TYPE', 'نوع الإشعار', 'Category of notification'),
('SETTING_TYPE', 'نوع الإعداد', 'Data type for settings');

/* ===============================
   ACCOUNT STATUS
================================ */

INSERT INTO lookup_values (lookup_master_id, code, name, description)
SELECT lookup_master_id, 'ACTIVE', 'نشط', 'Account is active'
FROM lookup_masters WHERE code = 'ACCOUNT_STATUS';

INSERT INTO lookup_values (lookup_master_id, code, name, description)
SELECT lookup_master_id, 'PENDING', 'قيد المراجعة', 'Account pending verification'
FROM lookup_masters WHERE code = 'ACCOUNT_STATUS';

INSERT INTO lookup_values (lookup_master_id, code, name, description)
SELECT lookup_master_id, 'SUSPENDED', 'موقوف', 'Account suspended'
FROM lookup_masters WHERE code = 'ACCOUNT_STATUS';

INSERT INTO lookup_values (lookup_master_id, code, name, description)
SELECT lookup_master_id, 'INACTIVE', 'غير نشط', 'Account deactivated'
FROM lookup_masters WHERE code = 'ACCOUNT_STATUS';

/* ===============================
   STUDIO STATUS
================================ */

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'ACTIVE', 'نشط'
FROM lookup_masters WHERE code = 'STUDIO_STATUS';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'PENDING', 'قيد المراجعة'
FROM lookup_masters WHERE code = 'STUDIO_STATUS';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'SUSPENDED', 'موقوف'
FROM lookup_masters WHERE code = 'STUDIO_STATUS';

/* ===============================
   SCHOOL TYPE
================================ */

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'PUBLIC', 'حكومية'
FROM lookup_masters WHERE code = 'SCHOOL_TYPE';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'PRIVATE', 'أهلية'
FROM lookup_masters WHERE code = 'SCHOOL_TYPE';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'INTERNATIONAL', 'عالمية'
FROM lookup_masters WHERE code = 'SCHOOL_TYPE';

/* ===============================
   SCHOOL LEVEL
================================ */

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'KINDERGARTEN', 'روضة'
FROM lookup_masters WHERE code = 'SCHOOL_LEVEL';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'PRIMARY', 'ابتدائي'
FROM lookup_masters WHERE code = 'SCHOOL_LEVEL';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'MIDDLE', 'إعدادي'
FROM lookup_masters WHERE code = 'SCHOOL_LEVEL';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'HIGH', 'ثانوي'
FROM lookup_masters WHERE code = 'SCHOOL_LEVEL';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'UNIVERSITY', 'جامعي'
FROM lookup_masters WHERE code = 'SCHOOL_LEVEL';

/* ===============================
   SCHOOL STATUS
================================ */

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'ACTIVE', 'نشط'
FROM lookup_masters WHERE code = 'SCHOOL_STATUS';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'PENDING', 'قيد المراجعة'
FROM lookup_masters WHERE code = 'SCHOOL_STATUS';

/* ===============================
   SUBSCRIBER TYPE
================================ */

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'SCHOOL', 'مدرسة'
FROM lookup_masters WHERE code = 'SUBSCRIBER_TYPE';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'STUDIO', 'استوديو'
FROM lookup_masters WHERE code = 'SUBSCRIBER_TYPE';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'INDIVIDUAL', 'فرد'
FROM lookup_masters WHERE code = 'SUBSCRIBER_TYPE';

/* ===============================
   SUBSCRIBER STATUS
================================ */

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'ACTIVE', 'نشط'
FROM lookup_masters WHERE code = 'SUBSCRIBER_STATUS';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'INACTIVE', 'غير نشط'
FROM lookup_masters WHERE code = 'SUBSCRIBER_STATUS';

/* ===============================
   GENDER
================================ */

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'MALE', 'ذكر'
FROM lookup_masters WHERE code = 'GENDER';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'FEMALE', 'أنثى'
FROM lookup_masters WHERE code = 'GENDER';

/* ===============================
   BILLING CYCLE
================================ */

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'MONTHLY', 'شهري'
FROM lookup_masters WHERE code = 'BILLING_CYCLE';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'YEARLY', 'سنوي'
FROM lookup_masters WHERE code = 'BILLING_CYCLE';

/* ===============================
   SUBSCRIPTION STATUS
================================ */

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'ACTIVE', 'نشط'
FROM lookup_masters WHERE code = 'SUBSCRIPTION_STATUS';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'EXPIRED', 'منتهي'
FROM lookup_masters WHERE code = 'SUBSCRIPTION_STATUS';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'CANCELLED', 'ملغي'
FROM lookup_masters WHERE code = 'SUBSCRIPTION_STATUS';

/* ===============================
   INVOICE STATUS
================================ */

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'DRAFT', 'مسودة'
FROM lookup_masters WHERE code = 'INVOICE_STATUS';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'ISSUED', 'صادرة'
FROM lookup_masters WHERE code = 'INVOICE_STATUS';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'PAID', 'مدفوعة'
FROM lookup_masters WHERE code = 'INVOICE_STATUS';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'OVERDUE', 'متأخرة'
FROM lookup_masters WHERE code = 'INVOICE_STATUS';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'CANCELLED', 'ملغاة'
FROM lookup_masters WHERE code = 'INVOICE_STATUS';

/* ===============================
   PAYMENT METHOD
================================ */

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'CREDIT_CARD', 'بطاقة ائتمان'
FROM lookup_masters WHERE code = 'PAYMENT_METHOD';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'BANK_TRANSFER', 'تحويل بنكي'
FROM lookup_masters WHERE code = 'PAYMENT_METHOD';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'CASH', 'نقدي'
FROM lookup_masters WHERE code = 'PAYMENT_METHOD';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'MADA', 'مدى'
FROM lookup_masters WHERE code = 'PAYMENT_METHOD';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'APPLE_PAY', 'Apple Pay'
FROM lookup_masters WHERE code = 'PAYMENT_METHOD';

/* ===============================
   PAYMENT STATUS
================================ */

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'PENDING', 'قيد الانتظار'
FROM lookup_masters WHERE code = 'PAYMENT_STATUS';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'COMPLETED', 'مكتمل'
FROM lookup_masters WHERE code = 'PAYMENT_STATUS';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'FAILED', 'فشل'
FROM lookup_masters WHERE code = 'PAYMENT_STATUS';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'REFUNDED', 'مسترجع'
FROM lookup_masters WHERE code = 'PAYMENT_STATUS';

/* ===============================
   ITEM TYPE
================================ */

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'SUBSCRIPTION', 'اشتراك'
FROM lookup_masters WHERE code = 'ITEM_TYPE';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'STORAGE', 'مساحة تخزين'
FROM lookup_masters WHERE code = 'ITEM_TYPE';

/* ===============================
   TRANSACTION TYPE
================================ */

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'SALE', 'بيع'
FROM lookup_masters WHERE code = 'TRANSACTION_TYPE';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'REFUND', 'استرجاع'
FROM lookup_masters WHERE code = 'TRANSACTION_TYPE';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'COMMISSION', 'عمولة'
FROM lookup_masters WHERE code = 'TRANSACTION_TYPE';

/* ===============================
   COMMISSION STATUS
================================ */

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'PENDING', 'قيد الانتظار'
FROM lookup_masters WHERE code = 'COMMISSION_STATUS';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'PAID', 'مدفوعة'
FROM lookup_masters WHERE code = 'COMMISSION_STATUS';

/* ===============================
   CARD TYPE
================================ */

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'STANDARD', 'عادية'
FROM lookup_masters WHERE code = 'CARD_TYPE';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'PREMIUM', 'مميزة'
FROM lookup_masters WHERE code = 'CARD_TYPE';

/* ===============================
   CARD STATUS
================================ */

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'ACTIVE', 'نشطة'
FROM lookup_masters WHERE code = 'CARD_STATUS';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'INACTIVE', 'غير نشطة'
FROM lookup_masters WHERE code = 'CARD_STATUS';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'EXPIRED', 'منتهية'
FROM lookup_masters WHERE code = 'CARD_STATUS';

/* ===============================
   NOTIFICATION TYPE
================================ */

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'INFO', 'معلومة'
FROM lookup_masters WHERE code = 'NOTIFICATION_TYPE';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'WARNING', 'تحذير'
FROM lookup_masters WHERE code = 'NOTIFICATION_TYPE';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'ERROR', 'خطأ'
FROM lookup_masters WHERE code = 'NOTIFICATION_TYPE';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'SUCCESS', 'نجاح'
FROM lookup_masters WHERE code = 'NOTIFICATION_TYPE';

/* ===============================
   SETTING TYPE
================================ */

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'STRING', 'نص'
FROM lookup_masters WHERE code = 'SETTING_TYPE';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'BOOLEAN', 'منطقي'
FROM lookup_masters WHERE code = 'SETTING_TYPE';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'JSON', 'JSON'
FROM lookup_masters WHERE code = 'SETTING_TYPE';

INSERT INTO lookup_values (lookup_master_id, code, name)
SELECT lookup_master_id, 'INTEGER', 'رقم'
FROM lookup_masters WHERE code = 'SETTING_TYPE';
