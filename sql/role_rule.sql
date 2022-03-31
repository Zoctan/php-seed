DROP TABLE IF EXISTS `role_rule`;
CREATE TABLE `role_rule`
(
    `id`            BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '角色规则id',
    `role_id`       BIGINT(20) UNSIGNED NOT NULL COMMENT '角色id',
    `rule_id`       BIGINT(20) UNSIGNED NOT NULL COMMENT '规则id',
    `created_at`    DATETIME DEFAULT NOW() COMMENT '创建于',
    PRIMARY KEY (`id`),
    KEY `K_ROID` (`role_id`),
    KEY `K_RUID` (`rule_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT ='角色规则表';
