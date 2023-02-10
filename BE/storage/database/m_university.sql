DROP TABLE IF EXISTS `m_university`;

CREATE TABLE `m_university` (
   `university_cd` varchar(4) NOT NULL COMMENT '大学コード:出身大学コード＋学部識別コード',
   `university_name` varchar(50) DEFAULT NULL COMMENT '大学名',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_university_PKC` PRIMARY KEY (`university_cd`)
 ) COMMENT='大学マスタ:アルトマークのマスタ\r\nアルトマーク：出身校マスタ\r\nアルトマークでは出身校コードと学部識別コードは別カラム' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;