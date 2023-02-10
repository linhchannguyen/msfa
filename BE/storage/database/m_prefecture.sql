DROP TABLE IF EXISTS `m_prefecture`;

CREATE TABLE `m_prefecture` (
   `prefecture_cd` varchar(2) NOT NULL COMMENT '都道府県コード',
   `prefecture_name` varchar(50) NOT NULL COMMENT '都道府県名',
   `region_cd` varchar(3) NOT NULL COMMENT '地方コード',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_prefecture_PKC` PRIMARY KEY (`prefecture_cd`)
 ) COMMENT='都道府県マスタ:アルトマークの都道府県マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;