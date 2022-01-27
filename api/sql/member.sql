DROP TABLE IF EXISTS `member`;
CREATE TABLE `member`
(
    `id`            BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '成员id',
    `username`      VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '用户名',
    `password`      VARCHAR(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '密码',
    `status`        TINYINT(3) DEFAULT 1 COMMENT '状态：0异常|1正常',
    `logined_at`    DATETIME DEFAULT NULL COMMENT '登录于',
    `created_at`    DATETIME DEFAULT NOW() COMMENT '创建于',
    `updated_at`    DATETIME DEFAULT NULL COMMENT '更新于',
    PRIMARY KEY (`id`),
    KEY `K_U` (`username`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT ='成员表';

INSERT INTO `member` VALUES
(1, 'user', '$2y$10$ODodrsHcNKbAbF9ypnJ2qOuvQYYJ\/oK1lcOTX3ZNDFwD1Eg5wTtpa', 1, NULL, '2022-01-01 00:00:00', NULL),
(2, 'admin', '$2y$10$1yJq5zEGFDNXUXaRSNo3ruEjUgzWmxf3jgSPdlp8jMViVDg7Qsctq', 1, NULL, '2022-01-01 00:00:00', NULL),
(3, 'black', '$2y$10$FmPdINXjAxTAPfemZW5inObs4iDxb8b3tIaq83V8gOyoP5sqCx0xa', 0, NULL, '2022-01-01 00:00:00', NULL);