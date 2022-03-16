DROP TABLE IF EXISTS `rule`;
CREATE TABLE `rule`
(
    `id`           BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '规则id',
    `description`  VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '说明',
    `permission`   VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '权限',
    `created_at`   DATETIME DEFAULT NOW() COMMENT '创建于',
    `updated_at`   DATETIME DEFAULT NULL COMMENT '更新于',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT ='规则表';

INSERT INTO `rule` VALUES
(1, 'Member:Add', 'member:add', '2022-01-01 00:00:00', NULL),
(2, 'Member:Delete', 'member:delete', '2022-01-01 00:00:00', NULL),
(3, 'Member:Update', 'member:update', '2022-01-01 00:00:00', NULL),
(4, 'Member:List', 'member:list', '2022-01-01 00:00:00', NULL),
(5, 'Member:Detail', 'member:detail', '2022-01-01 00:00:00', NULL),
(6, 'Role:Add', 'role:add', '2022-01-01 00:00:00', NULL),
(7, 'Role:Delete', 'role:delete', '2022-01-01 00:00:00', NULL),
(8, 'Role:Update', 'role:update', '2022-01-01 00:00:00', NULL),
(9, 'Role:List', 'role:list', '2022-01-01 00:00:00', NULL),
(10, 'Role:Detail', 'role:detail', '2022-01-01 00:00:00', NULL),
(11, 'Article:Add', 'article:add', '2022-01-01 00:00:00', NULL),
(12, 'Article:Delete', 'article:delete', '2022-01-01 00:00:00', NULL),
(13, 'Article:Update', 'article:update', '2022-01-01 00:00:00', NULL),
(14, 'Video:Add', 'video:add', '2022-01-01 00:00:00', NULL),
(15, 'Video:Delete', 'video:delete', '2022-01-01 00:00:00', NULL),
(16, 'Video:Update', 'video:update', '2022-01-01 00:00:00', NULL);