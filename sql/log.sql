DROP TABLE IF EXISTS `log`;
CREATE TABLE `log`
(
    `id`            BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '日志id',
    `member_id`     BIGINT(20) UNSIGNED NOT NULL COMMENT '成员id',
    `level`         TINYINT(3) DEFAULT 0 COMMENT '级别：0正常|1警告|2错误',
    `content`       LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内容',
    `ip`            BIGINT(20) UNSIGNED COMMENT 'IP',
    `ip_city`       VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'IP所属城市',
    `extra`         LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '其他信息',
    `created_at`    DATETIME DEFAULT NOW() COMMENT '创建于',
    `updated_at`    DATETIME DEFAULT NULL COMMENT '更新于',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT ='日志表';
