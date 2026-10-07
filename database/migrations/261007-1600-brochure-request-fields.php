<?php
declare(strict_types=1);
// CLI only. Additive and restart-safe; the existing requests remain unchanged.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__, 2) . '/inc/lib/base.class.php';
$db = \Database\DB::getInstance();
$migration = basename(__FILE__);
$db->exec('CREATE TABLE IF NOT EXISTS migrations (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, migration VARCHAR(191) NOT NULL UNIQUE, batch INT UNSIGNED NOT NULL, applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP)');
$check = $db->prepare('SELECT 1 FROM migrations WHERE migration = ?');
$check->execute([$migration]);
if ($check->fetchColumn()) { echo "Already applied\n"; exit; }
// Existing table backup before schema changes. This copy contains private data and stays inside the database.
$db->exec('CREATE TABLE IF NOT EXISTS nb_request_backup_20261007 LIKE nb_request');
$backupColumns = $db->query('SHOW COLUMNS FROM nb_request_backup_20261007')->fetchAll(PDO::FETCH_COLUMN);
$columnList = implode(',', array_map(static fn($column) => '`' . str_replace('`', '``', $column) . '`', $backupColumns));
$db->exec("INSERT IGNORE INTO nb_request_backup_20261007 ($columnList) SELECT $columnList FROM nb_request");
$fields = [
    'company' => 'VARCHAR(100) NULL', 'email' => 'VARCHAR(190) NULL', 'message' => 'TEXT NULL',
    'privacy_consent' => 'TINYINT UNSIGNED NOT NULL DEFAULT 0', 'consent_version' => 'VARCHAR(32) NULL',
    'agreed_at' => 'DATETIME NULL', 'submission_key' => 'CHAR(64) NULL',
];
$columns = $db->query('SHOW COLUMNS FROM nb_request')->fetchAll(PDO::FETCH_COLUMN);
foreach ($fields as $name => $definition) {
    if (!in_array($name, $columns, true)) $db->exec("ALTER TABLE nb_request ADD COLUMN `$name` $definition");
}
$indexes = $db->query('SHOW INDEX FROM nb_request')->fetchAll(PDO::FETCH_ASSOC);
if (!in_array('uq_nb_request_submission_key', array_column($indexes, 'Key_name'), true)) $db->exec('ALTER TABLE nb_request ADD UNIQUE KEY uq_nb_request_submission_key (submission_key)');
$batch = (int) $db->query('SELECT COALESCE(MAX(batch),0)+1 FROM migrations')->fetchColumn();
$db->prepare('INSERT INTO migrations (migration,batch) VALUES (?,?)')->execute([$migration, $batch]);
echo "Applied: $migration\n";
