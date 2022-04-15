DROP TABLE IF EXISTS `log`;
CREATE TABLE `log`
(
    `id`            BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    `member_id`     BIGINT(20) UNSIGNED NOT NULL COMMENT 'member id',
    `level`         TINYINT(3) DEFAULT 0 COMMENT '0:info | 1:warn| 2:error',
    `content`       LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'content',
    `ip`            BIGINT(20) UNSIGNED COMMENT 'ipv4',
    `ip_city`       VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'ip city',
    `extra`         LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'json extra info',
    `created_at`    DATETIME DEFAULT NOW() COMMENT 'created at',
    `updated_at`    DATETIME DEFAULT NULL COMMENT 'updated at',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT ='log';
