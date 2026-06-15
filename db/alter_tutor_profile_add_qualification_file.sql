-- Migration: add qualification_file to tutor_profile
ALTER TABLE tutor_profile
  ADD COLUMN qualification_file VARCHAR(255) NULL AFTER qualification;
