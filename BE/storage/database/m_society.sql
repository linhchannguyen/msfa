DROP TABLE IF EXISTS `m_society`;

CREATE TABLE `m_society` (
   `society_cd` varchar(4) NOT NULL COMMENT '学会コード',
   `society_name` varchar(40) NOT NULL COMMENT '学会名',
   `society_short_name` varchar(30) NOT NULL COMMENT '学会略名',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_society_PKC` PRIMARY KEY (`society_cd`)
 ) COMMENT='学会マスタ:アルトマークのマスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;