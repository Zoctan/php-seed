DROP TABLE IF EXISTS `system`;
CREATE TABLE `system`
(
    `id`           BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '系统配置Id',
    `description`  LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '说明',
    `key`          VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT '键',
    `value`        LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '值',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='系统动态配置表';

INSERT INTO `system` VALUES
(1, '网站', 'web', '{"webName":"PHP-ADMIN"}'),
(2, 'Redis缓存', 'redisCache', '{"status":1,"option":{"scheme":"tcp","host":"127.0.0.1","port":6379,"password":"root"}}'),
(3, '微信', 'wechat', '{"credential":{"appId":"wx07f8fd1b50ae8109","appSecret":"aab76a401717a502eb32aa4b37f96570"}}'),
(4, '微信网页jssdk的token保存，暂时用不到', 'jssdk', '{"access_token":"","expires_in":7200,"get_time":"2021-08-01 00:00:00"}'),
(5, '微信小程序需要用到哪些配置', 'miniprogramNeedSetting', '["slowLiveUrl","miniprogram","miniprogramTabbar","miniprogramHomeSwiper","mallMiniProgram","jobMiniProgram","channel"]'),
(6, '权限列表', 'permission', '[]'),
(7, '角色列表', 'role', '[{"name":"black","des":"黑名单用户","permission":[]},{"name":"user","des":"普通用户","permission":[]},{"name":"admin","des":"运营员","permission":[]},{"name":"superadmin","des":"管理员","permission":[]}]'),
(8, '慢直播链接', 'slowLiveUrl', '{"rtmp":"","m3u8":"","flv":""}'),
(9, '微信小程序配置', 'miniprogram', '{"name":"数字都斛","appid":"wxc21c6727ff2a5e4c","path":"","extraData":"","auth":{"need":false,"validPeriod":2592000000}}'),
(10, '微信小程序底部导航', 'miniprogramTabbar', '[{"id":1,"order":5,"title":"首页","icon":"wap-home","jumpType":"switchTab","path":"/pages/home/index"},{"id":2,"order":4,"title":"实景VR","icon":"map-marked","jumpType":"switchTab","path":"/pages/vr/index"},{"id":3,"order":3,"title":"慢直播","icon":"video","jumpType":"switchTab","path":"/pages/slowLive/index"},{"id":4,"order":2,"title":"电商","icon":"shopping-cart","jumpType":"navigateToMiniProgram","path":"mallMiniProgram"},{"id":5,"order":1,"title":"数字人才","icon":"friends","jumpType":"navigateToMiniProgram","path":"jobMiniProgram"}]'),
(11, '微信小程序首页轮播图', 'miniprogramHomeSwiper', '[{"id":1,"order":10,"url":"upload/image/1.jpg","name":"1.jpg"},{"id":2,"order":9,"url":"upload/image/2.jpg","name":"2.jpg"},{"id":3,"order":8,"url":"upload/image/3.jpg","name":"3.jpg"},{"id":4,"order":7,"url":"upload/image/4.jpg","name":"4.jpg"},{"id":5,"order":6,"url":"upload/image/5.jpg","name":"5.jpg"},{"id":6,"order":5,"url":"upload/image/6.jpg","name":"6.jpg"},{"id":7,"order":4,"url":"upload/image/7.jpg","name":"7.jpg"},{"id":8,"order":3,"url":"upload/image/8.jpg","name":"8.jpg"},{"id":9,"order":2,"url":"upload/image/9.jpg","name":"9.jpg"},{"id":10,"order":1,"url":"upload/image/10.jpg","name":"10.jpg"}]'),
(12, '商城小程序配置', 'mallMiniProgram', '{"name":"广电商城","appid":"wx4a1fed1c6f7e2b68","path":"","extraData":""}'),
(13, '招聘小程序配置', 'jobMiniProgram', '{"name":"数字人才","appid":"wx4a1fed1c6f7e2b68","path":"","extraData":""}'),
(14, '频道', 'channel', '[{"id":1,"order":1,"title":"推荐"},{"id":2,"order":2,"title":"本地"}]'),
(15, '作品审核状态', 'worksStatus', '[{"value":0,"title":"未审核"},{"value":1,"title":"审核通过"},{"value":2,"title":"审核未通过"}]'),
(16, '作品是否显示', 'worksShow', '[{"value":0,"title":"下架"},{"value":1,"title":"展示"}]');