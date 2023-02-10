DROP TABLE IF EXISTS `i_user_role`;

CREATE TABLE `i_user_role` (
   `user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード',
   `role` varchar(5) NOT NULL COMMENT 'ロール',
   `start_date` date NOT NULL COMMENT '開始日付',
   `end_date` date DEFAULT NULL COMMENT '終了日付',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `i_user_role_PKC` PRIMARY KEY (`user_cd`,`role`,`start_date`)
 ) COMMENT='ユーザロール（履歴）' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;