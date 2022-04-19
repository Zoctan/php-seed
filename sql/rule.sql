DROP TABLE IF EXISTS `rule`;
CREATE TABLE `rule`
(
    `id`           BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    `parent_id`    BIGINT(20) UNSIGNED DEFAULT 0 COMMENT 'parent id',
    `description`  VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'description',
    `permission`   VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'permission',
    `created_at`   DATETIME DEFAULT NOW() COMMENT 'created at',
    `updated_at`   DATETIME DEFAULT NULL COMMENT 'updated at',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT ='rule';

INSERT INTO `rule` VALUES
(1, 0, 'Member', 'member', '2022-01-01 00:00:00', NULL),
(2, 1, 'Add', 'add', '2022-01-01 00:00:00', NULL),
(3, 1, 'Remove', 'remove', '2022-01-01 00:00:00', NULL),
(4, 1, 'Update', 'update', '2022-01-01 00:00:00', NULL),
(5, 1, 'List', 'list', '2022-01-01 00:00:00', NULL),
(6, 1, 'Detail', 'detail', '2022-01-01 00:00:00', NULL),

(7, 0, 'Role', 'role', '2022-01-01 00:00:00', NULL),
(8, 7, 'Add', 'add', '2022-01-01 00:00:00', NULL),
(9, 7, 'Remove', 'remove', '2022-01-01 00:00:00', NULL),
(10, 7, 'Update', 'update', '2022-01-01 00:00:00', NULL),
(11, 7, 'List', 'list', '2022-01-01 00:00:00', NULL),
(12, 7, 'Detail', 'detail', '2022-01-01 00:00:00', NULL),

(13, 0, 'Pair', 'pair', '2022-01-01 00:00:00', NULL),
(14, 13, 'Add', 'add', '2022-01-01 00:00:00', NULL),
(15, 13, 'Remove', 'remove', '2022-01-01 00:00:00', NULL),
(16, 13, 'Update', 'update', '2022-01-01 00:00:00', NULL),
(17, 13, 'List', 'list', '2022-01-01 00:00:00', NULL),

(18, 0, 'Log', 'log', '2022-01-01 00:00:00', NULL),
(19, 18, 'Remove', 'remove', '2022-01-01 00:00:00', NULL),
(20, 18, 'List', 'list', '2022-01-01 00:00:00', NULL);
