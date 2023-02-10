DROP TABLE IF EXISTS `t_inform`;

CREATE TABLE `t_inform` (
   `inform_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '通知ID',
   `inform_category_cd` varchar(3) NOT NULL COMMENT '通知種別コード',
   `inform_datetime` datetime NOT NULL COMMENT '通知日時',
   `inform_user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード(通知先)',
   `inform_contents` text NOT NULL COMMENT '本文',
   `archive_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'アーカイブフラグ',
   `informed_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '通知済フラグ',
   `read_flag` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '既読フラグ',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_inform_PKC` PRIMARY KEY (`inform_id`)
 ) COMMENT='ユーザ通知' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;