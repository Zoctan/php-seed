DROP TABLE IF EXISTS `system`;
CREATE TABLE `system`
(
    `id`           BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '系统配置id',
    `description`  LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '说明',
    `key`          VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '键',
    `value`        LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '值',
    `created_at`    DATETIME DEFAULT NOW() COMMENT '创建于',
    `updated_at`    DATETIME DEFAULT NULL COMMENT '更新于',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT ='系统动态配置表';

INSERT INTO `system` VALUES
(6, '微信小程序配置', 'miniprogram', '{"name":"数字都斛","appid":"wxc21c6727ff2a5e4c","path":"","extraData":"","auth":{"need":false,"validPeriod":2592000000}}', '2022-01-01 00:00:00', NULL),
(7, '微信小程序底部导航', 'miniprogramTabbar', '[{"id":1,"order":5,"title":"首页","icon":"wap-home","jumpType":"switchTab","path":"/pages/home/index"},{"id":2,"order":4,"title":"实景VR","icon":"map-marked","jumpType":"switchTab","path":"/pages/vr/index"},{"id":3,"order":3,"title":"慢直播","icon":"video","jumpType":"switchTab","path":"/pages/slowLive/index"},{"id":4,"order":2,"title":"电商","icon":"shopping-cart","jumpType":"navigateToMiniProgram","path":"mallMiniProgram"},{"id":5,"order":1,"title":"数字人才","icon":"friends","jumpType":"navigateToMiniProgram","path":"jobMiniProgram"}]', '2022-01-01 00:00:00', NULL),
(8, '频道', 'channel', '[{"id":1,"order":1,"title":"推荐"},{"id":2,"order":2,"title":"本地"}]', '2022-01-01 00:00:00', NULL),
(9, '作品审核状态', 'workStatus', '[{"value":0,"title":"未审核"},{"value":1,"title":"审核通过"},{"value":2,"title":"审核未通过"}]', '2022-01-01 00:00:00', NULL),
(10, '作品是否显示', 'workShow', '[{"value":0,"title":"下架"},{"value":1,"title":"展示"}]', '2022-01-01 00:00:00', NULL);