CREATE DATABASE IF NOT EXISTS sistem_penjualan_beras CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE sistem_penjualan_beras;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'pegawai') NOT NULL DEFAULT 'pegawai',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    profile_photo VARCHAR(255) NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL
);

CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_code VARCHAR(50) NOT NULL UNIQUE,
    product_name VARCHAR(150) NOT NULL,
    weight_kg DECIMAL(10,2) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL
);

CREATE TABLE quick_templates (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    template_code VARCHAR(50) NOT NULL UNIQUE,
    template_name VARCHAR(150) NOT NULL,
    qty_5kg INT NOT NULL DEFAULT 0,
    qty_10kg INT NOT NULL DEFAULT 0,
    qty_25kg INT NOT NULL DEFAULT 0,
    discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_by INT UNSIGNED NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_quick_templates_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE SET NULL
);

CREATE TABLE sale_limit_settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    is_enabled TINYINT(1) NOT NULL DEFAULT 0,
    max_total_kg INT UNSIGNED NOT NULL DEFAULT 0,
    updated_by INT UNSIGNED NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    CONSTRAINT fk_sale_limit_settings_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE SET NULL
);

CREATE TABLE product_prices (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    price DECIMAL(15,2) NOT NULL,
    price_change DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    is_current TINYINT(1) NOT NULL DEFAULT 1,
    updated_by INT UNSIGNED NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    INDEX idx_product_prices_current (product_id, is_current),
    CONSTRAINT fk_product_prices_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_product_prices_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE SET NULL
);

CREATE TABLE quick_template_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    template_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    CONSTRAINT fk_quick_template_items_template FOREIGN KEY (template_id) REFERENCES quick_templates(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_quick_template_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE sales_transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(50) NOT NULL UNIQUE,
    transaction_date DATETIME NOT NULL,
    created_by INT UNSIGNED NULL,
    template_id INT UNSIGNED NULL,
    customer_name VARCHAR(150) NULL,
    qty_5kg INT NOT NULL DEFAULT 0,
    qty_10kg INT NOT NULL DEFAULT 0,
    qty_25kg INT NOT NULL DEFAULT 0,
    price_5kg DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    price_10kg DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    price_25kg DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    subtotal_5kg DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    subtotal_10kg DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    subtotal_25kg DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    total_items INT NOT NULL DEFAULT 0,
    total_kg DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    grand_total DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    total_harga DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    discount_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    source_transaksi ENUM('manual', 'template') NOT NULL DEFAULT 'manual',
    notes TEXT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    INDEX idx_sales_transactions_date (transaction_date),
    CONSTRAINT fk_sales_transactions_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE SET NULL,
    CONSTRAINT fk_sales_transactions_template FOREIGN KEY (template_id) REFERENCES quick_templates(id) ON DELETE SET NULL ON UPDATE SET NULL
);

CREATE TABLE sales_transaction_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NULL,
    product_name_snapshot VARCHAR(150) NOT NULL,
    weight_kg_snapshot DECIMAL(10,2) NOT NULL,
    unit_price_snapshot DECIMAL(15,2) NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL,
    total_kg_item DECIMAL(12,2) NOT NULL,
    CONSTRAINT fk_sales_transaction_items_transaction FOREIGN KEY (transaction_id) REFERENCES sales_transactions(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_sales_transaction_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL ON UPDATE SET NULL
);

INSERT INTO users (id, full_name, username, password_hash, role, is_active, created_at, updated_at) VALUES
(1, 'Administrator UD Tulus Sari Merta', 'admin', '$2y$10$6EacSxhEqdr5TlyzOxCOjOHd0cxWeRJhuaE17HsHgBIT2k2/LJ0rC', 'admin', 1, NOW(), NOW()),
(2, 'Pegawai Penjualan', 'pegawai', '$2y$10$1vIUlAff7nhz3Y.GdKxpPu/YHU/2HBYh1HGkmG5G6ro4I02ScfHl2', 'pegawai', 1, NOW(), NOW());

INSERT INTO products (id, product_code, product_name, weight_kg, is_active, created_at, updated_at) VALUES
(1, 'BRS-005', 'Beras 5 Kg', 5.00, 1, NOW(), NOW()),
(2, 'BRS-010', 'Beras 10 Kg', 10.00, 1, NOW(), NOW()),
(3, 'BRS-025', 'Beras 25 Kg', 25.00, 1, NOW(), NOW());

