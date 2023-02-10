DROP TABLE IF EXISTS `m_infirmary_type`;

CREATE TABLE `m_infirmary_type` (
   `infirmary_type` varchar(1) NOT NULL COMMENT '病院種別',
   `infirmary_type_name` varchar(100) NOT NULL COMMENT '病院種別名',
   `created_by` varchar(15) NOT NULL COMMENT '登録者コード',
   `proxy_created_by` varchar(15) DEFAULT NULL COMMENT '登録者コード（代行ログイン者）',
   `created_at` datetime NOT NULL COMMENT '登録日時',
   `updated_by` varchar(15) NOT NULL COMMENT '更新者コード',
   `proxy_updated_by` varchar(15) DEFAULT NULL COMMENT '更新者コード（代行ログイン者コード）',
   `updated_at` datetime NOT NULL COMMENT '更新日時',
   constraint `m_infirmary_type_PKC` PRIMARY KEY (`infirmary_type`)
 ) COMMENT='病院種別マスタ:アルトマークのマスタ' ENGINE=InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci ;