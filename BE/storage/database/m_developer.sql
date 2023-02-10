DROP TABLE IF EXISTS `m_developer`;

CREATE TABLE `m_developer` (
   `developer_cd` varchar(10) NOT NULL COMMENT '開発者コード',
   `developer_name` varchar(20) DEFAULT NULL COMMENT '開発者名',
   `password_hash` varchar(100) DEFAULT NULL COMMENT 'パスワードハッシュ',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_developer_PKC` PRIMARY KEY (`developer_cd`)
 ) COMMENT='開発者' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;