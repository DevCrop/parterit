-- phpMyAdmin/FTP deployment alternative. Select the same database as .env first.
-- RUN ONCE ONLY on the old nb_request schema. Use the CLI PHP migration for safe retries.
-- No customer rows are inserted, deleted or overwritten.
CREATE TABLE IF NOT EXISTS nb_request_backup_20261007 LIKE nb_request;
INSERT IGNORE INTO nb_request_backup_20261007
    (no,sitekey,request_type,name,phone,gender,residence,residence_other,interest_codes,ip_address,user_agent,regdate)
SELECT no,sitekey,request_type,name,phone,gender,residence,residence_other,interest_codes,ip_address,user_agent,regdate FROM nb_request;
ALTER TABLE nb_request
    ADD COLUMN company VARCHAR(100) NULL,
    ADD COLUMN email VARCHAR(190) NULL,
    ADD COLUMN message TEXT NULL,
    ADD COLUMN privacy_consent TINYINT UNSIGNED NOT NULL DEFAULT 0,
    ADD COLUMN consent_version VARCHAR(32) NULL,
    ADD COLUMN agreed_at DATETIME NULL,
    ADD COLUMN submission_key CHAR(64) NULL,
    ADD UNIQUE KEY uq_nb_request_submission_key (submission_key);
