-- BlockAB Database Tables
-- Run this SQL in your MODX database
-- Replace 'mdx_' with your actual MODX table prefix if different

CREATE TABLE IF NOT EXISTS `mdx_blockab_test` (
    `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `test_group` VARCHAR(100) NOT NULL,
    `active` TINYINT NOT NULL DEFAULT '1',
    `archived` TINYINT NOT NULL DEFAULT '0',
    `smartoptimize` TINYINT NOT NULL DEFAULT '1',
    `threshold` INT UNSIGNED NOT NULL DEFAULT '100',
    `randomize` INT UNSIGNED NOT NULL DEFAULT '25',
    `resources` TEXT NULL,
    `contexts` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    INDEX `test_group` (`test_group`),
    INDEX `active` (`active`),
    INDEX `archived` (`archived`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mdx_blockab_variation` (
    `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    `test` INT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `variant_key` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `active` TINYINT NOT NULL DEFAULT '1',
    `weight` INT UNSIGNED NOT NULL DEFAULT '100',
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `test` (`test`),
    INDEX `variant_key` (`variant_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mdx_blockab_pick` (
    `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    `test` INT UNSIGNED NOT NULL,
    `variation` INT UNSIGNED NOT NULL,
    `date` INT UNSIGNED NOT NULL,
    `amount` INT UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    INDEX `test` (`test`),
    INDEX `variation` (`variation`),
    INDEX `date` (`date`),
    INDEX `test_date` (`test`, `date`),
    INDEX `variation_date` (`variation`, `date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mdx_blockab_conversion` (
    `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    `test` INT UNSIGNED NOT NULL,
    `variation` INT UNSIGNED NOT NULL,
    `date` INT UNSIGNED NOT NULL,
    `amount` INT UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    INDEX `test` (`test`),
    INDEX `variation` (`variation`),
    INDEX `date` (`date`),
    INDEX `test_date` (`test`, `date`),
    INDEX `variation_date` (`variation`, `date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
