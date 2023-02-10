DROP TABLE IF EXISTS `t_file`;

CREATE TABLE `t_file` (
   `file_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ファイルID',
   `use_type` varchar(10) NOT NULL COMMENT '利用種別:講演会/プロフィール/資材',
   `file_name` varchar(100) NOT NULL COMMENT '保存ファイル名',
   `display_name` varchar(100) DEFAULT NULL COMMENT '表示ファイル名',
   `mime_type` varchar(128) NOT NULL COMMENT 'MIMEタイプ',
   `file_url` text DEFAULT NULL COMMENT 'ファイルURL',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `t_file_PKC` PRIMARY KEY (`file_id`)
 ) COMMENT='ファイル' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;