DROP TABLE IF EXISTS `t_message_read`;

CREATE TABLE `t_message_read` (
   `message_id` bigint(20) unsigned NOT NULL COMMENT 'メッセージID',
   `user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード',
   `read_datetime` datetime NOT NULL COMMENT '既読日時',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_message_read_PKC` PRIMARY KEY (`message_id`,`user_cd`)
 ) COMMENT='メッセージ既読管理' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;