<?php
declare(strict_types=1);

// CLI only. Run with the DB import account while console writes are paused.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__, 2) . '/config/setup.php';
require_once dirname(__DIR__, 2) . '/src/Database/DB.php';

$db = \Database\DB::getInstance();
$tables = ['nb_board', 'nb_board_manage', 'nb_board_locale', 'nb_board_comment'];
$migration = basename(__FILE__);
$apply = in_array('--apply', $argv, true);
$lockName = 'partnerit-board-engines-' . substr(hash('sha256', DB_NAME), 0, 20);
$lock = $db->prepare('SELECT GET_LOCK(?, 10)');
$lock->execute([$lockName]);
if ((int) $lock->fetchColumn() !== 1) { fwrite(STDERR, "Another migration is running.\n"); exit(1); }

try {
    $state = [];
    foreach ($tables as $table) {
        $stmt = $db->prepare('SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?');
        $stmt->execute([$table]);
        $engine = $stmt->fetchColumn();
        if (!$engine) throw new RuntimeException("Required table missing: $table");
        $state[$table] = (string) $engine;
        echo "$table: $engine\n";
    }
    if (!$apply) {
        echo "Check only. Back up the database, pause console writes, then run with --apply using the import account.\n";
    } else {
        $db->exec('CREATE TABLE IF NOT EXISTS migrations (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, migration VARCHAR(191) NOT NULL UNIQUE, batch INT UNSIGNED NOT NULL, applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB');
        $check = $db->prepare('SELECT 1 FROM migrations WHERE migration = ?');
        $check->execute([$migration]);
        $alreadyApplied = (bool) $check->fetchColumn();
        if ($alreadyApplied && count(array_filter($state, static fn($engine) => strcasecmp($engine, 'InnoDB') !== 0))) {
            throw new RuntimeException('Migration history and table engines differ. Inspect the database before continuing.');
        }
        if (!$alreadyApplied) {
            // Backups remain inside the private DB; no dump is written under the web root.
            foreach ($tables as $table) {
                $backup = $table . '_backup_261105';
                $stmt = $db->prepare('SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?');
                $stmt->execute([$backup]);
                if (!$stmt->fetchColumn()) {
                    $db->exec("CREATE TABLE `$backup` LIKE `$table`");
                    $db->exec("INSERT INTO `$backup` SELECT * FROM `$table`");
                }
                $sourceCount = (int) $db->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
                $backupCount = (int) $db->query("SELECT COUNT(*) FROM `$backup`")->fetchColumn();
                if ($sourceCount !== $backupCount) throw new RuntimeException("Backup verification failed: $table. Inspect the backup before retrying.");
            }
            foreach ($state as $table => $engine) {
                if (strcasecmp($engine, 'InnoDB') !== 0) $db->exec("ALTER TABLE `$table` ENGINE=InnoDB");
            }
            $batch = (int) $db->query('SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations')->fetchColumn();
            $db->prepare('INSERT INTO migrations (migration, batch) VALUES (?, ?)')->execute([$migration, $batch]);
            echo "Applied: $migration\n";
        } else {
            echo "Already applied: $migration\n";
        }
    }
} catch (Throwable $e) {
    fwrite(STDERR, 'Migration stopped: ' . $e->getMessage() . "\n");
    exit(1);
} finally {
    $db->prepare('SELECT RELEASE_LOCK(?)')->execute([$lockName]);
}
