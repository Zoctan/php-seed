DROP TABLE IF EXISTS `member_role`;
CREATE TABLE `member_role`
(
    `id`            BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '成员角色id',
    `member_id`     BIGINT(20) UNSIGNED NOT NULL COMMENT '成员id',
    `role_id`       BIGINT(20) UNSIGNED NOT NULL DEFAULT 1 COMMENT '角色id',
    `created_at`    DATETIME DEFAULT NOW() COMMENT '创建于',
    `updated_at`    DATETIME DEFAULT NULL COMMENT '更新于',
    PRIMARY KEY (`id`),
    KEY `K_MID` (`member_id`),
    KEY `K_RID` (`role_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT ='成员角色表';

INSERT INTO `member_role` VALUES
(1, 1, 1, '2022-01-01 00:00:00', NULL),
(2, 2, 2, '2022-01-01 00:00:00', NULL);