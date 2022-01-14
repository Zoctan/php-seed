DROP TABLE IF EXISTS `member_data`;
CREATE TABLE `member_data`
(
    `member_id`     BIGINT(20) UNSIGNED NOT NULL COMMENT '成员id',
    `avatar`        VARCHAR(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '头像',
    `nickname`      VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '昵称',
    `gender`        TINYINT(3) DEFAULT 0 COMMENT '性别：0无|1男|2女',
    `created_at`    DATETIME DEFAULT NOW() COMMENT '创建于',
    `updated_at`    DATETIME DEFAULT NULL COMMENT '更新于',
    PRIMARY KEY (`id`),
    UNIQUE KEY `U_MID` (`member_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT ='成员信息表';

INSERT INTO `member_data` VALUES
(1, '', 'USER', 1, '2022-01-01 00:00:00', NULL),
(2, '', 'ADMIN', 1, '2022-01-01 00:00:00', NULL);