DROP TABLE IF EXISTS `system`;
CREATE TABLE `system`
(
    `id`            BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    `description`   LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'description',
    `key`           VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'key',
    `value`         LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'json value',
    `created_at`    DATETIME DEFAULT NOW() COMMENT 'created at',
    `updated_at`    DATETIME DEFAULT NULL COMMENT 'updated at',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT ='system dynamic key value pair';
