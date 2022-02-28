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
(1, '文章：添加', 'article:add', '2022-01-01 00:00:00', NULL),
(2, '文章：删除', 'article:delete', '2022-01-01 00:00:00', NULL),
(3, '文章：修改', 'article:update', '2022-01-01 00:00:00', NULL),
(4, '视频：添加', 'video:add', '2022-01-01 00:00:00', NULL),
(5, '视频：删除', 'video:delete', '2022-01-01 00:00:00', NULL),
(6, '视频：修改', 'video:update', '2022-01-01 00:00:00', NULL);