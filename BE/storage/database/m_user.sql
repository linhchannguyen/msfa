DROP TABLE IF EXISTS `m_user`;

CREATE TABLE `m_user` (
   `user_cd` varchar(15) NOT NULL COMMENT 'ユーザコード',
   `user_name` varchar(50) DEFAULT NULL COMMENT 'ユーザ名',
   `mail_address` varchar(255) DEFAULT NULL COMMENT 'メールアドレス',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_user_PKC` PRIMARY KEY (`user_cd`)
 ) COMMENT='ユーザマスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;