DROP TABLE IF EXISTS `member_role`;
CREATE TABLE `member_role`
(
    `id`          BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    `member_id`   BIGINT(20) UNSIGNED NOT NULL COMMENT 'member id',
    `role_id`     BIGINT(20) UNSIGNED NOT NULL DEFAULT 6 COMMENT 'role id',
    `created_at`  DATETIME DEFAULT NOW() COMMENT 'created at',
    `updated_at`  DATETIME DEFAULT NULL COMMENT 'updated at',
    PRIMARY KEY (`id`),
    KEY `K_MID` (`member_id`),
    KEY `K_RID` (`role_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT ='member role';

INSERT INTO `member_role` VALUES
(1, 1, 1, '2022-01-01 00:00:00', NULL),
(2, 2, 3, '2022-01-01 00:00:00', NULL),
(3, 2, 6, '2022-01-01 00:00:00', NULL),
(4, 3, 7, '2022-01-01 00:00:00', NULL);