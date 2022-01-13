DROP TABLE IF EXISTS `log`;
CREATE TABLE `log`
(
    `id`           BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '日志Id',
    `level`        TINYINT(1) DEFAULT 0 COMMENT '级别：0正常|1警告|2错误',
    `account_id`   BIGINT(20) UNSIGNED NOT NULL COMMENT '用户Id',
    `account_name` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT '用户名',
    `content`      LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '内容',
    `ip`           BIGINT(20) UNSIGNED COMMENT 'IP',
    `ip_city`      VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'IP所属城市',
    `extra`        LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT '其他信息',
    `create_time`  DATETIME DEFAULT NOW() COMMENT '创建时间',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='日志表';
