<?php

declare(strict_types=1);

/**
 * Creates the console authentication schema for the active project database.
 * Run from the project root: php database/migrations/260916-0845-create-admin-schema.php
 * No credentials or administrator account are seeded here.
 */

require_once dirname(__DIR__, 2) . '/inc/lib/base.class.php';

$pdo = \Database\DB::getInstance();
$migration = basename(__FILE__);
$pdo->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `migrations` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `migration` varchar(191) NOT NULL,
    `batch` int unsigned NOT NULL,
    `applied_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_migrations_migration` (`migration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);

$check = $pdo->prepare('SELECT 1 FROM migrations WHERE migration = :migration LIMIT 1');
$check->execute(['migration' => $migration]);
if ($check->fetchColumn()) {
    fwrite(STDOUT, "Already applied: {$migration}\n");
    exit(0);
}

$pdo->beginTransaction();
try {
    $pdo->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `nb_roles` (
    `role_id` int NOT NULL AUTO_INCREMENT,
    `role_name` varchar(50) NOT NULL,
    `description` text DEFAULT NULL,
    PRIMARY KEY (`role_id`),
    UNIQUE KEY `uq_nb_roles_name` (`role_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
    $pdo->exec(<<<'SQL'
INSERT IGNORE INTO `nb_roles` (`role_id`, `role_name`, `description`) VALUES
    (1, 'superadmin', '최고 관리자 - 모든 기능 사용 가능'),
    (2, 'manager', '중간 관리자 - 일부 기능 제한'),
    (3, 'external', '외부인 - 조회 전용')
SQL);
    $pdo->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `nb_admin` (
    `no` int NOT NULL AUTO_INCREMENT,
    `sitekey` varchar(32) NOT NULL,
    `uid` varchar(25) NOT NULL,
    `upwd` varchar(255) NOT NULL,
    `uname` varchar(25) NOT NULL,
    `active_status` enum('N','Y') NOT NULL DEFAULT 'N',
    `role_id` int NOT NULL DEFAULT 3,
    `email` varchar(100) DEFAULT NULL,
    `phone` varchar(20) DEFAULT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`no`),
    UNIQUE KEY `uq_nb_admin_site_uid` (`sitekey`, `uid`),
    KEY `idx_nb_admin_role` (`role_id`),
    KEY `idx_nb_admin_active` (`sitekey`, `active_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
    $batch = (int) $pdo->query('SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations')->fetchColumn();
    $record = $pdo->prepare('INSERT INTO migrations (migration, batch) VALUES (:migration, :batch)');
    $record->execute(['migration' => $migration, 'batch' => $batch]);
    $pdo->commit();
    fwrite(STDOUT, "Applied: {$migration} (batch {$batch})\n");
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    throw $exception;
}
