DROP TABLE IF EXISTS `role_rule`;
CREATE TABLE `role_rule`
(
    `id`            BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    `role_id`       BIGINT(20) UNSIGNED NOT NULL COMMENT 'role id',
    `rule_id`       BIGINT(20) UNSIGNED NOT NULL COMMENT 'rule id',
    `created_at`    DATETIME DEFAULT NOW() COMMENT 'created at',
    `updated_at`    DATETIME DEFAULT NULL COMMENT 'updated at',
    PRIMARY KEY (`id`),
    KEY `K_ROID` (`role_id`),
    KEY `K_RUID` (`rule_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT ='role rule';
