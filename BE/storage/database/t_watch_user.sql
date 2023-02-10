DROP TABLE IF EXISTS `t_watch_user`;

CREATE TABLE `t_watch_user` (
   `user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード',
   `watch_user_cd` varchar(15) NOT NULL COMMENT 'ウォッチユーザコード',
   `display_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '表示フラグ',
   `display_color_cd` varchar(3) NOT NULL COMMENT '表示色コード',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_watch_user_PKC` PRIMARY KEY (`user_cd`,`watch_user_cd`)
 ) COMMENT='ウォッチユーザ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;