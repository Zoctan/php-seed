DROP TABLE IF EXISTS `account`;
CREATE TABLE `account`
(
    `id`            BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`      VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT '用户名',
    `password`      VARCHAR(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT '密码',
    `extra`         LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT '其他信息',
    `openid`        VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT '用户openid',
    `nickname`      VARCHAR(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT '昵称',
    `headimgurl`    VARCHAR(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT '头像链接',
    `role`          VARCHAR(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT 'user' COMMENT '用户角色：black|user|admin|superadmin',
    `create_time`   DATETIME DEFAULT NOW() COMMENT '创建时间',
    PRIMARY KEY (`id`),
    KEY `K_U` (`username`),
    KEY `K_O` (`openid`),
    KEY `K_N` (`nickname`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='用户表';

INSERT INTO `account` VALUES
(1, 'admin', 'admin', NULL, 'ofsK5jvpz9392hYiZuF8Lg0Swlm0', 'Zoc', 'https://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTLhM5jmtTNVRzSDXSYjlTP9WYicEfyeCiaW3eVvhGj3IMaNJPuYa1tN7LuXqtcLgWouiaOeJZMOQxKEg/132', 'superadmin', '2021-07-10 00:00:00');