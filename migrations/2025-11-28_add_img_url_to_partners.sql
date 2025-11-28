-- Migration: Add img_url column to partners table
-- Date: 2025-11-28
-- Description: Add certificate/award image URL field for partners

ALTER TABLE partners ADD COLUMN IF NOT EXISTS img_url VARCHAR(500) NULL AFTER logo_url;
