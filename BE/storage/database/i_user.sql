DROP TABLE IF EXISTS `i_user`;

CREATE TABLE `i_user` (
   `user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード',
   `start_date` date NOT NULL COMMENT '開始日付',
   `end_date` date NOT NULL COMMENT '終了日付',
   `user_name` varchar(50) NOT NULL COMMENT 'ユーザ名',
   `mail_address` varchar(255) DEFAULT NULL COMMENT 'メールアドレス',
   `account_lock_flag` tinyint(1) unsigned DEFAULT NULL COMMENT 'アカウントロックフラグ',
   `account_lock_remarks` varchar(200) DEFAULT NULL COMMENT 'アカウントロック理由',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `i_user_PKC` PRIMARY KEY (`user_cd`,`start_date`)
 ) COMMENT='ユーザマスタ（履歴）:ユーザマスタ履歴' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE = utf8mb4_general_ci ;