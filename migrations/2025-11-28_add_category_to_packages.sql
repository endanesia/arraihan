-- Migration: Add category column to packages table
-- Date: 2025-11-28
-- Description: Menambahkan kolom category untuk mengkategorikan paket perjalanan
--              (Umroh, Badal Umroh, Badal Haji, Halal Tour, Ziarah, Dana Talangan, Tabungan Umroh)

ALTER TABLE packages 
ADD COLUMN IF NOT EXISTS category VARCHAR(100) NULL 
AFTER title;

-- Optional: Set default category for existing packages
-- UPDATE packages SET category = 'Umroh' WHERE category IS NULL OR category = '';
