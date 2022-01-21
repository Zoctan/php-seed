DROP TABLE IF EXISTS `member`;
CREATE TABLE `member`
(
    `id`            BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '成员id',
    `username`      VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '用户名',
    `password`      VARCHAR(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '密码',
    `status`        TINYINT(3) DEFAULT 1 COMMENT '状态：0异常|1正常',
    `created_at`    DATETIME DEFAULT NOW() COMMENT '创建于',
    `login_at`      DATETIME DEFAULT NULL COMMENT '登录于',
    `updated_at`    DATETIME DEFAULT NULL COMMENT '更新于',
    PRIMARY KEY (`id`),
    KEY `K_U` (`username`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT ='成员表';

INSERT INTO `member` VALUES
(1, 'user', 'user', 1, '2022-01-01 00:00:00', NULL, NULL),
(2, 'admin', 'admin', 1, '2022-01-01 00:00:00', NULL, NULL);