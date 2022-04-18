DROP TABLE IF EXISTS `pair`;
CREATE TABLE `pair`
(
    `id`            BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    `description`   LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'description',
    `key`           VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'key',
    `value`         LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'json value',
    `created_at`    DATETIME DEFAULT NOW() COMMENT 'created at',
    `updated_at`    DATETIME DEFAULT NULL COMMENT 'updated at',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT ='key value pair';

INSERT INTO `pair` VALUES
(1, 'Member status map', 'memberStatusMap', '[{"value":0,"label":"Abnormal","color":"danger"},{"value":1,"label":"Normal","color":"success"}]', '2022-01-01 00:00:00', NULL),
(2, 'Member lock map', 'memberLockMap', '[{"value":0,"label":"Unlock","color":"success"},{"value":1,"label":"Lock","color":"danger"}]', '2022-01-01 00:00:00', NULL),
(3, 'Member gender map', 'memberGenderMap', '[{"value":0,"label":"None","color":"info"},{"value":1,"label":"Male","color":""},{"value":2,"label":"Female","color":"warning"}]', '2022-01-01 00:00:00', NULL),
(4, 'Role has all rule map', 'roleHasAllRuleMap', '[{"value":0,"label":"No","color":"danger"},{"value":1,"label":"Yes","color":"success"}]', '2022-01-01 00:00:00', NULL),
(5, 'Role lock map', 'roleLockMap', '[{"value":0,"label":"Unlock","color":"success"},{"value":1,"label":"Lock","color":"danger"}]', '2022-01-01 00:00:00', NULL),
(6, 'Log level map', 'logLevelMap', '[{"value":0,"label":"Info","color":"info"},{"value":1,"label":"warn","color":"warning"},{"value":2,"label":"error","color":"danger"}]', '2022-01-01 00:00:00', NULL);
