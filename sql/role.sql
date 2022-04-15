DROP TABLE IF EXISTS `role`;
CREATE TABLE `role`
(
    `id`           BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    `parent_id`    BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'parent id',
    `name`         VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'name',
    `has_all_rule` TINYINT(3) DEFAULT 0 COMMENT '0:no | 1:yes',
    `lock`         TINYINT(3) DEFAULT 0 COMMENT 'unchangeable: 0:unlock | 1:lock',
    `created_at`   DATETIME DEFAULT NOW() COMMENT 'created at',
    `updated_at`   DATETIME DEFAULT NULL COMMENT 'updated at',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT ='role';

INSERT INTO `role` VALUES
(1, 0, 'SuperAdmin', 1, 1, '2022-01-01 00:00:00', NULL),
(2, 0, 'Department', 0, 0, '2022-01-01 00:00:00', NULL),
(3, 2, 'HR', 0, 0, '2022-01-01 00:00:00', NULL),
(4, 2, 'Manager', 0, 0, '2022-01-01 00:00:00', NULL),
(5, 0, 'User', 0, 0, '2022-01-01 00:00:00', NULL),
(6, 5, 'NormalUser', 0, 0, '2022-01-01 00:00:00', NULL),
(7, 5, 'BlackUser', 0, 0, '2022-01-01 00:00:00', NULL);