INSERT INTO product_prices (id, product_id, price, price_change, is_current, updated_by, created_at, updated_at) VALUES
(1, 1, 14200.00, 0.00, 1, 1, NOW(), NOW()),
(2, 2, 14100.00, 0.00, 1, 1, NOW(), NOW()),
(3, 3, 14000.00, 0.00, 1, 1, NOW(), NOW());

INSERT INTO quick_templates (id, template_code, template_name, qty_5kg, qty_10kg, qty_25kg, discount_percent, is_active, created_by, created_at, updated_at) VALUES
(1, 'TPL-001', 'Paket Operasional Ringkas', 2, 1, 0, 0.00, 1, 1, NOW(), NOW()),
(2, 'TPL-002', 'Paket Campuran Penjualan', 1, 0, 1, 5.00, 1, 1, NOW(), NOW());

INSERT INTO quick_template_items (template_id, product_id, quantity) VALUES
(1, 1, 2),
(1, 2, 1),
(2, 1, 1),
(2, 3, 1);

INSERT INTO sale_limit_settings (id, is_enabled, max_total_kg, updated_by, created_at, updated_at) VALUES
(1, 0, 100, 1, NOW(), NOW());

INSERT INTO sales_transactions (id, invoice_number, transaction_date, created_by, template_id, customer_name, qty_5kg, qty_10kg, qty_25kg, price_5kg, price_10kg, price_25kg, subtotal_5kg, subtotal_10kg, subtotal_25kg, total_items, total_kg, grand_total, total_harga, discount_percent, discount_amount, source_transaksi, notes, created_at, updated_at) VALUES
(1, CONCAT('TRX-', DATE_FORMAT(CURDATE() - INTERVAL 2 DAY, '%Y%m%d'), '-0001'), CONCAT(CURDATE() - INTERVAL 2 DAY, ' 09:15:00'), 1, 1, 'Pembeli Internal A', 2, 1, 0, 14200.00, 14100.00, 14000.00, 142000.00, 141000.00, 0.00, 3, 20.00, 283000.00, 283000.00, 0.00, 0.00, 'template', 'Data contoh laporan.', NOW(), NOW()),
(2, CONCAT('TRX-', DATE_FORMAT(CURDATE() - INTERVAL 1 DAY, '%Y%m%d'), '-0001'), CONCAT(CURDATE() - INTERVAL 1 DAY, ' 10:45:00'), 2, 2, 'Pembeli Internal B', 1, 0, 1, 14200.00, 14100.00, 14000.00, 71000.00, 0.00, 350000.00, 2, 30.00, 399950.00, 399950.00, 5.00, 21050.00, 'template', 'Transaksi contoh pegawai.', NOW(), NOW()),
(3, CONCAT('TRX-', DATE_FORMAT(CURDATE(), '%Y%m%d'), '-0001'), CONCAT(CURDATE(), ' 14:20:00'), 2, NULL, 'Pembeli Internal C', 1, 1, 0, 14200.00, 14100.00, 14000.00, 71000.00, 141000.00, 0.00, 2, 15.00, 212000.00, 212000.00, 0.00, 0.00, 'manual', 'Transaksi manual contoh.', NOW(), NOW());

INSERT INTO sales_transaction_items (transaction_id, product_id, product_name_snapshot, weight_kg_snapshot, unit_price_snapshot, quantity, subtotal, total_kg_item) VALUES
(1, 1, 'Beras 5 Kg', 5.00, 14200.00, 2, 142000.00, 10.00),
(1, 2, 'Beras 10 Kg', 10.00, 14100.00, 1, 141000.00, 10.00),
(2, 1, 'Beras 5 Kg', 5.00, 14200.00, 1, 71000.00, 5.00),
(2, 3, 'Beras 25 Kg', 25.00, 14000.00, 1, 350000.00, 25.00),
(3, 1, 'Beras 5 Kg', 5.00, 14200.00, 1, 71000.00, 5.00),
(3, 2, 'Beras 10 Kg', 10.00, 14100.00, 1, 141000.00, 10.00);
