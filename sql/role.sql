DROP TABLE IF EXISTS `role`;
CREATE TABLE `role`
(
    `id`           BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '角色id',
    `name`         VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '名称',
    `has_all_rule` TINYINT(3) DEFAULT 0 COMMENT '是否拥有所有权限：0否|1是',
    `lock`         TINYINT(3) DEFAULT 0 COMMENT '锁定，不可修改：0否|1是',
    `created_at`   DATETIME DEFAULT NOW() COMMENT '创建于',
    `updated_at`   DATETIME DEFAULT NULL COMMENT '更新于',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT ='角色表';

INSERT INTO `role` VALUES
(1, 'SuperAdmin', 1, 1, '2022-01-01 00:00:00', NULL),
(2, 'NormalUser', 0, 0, '2022-01-01 00:00:00', NULL),
(3, 'BlackUser', 0, 1, '2022-01-01 00:00:00', NULL);