DROP TABLE IF EXISTS `m_municipality`;

CREATE TABLE `m_municipality` (
   `prefecture_cd` varchar(2) NOT NULL COMMENT '都道府県コード',
   `municipality_cd` varchar(3) NOT NULL COMMENT '市区町村コード',
   `municipality_name` varchar(100) NOT NULL COMMENT '市区町村名',
   `municipality_name_kana` varchar(100) DEFAULT NULL COMMENT '市区町村名ｶﾅ',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_municipality_PKC` PRIMARY KEY (`prefecture_cd`,`municipality_cd`)
 ) COMMENT='市区町村マスタ:アルトマークの市区町村マスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;