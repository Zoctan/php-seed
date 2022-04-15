DROP TABLE IF EXISTS `member`;
CREATE TABLE `member`
(
    `id`            BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    `username`      VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'username',
    `password`      VARCHAR(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'password',
    `status`        TINYINT(3) DEFAULT 1 COMMENT '0:abnormal | 1:normal',
    `lock`          TINYINT(3) DEFAULT 0 COMMENT 'unchangeable: 0:unlock | 1:lock',
    `logined_at`    DATETIME DEFAULT NULL COMMENT 'logined at',
    `created_at`    DATETIME DEFAULT NOW() COMMENT 'created at',
    `updated_at`    DATETIME DEFAULT NULL COMMENT 'updated at',
    PRIMARY KEY (`id`),
    KEY `K_U` (`username`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT ='member';

INSERT INTO `member` VALUES
(1, 'admin', '$2y$10$1yJq5zEGFDNXUXaRSNo3ruEjUgzWmxf3jgSPdlp8jMViVDg7Qsctq', 1, 0, NULL, '2022-01-01 00:00:00', NULL),
(2, 'user', '$2y$10$ODodrsHcNKbAbF9ypnJ2qOuvQYYJ\/oK1lcOTX3ZNDFwD1Eg5wTtpa', 1, 0, NULL, '2022-01-01 00:00:00', NULL),
(3, 'black', '$2y$10$FmPdINXjAxTAPfemZW5inObs4iDxb8b3tIaq83V8gOyoP5sqCx0xa', 0, 1, NULL, '2022-01-01 00:00:00', NULL);