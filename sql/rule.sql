DROP TABLE IF EXISTS `rule`;
CREATE TABLE `rule`
(
    `id`           BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '规则id',
    `parent_id`    BIGINT(20) UNSIGNED DEFAULT 0 COMMENT '父规则id',
    `description`  VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '说明',
    `permission`   VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '权限',
    `created_at`   DATETIME DEFAULT NOW() COMMENT '创建于',
    `updated_at`   DATETIME DEFAULT NULL COMMENT '更新于',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT ='规则表';

INSERT INTO `rule` VALUES
(1, 0, 'Member', 'member', '2022-01-01 00:00:00', NULL),
(2, 1, 'Add', 'add', '2022-01-01 00:00:00', NULL),
(3, 1, 'Delete', 'delete', '2022-01-01 00:00:00', NULL),
(4, 1, 'Update', 'update', '2022-01-01 00:00:00', NULL),
(5, 1, 'List', 'list', '2022-01-01 00:00:00', NULL),
(6, 1, 'Detail', 'detail', '2022-01-01 00:00:00', NULL),

(7, 0, 'Role', 'role', '2022-01-01 00:00:00', NULL),
(8, 7, 'Add', 'add', '2022-01-01 00:00:00', NULL),
(9, 7, 'Delete', 'delete', '2022-01-01 00:00:00', NULL),
(10, 7, 'Update', 'update', '2022-01-01 00:00:00', NULL),
(11, 7, 'List', 'list', '2022-01-01 00:00:00', NULL),
(12, 7, 'Detail', 'detail', '2022-01-01 00:00:00', NULL);
