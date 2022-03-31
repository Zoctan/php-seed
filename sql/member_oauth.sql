DROP TABLE IF EXISTS `member_oauth`;
CREATE TABLE `member_oauth`
(
    `id`            BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '成员第三方授权id',
    `member_id`     BIGINT(20) UNSIGNED NOT NULL COMMENT '成员id',
    `oauth_type`    TINYINT(3) DEFAULT 0 COMMENT '授权类型：0无|1QQ|2微信',
    `oauth_id`      VARCHAR(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '授权id',
    `credential`    VARCHAR(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '凭证',
    `extra`         LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '额外信息',
    `created_at`    DATETIME DEFAULT NOW() COMMENT '创建于',
    `updated_at`    DATETIME DEFAULT NULL COMMENT '更新于',
    PRIMARY KEY (`id`),
    KEY `K_MID` (`member_id`),
    KEY `K_OID` (`oauth_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT ='成员第三方授权表';

INSERT INTO `member_oauth` VALUES
(1, 1, 2, 'ofsK5jvpz9392hYiZuF8Lg0Swlm0', NULL, '{"nickname":"Zoc","headimgurl":"https://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTLhM5jmtTNVRzSDXSYjlTP9WYicEfyeCiaW3eVvhGj3IMaNJPuYa1tN7LuXqtcLgWouiaOeJZMOQxKEg/132"}', '2022-01-01 00:00:00', NULL),
(2, 2, 2, 'ofsK5jvpz9392hYiZuF8Lg0Swlm1', NULL, '{"nickname":"USER","headimgurl":""}', '2022-01-01 00:00:00', NULL);