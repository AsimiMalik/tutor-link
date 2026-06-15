-- Migration: add is_active column to users table
ALTER TABLE users
ADD COLUMN IF NOT EXISTS is_active TINYINT(1) NOT NULL DEFAULT 1;
