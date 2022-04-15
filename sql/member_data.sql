DROP TABLE IF EXISTS `member_data`;
CREATE TABLE `member_data`
(
    `id`            BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    `member_id`     BIGINT(20) UNSIGNED NOT NULL COMMENT 'member id',
    `avatar`        VARCHAR(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'avatar',
    `nickname`      VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'nickname',
    `gender`        TINYINT(3) DEFAULT 0 COMMENT '0:none | 1:male | 2:female',
    `created_at`    DATETIME DEFAULT NOW() COMMENT 'created at',
    `updated_at`    DATETIME DEFAULT NULL COMMENT 'updated at',
    PRIMARY KEY (`id`),
    KEY `K_MID` (`member_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT ='member data';

INSERT INTO `member_data` VALUES
(1, 1, 'http://127.0.0.1/php-seed/upload?filename=avatar.jpg&type=image', 'admin', 1, '2022-01-01 00:00:00', NULL),
(2, 2, '', 'user001', 1, '2022-01-01 00:00:00', NULL),
(3, 3, '', 'black001', 0, '2022-01-01 00:00:00', NULL);