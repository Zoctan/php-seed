DROP TABLE IF EXISTS `video`;
CREATE TABLE `video`
(
    `id`            BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '视频id',
    `order`         BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '顺序',
    `member_id`     BIGINT(20) UNSIGNED NOT NULL COMMENT '成员id',
    `images`        LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '封面图列表',
    `title`         LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
    `videos`        LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '视频列表',
    `brief`         LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '简介',
    `channel_id`    BIGINT(20) UNSIGNED NOT NULL COMMENT '频道id',
    `status`        TINYINT(3) DEFAULT 1 COMMENT '审核状态：0未审核|1审核通过|2审核未通过',
    `show`          TINYINT(3) DEFAULT 0 COMMENT '展示状态：0下架|1展示',
    `extra`         LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '其他信息',
    `created_at`    DATETIME DEFAULT NOW() COMMENT '创建于',
    `updated_at`    DATETIME DEFAULT NULL COMMENT '更新于',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT ='视频表';