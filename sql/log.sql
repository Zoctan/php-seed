DROP TABLE IF EXISTS `log`;
CREATE TABLE `log`
(
    `id`              BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    `member_id`       BIGINT(20) UNSIGNED NOT NULL COMMENT 'member id',
    `member_username` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'member username',
    `level`           TINYINT(3) DEFAULT 0 COMMENT '0:info | 1:warn| 2:error',
    `content`         LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'content',
    `ip`              BIGINT(20) UNSIGNED COMMENT 'ipv4',
    `ip_city`         VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'ip city',
    `extra`           LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'json extra info',
    `created_at`      DATETIME DEFAULT NOW() COMMENT 'created at',
    `updated_at`      DATETIME DEFAULT NULL COMMENT 'updated at',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT ='log';

INSERT INTO `log` VALUES
(1, 1, 'admin', 0, 'update member id: 2', 16909060, '广东-深圳市', NULL, '2022-01-01 00:00:00', NULL),
(2, 2, 'user001', 1, 'update passwrod', 16909061, '广东-广州市', NULL, '2022-01-01 00:00:00', NULL),
(3, 3, 'black001', 2, 'login error', 16909062, '上海-静安区', NULL, '2022-01-01 00:00:00', NULL),
(4, 3, 'black001', 2, 'test', 16909062, '上海-静安区', NULL, '2022-01-01 00:00:00', NULL);