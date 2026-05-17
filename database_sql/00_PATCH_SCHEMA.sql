-- ============================================================
-- PATCH SCHEMA: Untuk database yang sudah terlanjur diimport
-- dari versi lama `penjualan_beras.sql` tanpa kolom-kolom
-- tambahan yang dibutuhkan oleh versi terbaru sistem.
--
-- File ini aman dijalankan berulang kali (idempotent).
-- Membutuhkan MySQL 8.0+ untuk sintaks ADD COLUMN IF NOT EXISTS.
-- ============================================================

USE sistem_penjualan_beras;

-- ------------------------------------------------------------
-- quick_templates : kolom kuantitas tetap + diskon + soft delete
-- ------------------------------------------------------------
ALTER TABLE quick_templates
    ADD COLUMN IF NOT EXISTS qty_5kg  INT NOT NULL DEFAULT 0 AFTER template_name,
    ADD COLUMN IF NOT EXISTS qty_10kg INT NOT NULL DEFAULT 0 AFTER qty_5kg,
    ADD COLUMN IF NOT EXISTS qty_25kg INT NOT NULL DEFAULT 0 AFTER qty_10kg,
    ADD COLUMN IF NOT EXISTS discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00 AFTER qty_25kg,
    ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL AFTER updated_at;

-- ------------------------------------------------------------
-- sales_transactions : kolom snapshot kuantitas/harga/subtotal,
-- total harga, dan sumber transaksi
-- ------------------------------------------------------------
ALTER TABLE sales_transactions
    ADD COLUMN IF NOT EXISTS qty_5kg       INT NOT NULL DEFAULT 0           AFTER customer_name,
    ADD COLUMN IF NOT EXISTS qty_10kg      INT NOT NULL DEFAULT 0           AFTER qty_5kg,
    ADD COLUMN IF NOT EXISTS qty_25kg      INT NOT NULL DEFAULT 0           AFTER qty_10kg,
    ADD COLUMN IF NOT EXISTS price_5kg     DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER qty_25kg,
    ADD COLUMN IF NOT EXISTS price_10kg    DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER price_5kg,
    ADD COLUMN IF NOT EXISTS price_25kg    DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER price_10kg,
    ADD COLUMN IF NOT EXISTS subtotal_5kg  DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER price_25kg,
    ADD COLUMN IF NOT EXISTS subtotal_10kg DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER subtotal_5kg,
    ADD COLUMN IF NOT EXISTS subtotal_25kg DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER subtotal_10kg,
    ADD COLUMN IF NOT EXISTS total_harga   DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER grand_total,
    ADD COLUMN IF NOT EXISTS discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00 AFTER total_harga,
    ADD COLUMN IF NOT EXISTS discount_amount  DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER discount_percent,
    ADD COLUMN IF NOT EXISTS source_transaksi ENUM('manual','template') NOT NULL DEFAULT 'manual' AFTER discount_amount;

-- ------------------------------------------------------------
-- product_prices : kolom delta perubahan harga
-- ------------------------------------------------------------
ALTER TABLE product_prices
    ADD COLUMN IF NOT EXISTS price_change DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER price;

-- ------------------------------------------------------------
-- users : kolom foto profil (jika belum ada)
-- ------------------------------------------------------------
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS profile_photo VARCHAR(255) NULL AFTER is_active;

-- ------------------------------------------------------------
-- sale_limit_settings : max_total_kg dari DECIMAL ke INT UNSIGNED
-- (MODIFY COLUMN bersifat in-place; aman dijalankan ulang.)
-- ------------------------------------------------------------
ALTER TABLE sale_limit_settings
    MODIFY COLUMN max_total_kg INT UNSIGNED NOT NULL DEFAULT 0;
